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
            $productos = Productos::where('estado', 1)->get();
            return response()->json([
                "data" => $productos
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        }
    }

    public function newProducto(Request $request)
    {
        try {
            $request->validate([
                'nombre_producto' => 'required',
                'codigo_barras' => 'required|min:10',
                'categoria' => 'required',
                'precio_unitario' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'stock' => 'required|numeric|min:0',
                'estado' => 'required'
            ]);
            $producto = new Productos();
            $producto->nombre_producto = $request->nombre_producto;
            $producto->codigo_barras = $request->codigo_barras;
            $producto->categoria = $request->categoria;
            $producto->precio_unitario = $request->precio_unitario;
            $producto->precio_venta = $request->precio_venta;
            $producto->stock = $request->stock;
            $producto->estado = $request->estado;
            $producto->save();
            return response()->json([
                "message" => "Producto creado correctamente",
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
                "message" => "Error al crear el producto",
                "error" => $e->getMessage()
            ], 500);
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
                'id_producto' => 'required|min:10'
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
