<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Validation\ValidationException;
use Hash;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\ClosureUse;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
class AuthController extends Controller
{

    public function register(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }
        $request->validate([
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'rol' => 'required|in:1,2,3',
        ]);
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => $request->rol,
            ]);


            return response()->json([
                'status' => 201,
                'message' => 'Usuario registrado exitosamente',
                'data' => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error al registrar el usuario',
            ], 500);
        }

    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['email', 'required'],
            'password' => ['required']
        ]);

        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                if ($user->activo == 0) {
                    return response([
                        "message" => "Tu cuenta está suspendida, contacta con tu administrador"
                    ], Response::HTTP_FORBIDDEN);
                }

                $permissions = User::$rolesPermissions[$user->rol];

                $token = $user->createToken('token', $permissions, Carbon::now()->addDay())->plainTextToken;

                $cookie = cookie('cookie_token', $token, 1440);

                $responseUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol
                ];

                return response([
                    "token" => $token,
                    "userData" => $responseUser
                ], Response::HTTP_OK)->withCookie($cookie);
            } else {
                return response(["message" => "Credenciales incorrectas"], Response::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error al iniciar sesión',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Obtener el usuario autenticado y revocar su token
            $user = Auth::user();
            if ($user) {
                $user->tokens()->delete(); // Elimina todos los tokens del usuario
            }

            // Devolver respuesta eliminando la cookie
            return response([
                "message" => "Sesión cerrada correctamente para el usuario {$user->name}"
            ], Response::HTTP_OK)->withoutCookie('cookie_token');

        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error al cerrar sesión',
            ], 500);
        }
    }
    public function deleteUser(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }
        try {
            $request->validate([
                'id_usuario' => 'required'
            ]);
            $usuario = User::where('id', $request->id_usuario)->first();
            if (!$usuario) {
                return response()->json([
                    "message" => "Usuario no encontrado"
                ], Response::HTTP_NOT_FOUND);
            }

            $usuario->delete();
            return response()->json([
                "message" => "Usuario eliminado correctamente",
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al eliminar el usuario",
            ], 500);
        }

    }
    public function updateUser(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }

        $request->validate([
            'id' => 'required|exists:users,id', // Asegura que el usuario exista
            'name' => 'sometimes|min:3|max:100',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $request->id . ',id',
            'password' => 'sometimes|min:6', // La contraseña es opcional
            "activo" => 'sometimes|in:0,1',
            'rol' => 'sometimes|in:1,2,3'
        ]);

        try {
            $user = User::findOrFail($request->id);

            // Verificar si se está enviando una nueva contraseña
            if ($request->filled('password')) {
                // Encriptar la contraseña si está presente
                $user->password = Hash::make($request->password);
            }

            // Si no estamos actualizando la contraseña, eliminamos la clave de password
            // del array para que no sea sobrescrita sin encriptar
            $data = $request->except('password');

            // Actualizar el resto de los campos (excepto la contraseña)
            $user->update($data);

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'status' => 'ok'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error al actualizar usuario']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el usuario',
            ]);
        }
    }

    public function getAllUsers(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }
        try {
            // Obtener todos los usuarios
            $users = User::where('activo', 1)->get();

            // Verificar si hay usuarios en la base de datos
            if ($users->isEmpty()) {
                return response()->json([
                    "message" => "No se encontraron usuarios"
                ], Response::HTTP_NOT_FOUND);
            }

            // Retornar la lista de usuarios
            return response()->json([
                "message" => "Usuarios obtenidos correctamente",
                "users" => $users
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al obtener los usuarios",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    public function suspendUsers(Request $request)
    {
        if (!$request->user()->tokenCan('*')) {
            return response()->json(['message' => 'No tienes permisos para esta acción'], 403);
        }
        try {
            // Validar los datos recibidos
            $request->validate([
                'id_usuario' => 'required|exists:users,id', // Asegura que el usuario exista en la base de datos
                'activo' => 'required|boolean', // Asegura que activo sea 0 o 1
            ]);

            // Buscar el usuario
            $usuario = User::find($request->id_usuario);

            // Actualizar el estado del usuario
            $usuario->activo = $request->activo;
            $usuario->save();

            // Determinar el mensaje según el valor de "activo"
            $message = $request->activo == 1
                ? "Usuario activado correctamente"
                : "Usuario suspendido correctamente";

            return response()->json([
                "message" => $message,
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al actualizar el estado del usuario",
                "error" => $e->getMessage()
            ], 500);
        }
    }




























    // public function userProfile(Request $request)
    // {
    //     try {
    //         return response()->json([
    //             "message" => "UserProfile OK",
    //             "userData" => auth()->user()
    //         ], Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "message" => "Error en la solicitud"
    //         ]);
    //     }
    // }

    // public function logout()
    // {
    //     return response()->json([
    //         "message" => "logout OK"
    //     ]);
    // }
    // public function allUsers(Request $request)
    // {
    //     return response()->json([
    //         "message" => "allUsers OK"
    //     ]);
    // }
}
