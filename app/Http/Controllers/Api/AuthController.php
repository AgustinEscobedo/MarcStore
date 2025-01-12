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
                'password' => 'required|confirmed|min:6',
                'rol' => 'required|in:1,2,3',
                'foto_perfil' => 'nullable|image|mimes:png,jpg,jpeg|max:10240'
            ]);
            try {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->rol = $request->rol;
                if ($request->hasFile('foto_perfil')) {
                    $user->foto_perfil = file_get_contents($request->file('foto_perfil')->getRealPath());
                } else {
                    $defaultImagePath = public_path('img/perfil.png');
                    if (file_exists($defaultImagePath)) {
                        $user->foto_perfil = file_get_contents($defaultImagePath);
                    } else {
                        return response()->json(['error' => 'Imagen por defecto no encontrada.'], 500);
                    }
                }
                $user->save();
                // $base64Image = base64_encode($user->foto_perfil);
                // $responseUser = [
                //     'id' => $user->id,
                //     'name' => $user->name,
                //     'email' => $user->email,
                //     'rol' => $user->rol,
                //     'foto_perfil' => 'data:image/png;base64,' . $base64Image,
                // ];
                try {
                    return response()->json([
                        'message' => 'Usuario creado exitosamente'
                    ], Response::HTTP_CREATED);
                } catch (\Throwable $th) {
                    return response()->json([
                        "Error_imagen" => $th
                    ]);
                }
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
                $base64Image = base64_encode($user->foto_perfil);
                $responseUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'foto_perfil' => 'data:image/png;base64,' . $base64Image,
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
                'id' => 'required'
            ]);
            $usuario = User::where('id', $request->id)->first();
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
            $request->validate([
                'id' => 'required',
                'name' => 'required|min:3|max:100',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'rol' => 'required|in:1,2,3',
            ]);
            // DEVOVLER STATUS EN 401, o como error
            $user = User::where('id', $request->id)->first();

            if (!$user) {
                return response()->json([
                    "message" => "Usuario no encontrado"
                ], Response::HTTP_NOT_FOUND);
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            // $image = $request->file('foto_perfil');
            // $imageData = file_get_contents($image->getRealPath());
            // $user->foto_perfil = $imageData;
            $user->rol = $request->rol;

            $user->update();

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
