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
