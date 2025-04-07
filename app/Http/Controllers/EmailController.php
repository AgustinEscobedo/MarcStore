<?php

namespace App\Http\Controllers;

use App\Mail\SendMailChucho;
use App\Mail\SendMailMe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'asunto' => 'sometimes|string',
        ]);

        $datosCorreo = [
            'email' => $request->email,
            'name' => $request->name,
            'asunto' => $request->asunto ?? 'Confirmación de Recepción',
        ];

        $miCorreo = 'agustinescobedovargas@gmail.com';


        try {
            Mail::to($miCorreo)->send(new SendMailChucho($datosCorreo));

            Mail::to($request->email)->send(new SendMailMe($datosCorreo));
            
            return response()->json([
                'success' => true,
                'message' => 'Correo enviado correctamente',
            ], 200);
        
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage(),
            ], 500);
        }

    }

    // public function sendEmail(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'name' => 'required|string|max:255',
    //         'asunto' => 'required|string',
    //     ]);

    //     $asuntoGenerado = $this->generarAsuntoChatGPT($request->asunto);

    //     $datosCorreo = [
    //         'email' => $request->email,
    //         'name' => $request->name,
    //         'asunto_original' => $request->asunto,
    //         'asunto_generado' => $asuntoGenerado,
    //     ];

    //     try {
    //         Mail::to($request->email)->send(new SendMailChucho($datosCorreo));
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Correo enviado correctamente',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error al enviar el correo: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }
    public function generarAsuntoChatGPT($asuntoOriginal)
    {
        if (empty(trim($asuntoOriginal))) {
            $asuntoOriginal = 'una consulta general sobre desarrollo';
        }

        $prompt = "Responde al siguiente asunto de un cliente de forma profesional y cordial, como si fuera un correo automático de confirmación de recepción. El texto debe ser breve, confirmar que se recibió el asunto, y prometer seguimiento pronto. Asunto original: \"$asuntoOriginal\"";

        $respuesta = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ]
                ],
                'temperature' => 0.7,
            ]);

        // Logueamos la respuesta completa para debug
        \Log::info('Respuesta OpenAI:', $respuesta->json());

        return $respuesta->json('choices.0.message.content') ?? 'Hemos recibido su mensaje y daremos seguimiento pronto.';
    }


}
