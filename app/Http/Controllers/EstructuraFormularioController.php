<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formulario;
use App\Services\EstructuraFormularioService;

class EstructuraFormularioController extends Controller
{
    /**
     * Guardar la estructura completa de un formulario
     */
    public function guardar(
        Request $request,
        Formulario $formulario,
        EstructuraFormularioService $service
    ) {
        // 1️⃣ Validar que venga la estructura y sea un array
        $data = $request->validate([
            'estructura' => 'required|array',
        ]);

        // 2️⃣ Guardar estructura usando el service
        try {
            $service->guardarEstructura($formulario, $data['estructura']);

            // 3️⃣ Responder con éxito
            return response()->json([
                'ok' => true,
                'mensaje' => 'Estructura guardada correctamente'
            ]);
        } catch (\Exception $e) {
            // 4️⃣ Capturar errores y responder con detalle
            return response()->json([
                'ok' => false,
                'mensaje' => 'Error al guardar la estructura: ' . $e->getMessage()
            ], 500);
        }
    }
}
