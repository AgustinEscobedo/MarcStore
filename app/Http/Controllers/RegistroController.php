<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Registro;
use Auth;

class RegistroController extends Controller
{
    public function registrarEntradaSalida(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'fecha_hora' => 'sometimes|date'
        ]);

        try {
            $user = Auth::user();

            // Obtener la fecha solo (sin hora) para comparar por día
            $fecha = $request->fecha_hora ? \Carbon\Carbon::parse($request->fecha_hora)->toDateString() : now()->toDateString();

            // Validar si ya existe un registro de tipo 'entrada' o 'salida' para ese día
            $registroExistente = Registro::where('id_usuario', $user->id)
                ->whereDate('fecha_hora', $fecha)  // Filtra solo por la fecha, sin importar la hora
                ->where('tipo', $request->tipo)    // Asegura que sea del tipo 'entrada' o 'salida'
                ->exists();

            if ($registroExistente) {
                return response()->json([
                    'status' => Response::HTTP_CONFLICT, // 409
                    'message' => "Ya existe un registro de tipo {$request->tipo} para el día {$fecha}."
                ], Response::HTTP_CONFLICT);
            }

            // Crear el nuevo registro si no existe
            $registro = Registro::create([
                'id_usuario' => $user->id,
                'tipo' => $request->tipo,
                'fecha_hora' => $request->fecha_hora ?? now()
            ]);

            return response()->json([
                'message' => 'Registro creado exitosamente',
                'data' => $registro
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY, // 422
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR, // 500
                'message' => 'Error al registrar la entrada/salida',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function obtenerRegistrosUsuario(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:users,id'
        ]);
        try {

            $registros = Registro::where('id_usuario', $request->id_usuario)
                ->orderBy('fecha_hora', 'desc')
                ->get();

            // Respuesta exitosa
            return response()->json([
                'message' => 'Registros obtenidos exitosamente',
                'data' => $registros
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            // Manejo de errores inesperados
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR, // 500
                'message' => 'Error al obtener los registros',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function obtenerTodosLosRegistros(Request $request)
    {

        $request->validate([
            'fecha_inicio' => 'nullable|date',  // Validar si se proporciona una fecha válida para inicio
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',     // Validar si se proporciona una fecha válida para fin
        ]);

        try {
            // Obtener las fechas de inicio y fin si están presentes en la solicitud
            $fecha_inicio = $request->input('fecha_inicio');
            $fecha_fin = $request->input('fecha_fin');

            // Construcción de la consulta
            $query = Registro::join('users', 'registros.id_usuario', '=', 'users.id')
                ->select('registros.id_registro', 'registros.tipo', 'registros.fecha_hora', 'users.id as id_usuario', 'users.name as nombre_usuario')
                ->orderByDesc('registros.fecha_hora');

            // Agregar condiciones de fecha si se proporcionan
            if ($fecha_inicio) {
                $query->where('registros.fecha_hora', '>=', $fecha_inicio);
            }

            if ($fecha_fin) {
                $query->where('registros.fecha_hora', '<=', $fecha_fin);
            }

            // Ejecutar la consulta
            $registros = $query->get();

            // Respuesta exitosa
            return response()->json([
                'message' => 'Registros obtenidos exitosamente',
                'data' => $registros
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error al obtener los registros',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function actualizarHoraRegistro(Request $request)
    {
        // Validar la entrada
        $request->validate([
            'id_registro' => 'required|exists:registros,id_registro',
            'fecha_hora' => 'required|date'
        ]);

        try {
            // Buscar el registro por id
            $registro = Registro::find($request->id_registro);

            // Verificar si el registro existe
            if (!$registro) {
                return response()->json([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'Registro no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }

            // Actualizar la hora del registro
            $registro->fecha_hora = $request->fecha_hora;
            $registro->save();

            return response()->json([
                'message' => 'Hora actualizada exitosamente',
                'data' => $registro
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error al actualizar la hora',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function obtenerRegistrosPorFechaUsuario(Request $request)
    {
        // Validación de parámetros
        $request->validate([
            'id_usuario' => 'required|exists:users,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        try {
            // Recuperamos los registros para el usuario en el rango de fechas especificado
            $registros = Registro::join('users', 'registros.id_usuario', '=', 'users.id')
                ->select('registros.id_registro', 'registros.tipo', 'registros.fecha_hora', 'users.id as id_usuario', 'users.name as nombre_usuario')
                ->where('registros.id_usuario', $request->id_usuario)
                ->where('registros.fecha_hora', '>=', $request->fecha_inicio)  // Fecha de inicio
                ->where('registros.fecha_hora', '<=', $request->fecha_fin)  // Fecha de fin
                ->orderByDesc('registros.fecha_hora')  // Ordenar por fecha descendente
                ->get();

            return response()->json([
                'message' => 'Registros obtenidos exitosamente',
                'data' => $registros
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Error al obtener los registros',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
