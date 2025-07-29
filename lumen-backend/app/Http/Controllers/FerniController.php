<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FerniController extends Controller
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function old_responder(Request $request)
    {
        try {
            // El historial de la conversación debe venir en el cuerpo de la solicitud.
            // Se espera un array de objetos con 'role' ('user' o 'assistant') y 'text'.
            $historial = $request->input('history');

            // Validar que se recibió un historial válido.
            if (!$historial || !is_array($historial)) {
                return response()->json([
                    'error' => [
                        'code' => 400,
                        'message' => 'No se recibió un historial válido. Se espera un array de mensajes.'
                    ]
                ], 400);
            }

            // Convertimos el historial recibido del cliente al formato esperado por Gemini.
            // Gemini espera 'role:user' o 'role:model' y 'parts' con un array de objetos con 'text'.
            $contents = [];

            // La instrucción de sistema se inyecta como parte del primer mensaje del usuario
            // para modelos como gemini-2.0-flash que no tienen un 'systemInstruction' explícito.
            $systemInstructionText = 'Te llamas Ferni. Eres un bartender virtual amigable y experto en la preparación de todo tipo de bebidas, tanto alcohólicas como sin alcohol. Tu objetivo es ayudar a los usuarios a encontrar la bebida perfecta y darles las recetas detalladas.

**Directrices de conversación:**

1. **Bienvenida:** Siempre saluda con una bienvenida cálida al inicio de la conversación y ofrece tu ayuda.
2. **Comprensión:** Haz preguntas para entender mejor los gustos del usuario (ej. "¿dulce o seco?", "¿con o sin alcohol?", "¿qué tipo de licor te gusta?", "¿para qué ocasión?").
3. **Recetas:** Si el usuario te pide una bebida, ofrécele la receta detallada con ingredientes y pasos de preparación.
4. **Tono:** Mantén un tono conversacional, entusiasta y servicial.
5. **Clarificación:** Si una solicitud es ambigua, pide más detalles. Por ejemplo, si dicen "algo con ron", pregunta "¿un mojito, un daiquirí, una cuba libre?".
6. **Fuera de tema:** Si la conversación se desvía de las bebidas, redirígela amablemente explicando que tu especialidad son los tragos.
7. **Despedida:** Al finalizar una interacción, ofrece tu ayuda para futuras bebidas.

**Formato de respuesta para recetas:**
IMPORTANTE: SOLO cuando el usuario te pida EXPLÍCITAMENTE guardar una receta (usando palabras como "guardar", "guardar la receta", "guardar en mis creaciones", "agregar a favoritos", etc.), DEBES responder ÚNICAMENTE en formato JSON con la siguiente estructura EXACTA:
{
  "type": "recipe",
  "data": {
    "nombre": "Nombre del trago",
    "descripcion": "Descripción breve del trago",
    "ingredientes": ["Ingrediente 1", "Ingrediente 2", "Ingrediente 3"]
  }
}

Para TODAS las demás respuestas (incluyendo cuando te pidan recetas normalmente, saludos, preguntas, aclaraciones), responde normalmente en texto plano con explicaciones detalladas, pasos de preparación, etc.

NO uses el formato JSON para recetas normales, solo para cuando quieran guardar la receta.';

            foreach ($historial as $index => $mensaje) {
                // El rol para Gemini es 'user' o 'model' (para las respuestas del bot).
                // Mapeamos 'assistant' a 'model'.
                $role = ($mensaje['role'] ?? '') === 'assistant' ? 'model' : 'user';
                $text = $mensaje['text'] ?? '';

                if ($text !== '') {
                    // Si es el primer mensaje del usuario, le concatenamos la instrucción de sistema.
                    if ($index === 0 && $role === 'user') {
                        $text = $systemInstructionText . "\n\n" . $text;
                    }

                    $contents[] = [
                        'role' => $role,
                        'parts' => [
                            ['text' => $text]
                        ]
                    ];
                }
            }

            // Preparar el payload para la API de Gemini
            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.3,
                    'candidateCount' => 1
                ]
            ];

            // Hacer la llamada a la API de Gemini
            $response = $this->client->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
                [
                    'query' => ['key' => $this->apiKey],
                    'json' => $payload,
                    'verify' => false
                ]
            );

            $data = json_decode($response->getBody(), true);
            $respuesta = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No logré entender bien tu solicitud.';

            // Validar si la respuesta debería ser una receta
            $ultimoMensaje = $historial[count($historial) - 1]['text'] ?? '';
            $palabrasClaveGuardar = ['guardar', 'guardado', 'guardar la receta', 'guardar el trago', 'guardar esta receta', 'guardar esta bebida', 'guardar en mis creaciones', 'guardar en mi lista', 'agregar a mis recetas', 'agregar a mi lista'];
            $esSolicitudGuardar = false;

            foreach ($palabrasClaveGuardar as $palabra) {
                if (stripos($ultimoMensaje, $palabra) !== false) {
                    $esSolicitudGuardar = true;
                    break;
                }
            }

            // Si es solicitud de guardar pero no está en formato JSON, intentar reformatear
            if ($esSolicitudGuardar && !$this->esRespuestaJSON($respuesta)) {
                Log::warning('Ferni no respondió en formato JSON para guardar receta, intentando reformatear', [
                    'mensaje_usuario' => $ultimoMensaje,
                    'respuesta_original' => $respuesta
                ]);

                // Intentar extraer información de la respuesta y reformatear
                $respuesta = $this->reformatearReceta($respuesta);
            }

            // Log de la respuesta de Ferni
            Log::info('Respuesta de Ferni:', [
                'mensaje_usuario' => $ultimoMensaje,
                'respuesta_ferni' => $respuesta,
                'es_solicitud_guardar' => $esSolicitudGuardar,
                'es_json' => $this->esRespuestaJSON($respuesta)
            ]);

            // Devolver la respuesta
            return response()->json([
                'success' => true,
                'reply' => $respuesta
            ]);

        } catch (\Exception $e) {
            Log::error('Error en FerniController::old_responder: ' . $e->getMessage());
            return response()->json([
                'error' => [
                    'code' => 500,
                    'message' => 'Error interno del servidor.',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }


    private function generarRespuesta(array $mensajes): string
    {
        $systemInstructionText = 'Te llamas Ferni. Eres un bartender virtual amigable y experto en la preparación de todo tipo de bebidas, tanto alcohólicas como sin alcohol. Tu objetivo es ayudar a los usuarios a encontrar la bebida perfecta y darles las recetas detalladas.

**Directrices de conversación:**

1. **Bienvenida:** Siempre saluda con una bienvenida cálida al inicio de la conversación y ofrece tu ayuda.
2. **Comprensión:** Haz preguntas para entender mejor los gustos del usuario (ej. "¿dulce o seco?", "¿con o sin alcohol?", "¿qué tipo de licor te gusta?", "¿para qué ocasión?").
3. **Recetas:** Si el usuario te pide una bebida, ofrécele la receta detallada con ingredientes y pasos de preparación.
4. **Tono:** Mantén un tono conversacional, entusiasta y servicial.
5. **Clarificación:** Si una solicitud es ambigua, pide más detalles. Por ejemplo, si dicen "algo con ron", pregunta "¿un mojito, un daiquirí, una cuba libre?".
6. **Fuera de tema:** Si la conversación se desvía de las bebidas, redirígela amablemente explicando que tu especialidad son los tragos.
7. **Despedida:** Al finalizar una interacción, ofrece tu ayuda para futuras bebidas.

**Formato de respuesta para recetas:**
IMPORTANTE: SOLO cuando el usuario te pida EXPLÍCITAMENTE guardar una receta (usando palabras como "guardar", "guardar la receta", "guardar en mis creaciones", "agregar a favoritos", etc.), DEBES responder ÚNICAMENTE en formato JSON con la siguiente estructura EXACTA:
{
  "type": "recipe",
  "data": {
    "nombre": "Nombre del trago",
    "descripcion": "Descripción breve del trago",
    "ingredientes": ["Ingrediente 1", "Ingrediente 2", "Ingrediente 3"]
  }
}

Para TODAS las demás respuestas (incluyendo cuando te pidan recetas normalmente, saludos, preguntas, aclaraciones), responde normalmente en texto plano con explicaciones detalladas, pasos de preparación, etc.

NO uses el formato JSON para recetas normales, solo para cuando quieran guardar la receta.';

        $contents = [];

        foreach ($mensajes as $index => $mensaje) {
            $role = $mensaje['role'] === 'assistant' ? 'model' : 'user';
            $text = $mensaje['text'];

            if ($index === 0 && $role === 'user') {
                $text = $systemInstructionText . "\n\n" . $text;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $text]]
            ];
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.3,
                'candidateCount' => 1
            ]
        ];

        $response = $this->client->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
            [
                'query' => ['key' => $this->apiKey],
                'json' => $payload,
                'verify' => false
            ]
        );

        $data = json_decode($response->getBody(), true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No logré entender bien tu solicitud.';
    }

    public function handleChat(Request $request) {
        $userId = $request->input('user_id');
        $chatId = $request->input('chat_id'); // opcional
        $mensajes = $request->input('messages'); // array de mensajes nuevos

        if (!is_array($mensajes) || empty($mensajes)) {
            return response()->json(['error' => 'No se proporcionaron mensajes válidos'], 400);
        }

        try {
            // Verificar si el usuario es mayor de edad
            $isAdult = true;

            try {
                $usuario = User::find($userId);
                if ($usuario && $usuario->fecha_nacimiento) {
                    $edad = Carbon::parse($usuario->fecha_nacimiento)->age;
                    $isAdult = $edad >= 18;
                }
            } catch (\Exception $e) {
                Log::warning("No se pudo calcular edad del usuario $userId: " . $e->getMessage());
            }

            // Crear nuevo chat si no existe
            if (!$chatId) {
                $chat = Chat::create([
                    'user_id' => $userId,
                    'title' => 'Nuevo Chat'
                ]);
                $chatId = $chat->id;

                $primerMensaje = $mensajes[0]['text'] ?? '';

                Message::create([
                    'chat_id' => $chatId,
                    'sender' => 'user',
                    'content' => $primerMensaje
                ]);

                // Título generado
                $tituloGenerado = $this->generarRespuesta([
                    ['role' => 'user', 'text' => "Genera un título breve para este chat con base en el mensaje [SOLO RESPONDE CON UN TÍTULO EN TEXTO PLANO]: $primerMensaje"]
                ]);

                $chat->update(['title' => $tituloGenerado]);

            } else {
                // Chat ya existe
                $chat = Chat::findOrFail($chatId);

                // Insertar nuevos mensajes del usuario (normalmente solo uno)
                foreach ($mensajes as $msg) {
                    if ($msg['role'] === 'user') {
                        Message::create([
                            'chat_id' => $chatId,
                            'sender' => 'user',
                            'content' => $msg['text']
                        ]);
                    }
                }
            }

            // Obtener historial completo desde la base de datos
            $historial = Message::where('chat_id', $chatId)
                ->orderBy('created_at')
                ->get()
                ->map(fn($msg) => [
                    'role' => $msg->sender === 'bot' ? 'assistant' : 'user',
                    'text' => $msg->content
                ])
                ->toArray();

            $mensajeEdad = $isAdult
                ? "Importante: El usuario es mayor de edad, puedes sugerirle tragos con alcohol."
                : "Importante: El usuario es menor de edad, NO le sugieras tragos con alcohol.";

            array_unshift($historial, [
                'role' => 'user',
                'text' => $mensajeEdad
            ]);

            // Obtener respuesta del modelo
            $respuesta = $this->generarRespuesta($historial);

            // Guardar la respuesta en la base de datos
            Message::create([
                'chat_id' => $chatId,
                'sender' => 'bot',
                'content' => $respuesta
            ]);

            return response()->json([
                'chat_id' => $chatId,
                'reply' => $respuesta
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en el manejo del chat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getChats($userId)
    {
        $chats = Chat::where('user_id', $userId)->orderByDesc('created_at')->get();
        return response()->json($chats);
    }

    public function getMessages($chatId)
    {
        $mensajes = Message::where('chat_id', $chatId)->orderBy('created_at')->get();
        return response()->json($mensajes);
    }

    private function esRespuestaJSON($respuesta): bool
    {
        $trimmed = trim($respuesta);
        return (substr($trimmed, 0, 1) === '{' && substr($trimmed, -1) === '}') ||
               (substr($trimmed, 0, 1) === '[' && substr($trimmed, -1) === ']');
    }

    private function reformatearReceta($respuesta): string
    {
        // Extraer nombre del trago (buscar después de "**" o en títulos)
        preg_match('/\*\*(.*?)\*\*/', $respuesta, $matches);
        $nombre = $matches[1] ?? 'Trago';

        // Extraer ingredientes (buscar después de "**Ingredientes:**")
        preg_match('/\*\*Ingredientes:\*\*(.*?)(?:\*\*|$)/s', $respuesta, $matches);
        $ingredientesTexto = $matches[1] ?? '';

        // Convertir ingredientes a array
        $ingredientes = [];
        $lineas = explode("\n", $ingredientesTexto);
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (!empty($linea) && !preg_match('/^\*\*.*\*\*$/', $linea)) {
                // Limpiar la línea de caracteres especiales
                $linea = preg_replace('/^[\-\*•\s]+/', '', $linea);
                $linea = trim($linea);
                if (!empty($linea)) {
                    $ingredientes[] = $linea;
                }
            }
        }

        // Si no se encontraron ingredientes, crear uno genérico
        if (empty($ingredientes)) {
            $ingredientes = ["Ingredientes del $nombre"];
        }

        // Extraer pasos de preparación
        preg_match('/\*\*Preparación:\*\*(.*?)(?:\*\*|$)/s', $respuesta, $matches);
        $pasosTexto = $matches[1] ?? '';

        // Extraer tips o consejos
        preg_match('/\*\*Tips?.*?:\*\*(.*?)(?:\*\*|$)/s', $respuesta, $matches);
        $tipsTexto = $matches[1] ?? '';

        // Crear descripción detallada
        $descripcion = "Receta de $nombre preparada por Ferni. ";

        // Agregar información sobre el tipo de bebida
        if (stripos($respuesta, 'cóctel') !== false) {
            $descripcion .= "Es un cóctel ";
        } elseif (stripos($respuesta, 'trago') !== false) {
            $descripcion .= "Es un trago ";
        } else {
            $descripcion .= "Es una bebida ";
        }

        // Agregar características
        $caracteristicas = [];
        if (stripos($respuesta, 'refrescante') !== false) $caracteristicas[] = 'refrescante';
        if (stripos($respuesta, 'dulce') !== false) $caracteristicas[] = 'dulce';
        if (stripos($respuesta, 'seco') !== false) $caracteristicas[] = 'seco';
        if (stripos($respuesta, 'clásico') !== false) $caracteristicas[] = 'clásico';
        if (stripos($respuesta, 'tradicional') !== false) $caracteristicas[] = 'tradicional';
        if (stripos($respuesta, 'popular') !== false) $caracteristicas[] = 'popular';

        if (!empty($caracteristicas)) {
            $descripcion .= implode(', ', $caracteristicas) . ". ";
        }

        // Agregar pasos de preparación específicos
        if (!empty($pasosTexto)) {
            // Limpiar y formatear los pasos
            $pasos = [];
            $lineas = explode("\n", $pasosTexto);
            foreach ($lineas as $linea) {
                $linea = trim($linea);
                if (!empty($linea) && !preg_match('/^\*\*.*\*\*$/', $linea)) {
                    // Limpiar números y caracteres especiales al inicio
                    $linea = preg_replace('/^[\d\.\-\*•\s]+/', '', $linea);
                    $linea = trim($linea);
                    if (!empty($linea)) {
                        $pasos[] = $linea;
                    }
                }
            }

            if (!empty($pasos)) {
                $descripcion .= "Pasos: " . implode('. ', $pasos) . ". ";
            }
        }

        // Agregar tips si existen
        if (!empty($tipsTexto)) {
            // Limpiar y formatear los tips
            $tips = [];
            $lineas = explode("\n", $tipsTexto);
            foreach ($lineas as $linea) {
                $linea = trim($linea);
                if (!empty($linea) && !preg_match('/^\*\*.*\*\*$/', $linea)) {
                    $linea = preg_replace('/^[\-\*•\s]+/', '', $linea);
                    $linea = trim($linea);
                    if (!empty($linea)) {
                        $tips[] = $linea;
                    }
                }
            }

            if (!empty($tips)) {
                $descripcion .= "Consejos: " . implode('. ', $tips) . ".";
            }
        }

        // Limpiar la descripción
        $descripcion = trim($descripcion);

        return json_encode([
            'type' => 'recipe',
            'data' => [
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'ingredientes' => $ingredientes
            ]
        ]);
    }
}
