<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    //Listar productos
    public function index()
    {
        try {
            $products = DB::select('select * from products order by created_at desc');
            return response()->json([
                'status'  => 200,
                'message' => 'Productos obtenidos con éxito',
                'data'    => $products,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 100,
                'message' => 'Error al obtener los productos' . $e->getMessage(),
                'data'    => null,

            ], 200);
        }
    }

    //Obtener un producto por ID
    public function show($id)
    {
        try {
            $products = DB::select('select * from products where id = ?', [$id]);
            if (empty($products)) {
                return response()->json([
                    'status'  => 100,
                    'message' => 'Producto no encontrado',
                    'data'    => null,
                ], 200);
            }
            return response()->json([
                'status'  => 200,
                'message' => 'Producto obtenido con éxito',
                'data'    => $products[0],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 100,
                'message' => 'Error al obtener el producto' . $e->getMessage(),
                'data'    => null,
            ], 200);
        }
    }
    //Funcion de crear nuevo producto
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 100,
                    'message' => 'Validación fallida',
                    'data'    => $validator->errors(),
                ], 422);
            }

            $now = now()->format('Y-m-d H:i:s');
            DB::insert('insert into products (name, price, description, created_at, updated_at) values (?, ?, ?, NOW(), NOW())', [$request->name, $request->price, $request->description, $now, $now]);

            $id      = DB::getPdo()->lastInsertId();
            $product = DB::select('select * from products where id = ?', [$id]);
            return response()->json([
                'status'  => 200,
                'message' => 'Producto creado con éxito',
                'data'    => $product[0],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 100,
                'message' => 'Error al crear el producto' . $e->getMessage(),
                'data'    => null,
            ], 200);
        }
    }

    //Actualizar un producto
    public function update(Request $request, $id)
    {
        try {
            $products = DB::select('select * from products where id = ?', [$id]);
            if (empty($products)) {
                return response()->json([
                    'status'  => 100,
                    'message' => 'Producto no encontrado',
                    'data'    => null,
                ], 200);
            }

            $validator = Validator::make($request->all(), [
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric|min:0',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 100,
                    'message' => 'Validación fallida',
                    'errors'  => $validator->errors(),
                ], 200);
            }

            $now = now()->format('Y-m-d H:i:s');
            DB::update('update products set name = ?, price = ?, description = ?, updated_at = ? where id = ?', [$request->name, $request->price, $request->description, $now, $id]);

            $product = DB::select('select * from products where id = ?', [$id]);

            return response()->json([
                'status'  => 200,
                'message' => 'Producto actualizado con éxito',
                'data'    => $product[0],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 100,
                'message' => 'Error al actualizar el producto' . $e->getMessage(),
                'data'    => null,
            ], 200);
        }
    }

    //Fucion Eliminar un producto
    public function destroy($id)
    {
        try {
            $products = DB::select('select * from products where id = ?', [$id]);
            if (empty($products)) {
                return response()->json([
                    'status'  => 100,
                    'message' => 'Producto no encontrado',
                    'data'    => null,
                ], 200);
            }

            DB::delete('delete from products where id = ?', [$id]);

            return response()->json([
                'status'  => 200,
                'message' => 'Producto eliminado con éxito',
                'data'    => null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 100,
                'message' => 'Error al eliminar el producto' . $e->getMessage(),
                'data'    => null,
            ], 200);
        }
    }
}
