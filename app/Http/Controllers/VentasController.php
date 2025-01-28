<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\venta_detalle;
use App\Models\Ventas;
use App\Models\User;
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
                'productos.*.cantidad' => 'required|integer'
            ]);

            $productos_items = $request->productos;

            // Validar si hay suficiente stock para todos los productos antes de registrar la venta
            foreach ($productos_items as $producto) {
                $producto_db = Productos::find($producto['id_producto']);

                if (!$producto_db || $producto_db->stock < $producto['cantidad']) {
                    // Si no existe el producto o no hay suficiente stock, devolver error
                    return response()->json([
                        'status' => 400,
                        'message' => 'No hay suficiente stock para el producto: ' . ($producto_db->nombre_producto ?? 'ID ' . $producto['id_producto'])
                    ], 400);
                }
            }

            // Si todos los productos tienen suficiente stock, proceder a registrar la venta
            $ventas = new Ventas();
            $ventas->id_usuario = $request->id_usuario;
            $ventas->fecha_venta = Carbon::now()->format('Y-m-d H:i:s');
            $ventas->total_venta = $request->total_venta;
            $ventas->save();

            // Obtención del ID generado de la venta
            $id_ventas = $ventas->id_ventas;

            // Registrar los detalles de la venta y descontar el stock
            foreach ($productos_items as $producto) {
                $producto_db = Productos::find($producto['id_producto']);

                // Restar la cantidad del stock
                $producto_db->stock -= $producto['cantidad'];
                $producto_db->save();

                // Calcular el subtotal
                $subtotal = $producto['cantidad'] * $producto_db->precio_venta;

                // Crear un registro en la tabla venta_detalle
                venta_detalle::create([
                    'id_grupo_venta' => $id_ventas,  // ID de la venta
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto_db->precio_venta, // Usamos el precio del producto
                    'subtotal' => $subtotal
                ]);
            }

            // Respuesta de éxito
            return response()->json([
                "status" => 200,
                "message" => "Venta registrada exitosamente",
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
    public function editarVenta(Request $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'id_venta' => 'required|integer',  // Validar que id_venta se envíe en el cuerpo de la solicitud
                'productos' => 'required|array',
                'productos.*.id_producto' => 'required|integer',
                'productos.*.cantidad' => 'required|integer|min:0'
            ]);

            // Obtener el id_venta del request
            $id_venta = $request->id_venta;

            // Buscar la venta por el ID
            $venta = Ventas::find($id_venta);

            // Validar que la venta exista
            if (!$venta) {
                return response()->json([
                    "status" => 404,
                    "message" => "La venta con ID $id_venta no existe."
                ], 404);
            }

            // Productos que se desean actualizar
            $productos_actualizados = $request->productos;

            // Iterar sobre los productos enviados para actualización
            foreach ($productos_actualizados as $producto_actualizado) {
                // Buscar el detalle de la venta para el producto
                $detalle = venta_detalle::where('id_grupo_venta', $id_venta)
                    ->where('id_producto', $producto_actualizado['id_producto'])
                    ->first();

                // Validar que el producto esté en los detalles de la venta
                if (!$detalle) {
                    return response()->json([
                        "status" => 404,
                        "message" => "El producto con ID " . $producto_actualizado['id_producto'] . " no existe en la venta."
                    ], 404);
                }

                // Buscar el producto en la base de datos
                $producto_db = Productos::find($producto_actualizado['id_producto']);

                // Revertir el stock del producto antes de la actualización
                $producto_db->stock += $detalle->cantidad;

                // Validar si hay suficiente stock antes de actualizar
                if ($producto_actualizado['cantidad'] > $producto_db->stock) {
                    return response()->json([
                        "status" => 400,
                        "message" => "No hay suficiente stock para el producto."
                    ], 400);
                }

                // Calcular el nuevo subtotal basado en la nueva cantidad
                $cantidad_nueva = $producto_actualizado['cantidad'];
                $subtotal_nuevo = $cantidad_nueva * $producto_db->precio_venta;

                // Actualizar el stock con la nueva cantidad
                $producto_db->stock -= $cantidad_nueva;
                $producto_db->save();

                // Actualizar el detalle de la venta
                $detalle->cantidad = $cantidad_nueva;
                $detalle->subtotal = $subtotal_nuevo;
                $detalle->save();
            }

            // Recalcular el total de la venta
            $total_nuevo = venta_detalle::where('id_grupo_venta', $id_venta)->sum('subtotal');
            $venta->total_venta = $total_nuevo;
            $venta->save();

            // Respuesta de éxito con el nuevo total de la venta
            return response()->json([
                "status" => 200,
                "message" => "Venta actualizada exitosamente.",
                "id_venta" => $id_venta,
                "total_venta" => $total_nuevo
            ]);
        } catch (ValidationException $e) {
            // Error de validación
            return response()->json([
                "status" => 422,
                "errors" => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Error general
            return response()->json([
                "status" => 500,
                "message" => $e->getMessage()
            ], 500);
        }
    }


    public function eliminarVenta(Request $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'id_venta' => 'required|integer',  // Validar que id_venta se envíe en el cuerpo de la solicitud
            ]);

            // Obtener el ID de la venta desde el request
            $id_venta = $request->id_venta;

            // Buscar la venta por ID
            $venta = Ventas::find($id_venta);

            // Validar que la venta exista
            if (!$venta) {
                return response()->json([
                    "status" => 404,
                    "message" => "La venta con ID $id_venta no existe."
                ], 404);
            }

            // Obtener los detalles de la venta
            $detalles_venta = venta_detalle::where('id_grupo_venta', $id_venta)->get();

            // Inicializar la variable para la suma total
            $total_eliminado = 0;

            // Iterar sobre los detalles de la venta
            foreach ($detalles_venta as $detalle) {
                // Buscar el producto en la base de datos
                $producto_db = Productos::find($detalle->id_producto);

                // Asegurarse de que el producto exista
                if ($producto_db) {
                    // Devolver el stock del producto
                    $producto_db->stock += $detalle->cantidad;
                    $producto_db->save();

                    // Calcular el subtotal del producto eliminado
                    $total_eliminado += $detalle->subtotal;
                }
            }

            // Eliminar los detalles de la venta
            venta_detalle::where('id_grupo_venta', $id_venta)->delete();

            // Eliminar la venta
            $venta->delete();

            // Responder con el total eliminado y un mensaje de éxito
            return response()->json([
                "status" => 200,
                "message" => "Venta eliminada exitosamente.",
                "total_eliminado" => $total_eliminado
            ]);
        } catch (\Exception $e) {
            // En caso de error
            return response()->json([
                "status" => 500,
                "message" => $e->getMessage()
            ], 500);
        }
    }
    public function obtenerVentasConDetalles(Request $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'id_venta' => 'required|integer',  // Validar que id_venta se envíe en el cuerpo de la solicitud
            ]);

            // Obtener el ID de la venta desde el request
            $id_venta = $request->id_venta;

            // Buscar la venta con todos los campos de la tabla Ventas
            $venta = Ventas::where('id_ventas', $id_venta)->first();

            // Validar que la venta exista
            if (!$venta) {
                return response()->json([
                    "status" => 404,
                    "message" => "No existe una venta con el ID $id_venta."
                ], 404);
            }

            // Obtener todos los detalles de la venta con el id_grupo_venta igual al id_venta
            $detalles_venta = venta_detalle::where('id_grupo_venta', $id_venta)->get();

            // Recorrer los detalles de la venta y agregar el nombre del producto
            foreach ($detalles_venta as $detalle) {
                // Obtener el nombre del producto asociado
                $producto = Productos::find($detalle->id_producto);
                $detalle->nombre_producto = $producto ? $producto->nombre_producto : null;
            }

            // Obtener el nombre del usuario asociado con la venta
            $usuario = User::find($venta->id_usuario);
            $venta->nombre_usuario = $usuario ? $usuario->name : null;

            // Responder con todos los datos de la venta y los detalles
            return response()->json([
                "status" => 200,
                "message" => "Venta y detalles encontrados.",
                "venta" => $venta,  // Todos los campos de la venta
                "detalles_venta" => $detalles_venta  // Todos los detalles de la venta con nombres de productos
            ]);
        } catch (\Exception $e) {
            // En caso de error
            return response()->json([
                "status" => 500,
                "message" => $e->getMessage()
            ], 500);
        }
    }
    public function getAllVentas()
    {
        try {
            // Obtener todas las ventas
            $ventas = Ventas::all();

            // Validar si no existen ventas
            if ($ventas->isEmpty()) {
                return response()->json([
                    "status" => 404,
                    "message" => "No hay ventas registradas."
                ], 404);
            }

            // Inicializar variables para almacenar ganancias generales
            $gananciaBrutaGeneral = 0;
            $gananciaNetaGeneral = 0;

            // Arreglo para almacenar todas las ventas con sus detalles y ganancias
            $ventasConDetalles = [];

            // Recorrer todas las ventas
            foreach ($ventas as $venta) {
                // Obtener los detalles de la venta actual
                $detalles_venta = venta_detalle::where('id_grupo_venta', $venta->id_ventas)->get();

                // Inicializar ganancias para este grupo de venta
                $gananciaBrutaVenta = 0;
                $gananciaNetaVenta = 0;

                // Agregar el nombre del producto a cada detalle y calcular ganancias
                foreach ($detalles_venta as $detalle) {
                    $producto = Productos::find($detalle->id_producto);

                    // Agregar el nombre del producto
                    $detalle->nombre_producto = $producto ? $producto->nombre_producto : null;

                    if ($producto) {
                        // Calcular la ganancia bruta y neta para este detalle
                        $subtotalVenta = $detalle->cantidad * $detalle->precio_venta;
                        $subtotalCosto = $detalle->cantidad * $producto->precio_unitario;

                        $gananciaBrutaDetalle = $subtotalVenta - $subtotalCosto; // Ganancia bruta
                        $gananciaNetaDetalle = $gananciaBrutaDetalle; // Puedes restar costos adicionales aquí si aplican

                        // Sumar al total de la venta
                        $gananciaBrutaVenta += $gananciaBrutaDetalle;
                        $gananciaNetaVenta += $gananciaNetaDetalle;

                        // Agregar los cálculos al detalle
                        $detalle->ganancia_bruta = $gananciaBrutaDetalle;
                        $detalle->ganancia_neta = $gananciaNetaDetalle;
                    }
                }

                // Obtener el nombre del usuario asociado a la venta
                $usuario = User::find($venta->id_usuario);
                $venta->nombre_usuario = $usuario ? $usuario->name : null;

                // Agregar la venta y sus detalles al arreglo, incluyendo las ganancias
                $ventasConDetalles[] = [
                    "venta" => $venta,
                    "detalles_venta" => $detalles_venta,
                    "ganancia_bruta" => $gananciaBrutaVenta,
                    "ganancia_neta" => $gananciaNetaVenta
                ];

                // Sumar al total general
                $gananciaBrutaGeneral += $gananciaBrutaVenta;
                $gananciaNetaGeneral += $gananciaNetaVenta;
            }

            // Responder con todas las ventas, detalles y ganancias generales
            return response()->json([
                "status" => 200,
                "message" => "Ventas, detalles y ganancias calculados correctamente.",
                "ventas" => $ventasConDetalles,
                "ganancia_bruta_general" => $gananciaBrutaGeneral,
                "ganancia_neta_general" => $gananciaNetaGeneral
            ]);
        } catch (\Exception $e) {
            // En caso de error
            return response()->json([
                "status" => 500,
                "message" => $e->getMessage()
            ], 500);
        }
    }



}
