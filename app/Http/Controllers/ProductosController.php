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
            $productos = Productos::all();
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
                'nombre_producto' => 'required',
                'codigo_barras' => 'required|min:10',
                'categoria' => 'required',
                'precio_unitario' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'stock' => 'required|numeric|min:0',
                'estado' => 'required'
            ]);
            $producto = Productos::where('codigo_barras', $request->codigo_barras)->first();
            if (!$producto) {
                return response()->json([
                    "message" => "Producto no encontrado"
                ], Response::HTTP_NOT_FOUND);
            }

            $producto->update($request->all());

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
                'codigo_barras' => 'required|min:10'
            ]);
            $producto = Productos::where('codigo_barras', $request->codigo_barras)->first();
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
