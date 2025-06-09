<?php

namespace App\Http\Controllers;

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
        
        // VER DE USAR env('GEMINI_API_KEY')
        $this->apiKey = 'AIzaSyDiQgpkRXG2VwjQJPW1Lo8RqERX8gd1Stc';
    }

    /**
     * Responde a los mensajes del usuario utilizando la API de Gemini.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function responder(Request $request)
    {
        try {
            // El historial de la conversación debe venir en el cuerpo de la solicitud.
            // Se espera un array de objetos con 'role' ('user' o 'assistant') y 'text'.
            $historial = $request->input('history');

            // Validar que se recibió un historial válido.
            if (!$historial || !is_array($historial)) {
                return response()->json([
                    'error' => ['code' => 400, 'message' => 'No se recibió un historial válido. Se espera un array de mensajes.']
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
                            [
                                'text' => $text
                            ]
                        ]
                    ];
                }
            }

            $payload = [
                'contents' => $contents, 
                'generationConfig' => [
                    'temperature' => 0.7,
                    'candidateCount' => 1
                ]
            ];

            // Realizamos la llamada a la API de Gemini.
            $response = $this->client->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent',
                [
                    'query' => ['key' => $this->apiKey],
                    'json' => $payload,
                    'verify' => false // Deshabilitar la verificación SSL si es necesario (no recomendado en producción).
                ]
            );

            // Decodificamos la respuesta JSON de Gemini.
            $data = json_decode($response->getBody(), true);

            // Verificamos si hay candidatos y contenido de texto en la respuesta.
            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Disculpa, no logré entender bien tu solicitud. ¿Podrías explicármela de otra manera?';

            // Devolvemos la respuesta del bot al cliente.
            return response()->json(['reply' => $reply], 200);

        } catch (\Exception $e) {
            // Registramos cualquier error que ocurra durante el proceso.
            Log::error('Error en FerniController: ' . $e->getMessage() . ' en la línea ' . $e->getLine());

            // Devolvemos una respuesta de error más detallada para depuración (opcional, quitar en prod).
            return response()->json([
                'error' => [
                    'code' => 5001,
                    'message' => 'Error al comunicarse con Ferni.',
                    'details' => $e->getMessage() // Agrega el mensaje de error de la excepción para depuración
                ]
            ], 500);
        }
    }
}