<?php

namespace App\Http\Controllers;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::all();
        return response()->json($servicios);
    }
    public function store(Request $request)
    {
        $request->validate([
            'servicio' => 'required|string',
            'descripcion' => 'required|string',
            'caracteristicas' => 'required|array',
            'caracteristicas.*' => 'string', 
            'precio' => 'required|numeric',
        ]);

        try {
            $servicio = Servicio::create([
                'servicio' => $request->servicio,
                'descripcion' => $request->descripcion,
                'caracteristicas' => json_encode($request->caracteristicas),
                'precio' => $request->precio,
            ]);

            return response()->json([
                'message' => 'Servicio creado correctamente',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el servicio',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:servicios,id',
        ]);

        $servicio = Servicio::find($request->id);

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        try {
            $servicio->delete();
            return response()->json(['message' => 'Servicio eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el servicio', 'error' => $e->getMessage()], 500);
        }
    }


}
