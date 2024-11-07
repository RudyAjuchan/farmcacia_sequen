<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class contactoController extends Controller
{
    public function sendContactEmail(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'mensaje' => 'required|string',
        ]);

        Mail::send([], [], function ($message) use ($data) {
            $message->to('farmaciasequen@gmail.com')
                ->subject('Nuevo mensaje de contacto')
                ->html("
                    <p>Un usuario ha enviado un correo desde la página, los datos son los siguientes</p>
                    <p><b>Nombre:</b> {$data['nombre']}</p>
                    <p><b>Email:</b> {$data['email']}</p>
                    <p><b>Mensaje:</b></p>
                    <p>{$data['mensaje']}</p>
                ");
        });

        return response()->json(['message' => 'Correo enviado exitosamente.']);
    }
}
