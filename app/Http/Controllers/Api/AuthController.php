<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Validation\ValidationException;
use Hash;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\ClosureUse;
use Symfony\Component\HttpFoundation\Response;
class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|min:3|max:100',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'rol' => 'required|in:1,2,3',
            ]);
            try {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->rol = $request->rol;
                $user->save();
            } catch (\Throwable $th) {
                return response()->json([
                    "status" => $th
                ]);
            }
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Throwable $th) {

            return response()->json([
                "error" => "Something went wrong",
                "details" => $th->getMessage()
            ], 500);
        }

    }
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['email', 'required'],
                'password' => ['required']
            ]);
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('token')->plainTextToken;
                $cookie = cookie('cookie_token', $token, 60 * 24);
                $responseUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol
                ];
                return response([
                    "token" => $token,
                    "userData" => $responseUser
                ], Response::HTTP_OK)->withoutCookie($cookie);
            } else {
                return response(["message" => "Credenciales incorrectas"], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Throwable $th) {

            return response()->json([
                "error" => "Something went wrong",
                "details" => $th->getMessage()
            ], 500);
        }
    }

    public function deleteUser(Request $request)
    {
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
        try {
            // Validar que el ID del usuario sea obligatorio y exista
            $request->validate([
                'id_usuario' => 'required|exists:users,id', // Asegura que el usuario exista
                'name' => 'nullable|min:3|max:100',
                'email' => 'nullable|email',
                'password' => 'nullable|min:6', // La contraseña es opcional
                'rol' => 'nullable|in:1,2,3'
            ]);

            // Buscar el usuario por ID
            $user = User::find($request->id_usuario);

            if (!$user) {
                return response()->json([
                    "message" => "Usuario no encontrado"
                ], Response::HTTP_NOT_FOUND);
            }

            // Actualizar los campos que están presentes en la solicitud
            if ($request->filled('name')) {
                $user->name = $request->name;
            }
            if ($request->filled('email')) {
                $user->email = $request->email;
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            if ($request->filled('rol')) {
                $user->rol = $request->rol;
            }
            // Guardar los cambios
            $user->save();

            return response()->json([
                "message" => "Usuario actualizado correctamente",
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al actualizar el usuario",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    public function getAllUsers()
    {
        try {
            // Obtener todos los usuarios
            $users = User::all();

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
