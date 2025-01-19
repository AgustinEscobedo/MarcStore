<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\venta_detalle;
use App\Models\Ventas;
use App\Models\Productos;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class VentasController extends Controller
{
    public function registrarVenta(Request $request)
    {
        try {
            // Validación de los campos
            $request->validate([
                'id_usuario' => 'required|integer',
                'total_venta' => 'required|numeric',
                'productos' => 'required|array',
                'productos.*.id_producto' => 'required|integer',
                'productos.*.cantidad' => 'required|integer',
                'productos.*.subtotal' => 'required|numeric',
            ]);

            // Creación de la venta principal
            $ventas = new Ventas();
            $ventas->id_usuario = $request->id_usuario;
            $ventas->fecha_venta = Carbon::now()->format('Y-m-d H:i:s');
            $ventas->total_venta = $request->total_venta;
            $ventas->save();

            // Obtención del ID generado de la venta
            $id_ventas = $ventas->id_ventas;
            $productos_items = $request->productos;

            // Iterar sobre los productos y crear los registros de venta_detalle
            foreach ($productos_items as $producto) {
                // Verificar si los datos del producto son correctos
                if (isset($producto['id_producto'], $producto['cantidad'], $producto['subtotal'])) {
                    // Obtener el producto desde la base de datos
                    $producto_db = Productos::find($producto['id_producto']);

                    // Verificar que el producto existe y si hay suficiente stock
                    if ($producto_db) {
                        if ($producto_db->stock < $producto['cantidad']) {
                            // Si no hay suficiente stock, devolver un error
                            return response()->json([
                                'status' => 400,
                                'message' => 'No hay suficiente stock para el producto: ' . $producto_db->nombre_producto
                            ], 400);
                        }

                        // Restar la cantidad del producto del stock solo si es suficiente
                        $producto_db->stock -= $producto['cantidad'];
                        $producto_db->save();

                        // Crear un registro en la tabla venta_detalle
                        venta_detalle::create([
                            'id_grupo_venta' => $id_ventas,  // ID de la venta
                            'id_producto' => $producto['id_producto'],
                            'cantidad' => $producto['cantidad'],
                            'precio_unitario' => $producto_db->precio_venta, // Usamos el precio del producto
                            'subtotal' => $producto['subtotal']
                        ]);
                    } else {
                        // Si el producto no existe
                        return response()->json([
                            'status' => 400,
                            'message' => 'El producto con ID ' . $producto['id_producto'] . ' no existe.'
                        ], 400);
                    }
                }
            }

            // Respuesta de éxito
            return response()->json([
                "status" => 200,
                "message" => "Venta registrada exitosamente",
                "id_ventas" => $id_ventas
            ]);
        } catch (ValidationException $e) {
            // Respuesta de error por validación
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Respuesta de error general
            return response()->json([
                "status" => 500,
                "message" => $e->getMessage()
            ], 500);
        }
    }


}
