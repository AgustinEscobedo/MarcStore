<?php

namespace App\Http\Controllers;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Productos;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
class ProductosController extends Controller
{

    public function getAllProductos()
    {
        try {
            // Obtener productos activos (estado = 1) y cargar la relación con el proveedor
            $productos = Productos::where('estado', 1)
                ->with('proveedor:id_proveedor,nombre_proveedor') // Cargar solo los campos necesarios del proveedor
                ->get();

            // Formatear la respuesta
            $productosFormateados = $productos->map(function ($producto) {
                return [
                    'id_producto' => $producto->id_producto,
                    'nombre_producto' => $producto->nombre_producto,
                    'codigo_barras' => $producto->codigo_barras,
                    'categoria' => $producto->categoria,
                    'precio_unitario' => $producto->precio_unitario,
                    'precio_venta' => $producto->precio_venta,
                    'stock' => $producto->stock,
                    'estado' => $producto->estado,
                    'proveedor' => [
                        'id_proveedor' => $producto->proveedor->id_proveedor,
                        'nombre_proveedor' => $producto->proveedor->nombre_proveedor
                    ]
                ];
            });

            return response()->json([
                "data" => $productosFormateados
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al obtener los productos",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function newProducto(Request $request)
    {
        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'codigo_barras' => 'required|min:10|unique:productos,codigo_barras',
            'categoria' => 'required|string|max:255',
            'precio_unitario' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'id_proveedor' => 'required|exists:proveedores,id_proveedor',
            'estado' => 'required|boolean'
        ]);

        try {
            $producto = Productos::create([
                'nombre_producto' => $request->nombre_producto,
                'codigo_barras' => $request->codigo_barras,
                'categoria' => $request->categoria,
                'precio_unitario' => $request->precio_unitario,
                'precio_venta' => $request->precio_venta,
                'stock' => $request->stock,
                'id_proveedor' => $request->id_proveedor,
                'estado' => $request->estado
            ]);

            // Respuesta exitosa
            return response()->json([
                "message" => "Producto creado correctamente",
                "data" => $producto
            ], Response::HTTP_CREATED); // Usar HTTP_CREATED (201) para creación exitosa

        } catch (ValidationException $e) {
            // Manejo de errores de validación
            return response()->json([
                "status" => Response::HTTP_UNPROCESSABLE_ENTITY, // 422
                "errors" => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (\Exception $e) {
            // Manejo de errores inesperados
            return response()->json([
                "status" => Response::HTTP_INTERNAL_SERVER_ERROR, // 500
                "message" => "Error al crear el producto",
                "error" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function updateProducto(Request $request)
    {
        try {
            $request->validate([
                'id_producto' => 'required|exists:productos,id_producto', // Solo el ID es obligatorio
                'nombre_producto' => 'nullable',  // Los demás campos son opcionales
                'codigo_barras' => 'nullable|min:10',
                'categoria' => 'nullable',
                'precio_unitario' => 'nullable|numeric|min:0',
                'precio_venta' => 'nullable|numeric|min:0',
                'id_proveedor' => 'nullable|exists:proveedores,id_proveedor',
                'stock' => 'nullable|numeric|min:0',
                'estado' => 'nullable'
            ]);

            // Buscar el producto por id_producto
            $producto = Productos::find($request->id_producto);

            if (!$producto) {
                return response()->json([
                    "message" => "Producto no encontrado"
                ], Response::HTTP_NOT_FOUND);
            }

            // Actualizar solo los campos que han sido proporcionados
            $producto->update($request->only([
                'nombre_producto',
                'codigo_barras',
                'categoria',
                'id_proveedor',
                'precio_unitario',
                'precio_venta',
                'stock',
                'estado'
            ]));

            return response()->json([
                "message" => "Producto actualizado correctamente",
                "data" => $producto
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al actualizar el producto",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function deleteProducto(Request $request)
    {
        try {
            $request->validate([
                'id_producto' => 'required'
            ]);
            $producto = Productos::where('id_producto', $request->id_producto)->first();
            if (!$producto) {
                return response()->json([
                    "message" => "Producto no encontrado"
                ], Response::HTTP_NOT_FOUND);
            }

            $producto->delete();
            return response()->json([
                "message" => "Producto eliminado correctamente",
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                "status" => 500,
                "message" => "Error al eliminar el producto",
            ], 500);
        }

    }

}
