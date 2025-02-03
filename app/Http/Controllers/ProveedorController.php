<?php

namespace App\Http\Controllers;
use App\Models\proveedores;
use Illuminate\Http\Request;
use Exception;

class ProveedorController extends Controller
{
    public function newProveedor(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }
        $request->validate([
            'nombre_proveedor' => 'required|string|max:255',
            'contacto' => 'string|max:255',
            'telefono' => 'string|max:15',
            'email' => 'required|email|max:255|unique:proveedores,email',
        ]);

        try {
            proveedores::create($request->all());
            return response()->json([
                'message' => 'Proveedor creado correctamente',
                'status' => 'ok'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error al crear proveedor']);
        }
    }

    public function deleteProveedor(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id_proveedor'
        ]);

        try {
            proveedores::destroy($request->id_proveedor);
            return response()->json([
                'message' => 'Proveedor eliminado correctamente',
                'status' => 'ok'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error al eliminar proveedor']);
        }
    }

    public function updateProveedor(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }
        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'nombre_proveedor' => 'sometimes|string|max:255',
            'contacto' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:15',
            'email' => 'sometimes|email|max:255|unique:proveedores,email,' . $request->id_proveedor . ',id_proveedor',
            "estado" => "sometimes|in:0,1"
        ]);

        try {
            $proveedor = proveedores::findOrFail($request->id_proveedor);
            $proveedor->update($request->all());
            return response()->json([
                'message' => 'Proveedor actualizado correctamente',
                'status' => 'ok'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error al actualizar proveedor']);
        }
    }

    public function getAllProveedores(Request $request)
    {
        // if (!$request->user()->tokenCan('*')) {
        //     return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        // }
        try {
            $proveedores = proveedores::all();
            return response()->json(['status' => 'ok', 'data' => $proveedores]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error al obtener proveedores']);
        }
    }

    public function suspendProveedores(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }

        $request->validate([
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'estado' => 'required|in:0,1' // Asegura que el valor de 'estado' sea 0 o 1
        ]);

        try {
            // Buscar el proveedor por su ID
            $proveedor = proveedores::findOrFail($request->id_proveedor);

            $proveedor->update(['estado' => $request->estado]);

            if ($request->estado == 0) {
                return response()->json([
                    'message' => 'Proveedor suspendido correctamente',
                    'status' => 'ok'
                ]);
            } else {
                return response()->json([
                    'message' => 'Proveedor reactivado correctamente',
                    'status' => 'ok'
                ]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error al suspender o reactivar proveedor']);
        }
    }

}
