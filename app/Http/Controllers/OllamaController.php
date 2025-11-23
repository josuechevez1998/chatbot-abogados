<?php

namespace App\Http\Controllers;

use App\Models\User;
use Cloudstudio\Ollama\Facades\Ollama;
use Illuminate\Http\Request;

class OllamaController extends Controller
{
    public function index(Request $request)
    {
        $response = Ollama::agent('Asistente de atencion al cliente')
            ->prompt($request->msg)
            ->options([
                'temperature' => 0.8,
                'top_p' => 0.9,
                'max_tokens' => 500
            ])
            ->ask();

        return $response['response'];
    }

    public function users(Request $request)
    {
        $messages = [
            [
                "role" => "system",
                "content" => "Eres asistente de JGA Solutions. Si llamas a una herramienta, después de recibir su resultado, debes responder exclusivamente un JSON con el siguiente formato exacto: {\"response\": string, \"usuarios\": array}. No agregues texto fuera del JSON. No expliques. No saludes. Solo responde el JSON puro."
            ],
            [
                'role' => 'user',
                'content' => $request->msg
            ]
        ];

        // 1) PRIMERA LLAMADA
        $response = Ollama::model('llama3.1')
            ->tools([
                [
                    "type" => "function",
                    "function" => [
                        "name" => "getUsers",
                        "description" => "Devuelve todos los usuarios del sistema",
                        "parameters" => [
                            "type" => "object",
                            "properties" => (object)[],
                        ],
                    ],
                ]
            ])
            ->chat($messages);

        $toolCalls = $response['message']['tool_calls'] ?? [];

        // 2) EJECUCIÓN DE LA TOOL
        if (!empty($toolCalls)) {

            $call = $toolCalls[0];
            $functionName = $call['function']['name'];
            $callId = $call['id'];

            if ($functionName === 'getUsers') {

                $toolResult = [
                    "usuarios" => $this->getUsers()
                ];

                // Pasamos el resultado de la tool a la IA
                $messages[] = [
                    "role" => "tool",
                    "tool_call_id" => $callId,
                    "content" => json_encode($toolResult)
                ];

                // 3) SEGUNDA LLAMADA
                $final = Ollama::model('llama3.1')->chat($messages);

                // El modelo devuelve JSON como string dentro del content
                $content = $final['message']['content'] ?? '{}';

                // Convertir string a JSON real
                $json = json_decode($content, true);

                return response()->json($json);
            }
        }

        return $response;
    }

    protected function getUsers()
    {
        return User::all();
    }
}
