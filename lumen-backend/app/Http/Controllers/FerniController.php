<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
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
7. **Despedida:** Al finalizar una interacción, ofrece tu ayuda para futuras bebidas.';

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

            // Aquí puedes continuar con la llamada a Gemini o lo que desees hacer con $contents

        } catch (\Exception $e) {
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
        $systemInstructionText = 'Te llamas Ferni. Eres un bartender virtual amigable y experto en la preparación de todo tipo de bebidas...'; // Puedes poner el texto completo aquí

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
                'temperature' => 0.7,
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

    public function newChat(Request $request)
    {
        $userId = $request->input('user_id');
        $mensaje = $request->input('message');

        $chat = Chat::create(['user_id' => $userId]);

        Message::create([
            'chat_id' => $chat->id,
            'sender' => 'user',
            'content' => $mensaje
        ]);

        $respuesta = $this->generarRespuesta([
            ['role' => 'user', 'text' => $mensaje]
        ]);

        Message::create([
            'chat_id' => $chat->id,
            'sender' => 'bot',
            'content' => $respuesta
        ]);

        return response()->json(['chat_id' => $chat->id, 'reply' => $respuesta]);
    }

    public function sendMessage(Request $request)
    {
        $chatId = $request->input('chat_id');
        $mensaje = $request->input('message');

        $chat = Chat::findOrFail($chatId);
        Message::create([
            'chat_id' => $chat->id,
            'sender' => 'user',
            'content' => $mensaje
        ]);

        // Obtener historial
        $historial = $chat->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($msg) => ['role' => $msg->sender === 'bot' ? 'assistant' : 'user', 'text' => $msg->content])
            ->toArray();

        $respuesta = $this->generarRespuesta($historial);

        Message::create([
            'chat_id' => $chat->id,
            'sender' => 'bot',
            'content' => $respuesta
        ]);

        return response()->json(['reply' => $respuesta]);
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
}
