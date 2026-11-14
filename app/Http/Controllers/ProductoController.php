<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    /** Mensajes de validación en español. */
    private const MENSAJES = [
        'required' => 'El campo :attribute es obligatorio.',
        'string'   => 'El campo :attribute debe ser texto.',
        'numeric'  => 'El campo :attribute debe ser numérico.',
        'integer'  => 'El campo :attribute debe ser un número entero.',
        'min'      => 'El campo :attribute debe ser como mínimo :min.',
        'max'      => 'El campo :attribute no debe superar :max caracteres.',
    ];

    /** GET /api/productos */
    public function index(): JsonResponse
    {
        return response()->json(Producto::orderBy('id')->get());
    }

    /** GET /api/productos/{id} */
    public function show(int $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (! $producto) {
            return $this->noEncontrado();
        }

        return response()->json($producto);
    }

    /** POST /api/productos */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio'      => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
        ], self::MENSAJES);

        if ($validator->fails()) {
            return $this->errorValidacion($validator);
        }

        $producto = Producto::create($validator->validated());

        return response()->json([
            'message' => 'Producto creado exitosamente',
            'data'    => $producto,
        ], 201);
    }

    /** PUT/PATCH /api/productos/{id} (actualización parcial permitida) */
    public function update(Request $request, int $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (! $producto) {
            return $this->noEncontrado();
        }

        $validator = Validator::make($request->all(), [
            'nombre'      => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|nullable|string',
            'precio'      => 'sometimes|required|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
        ], self::MENSAJES);

        if ($validator->fails()) {
            return $this->errorValidacion($validator);
        }

        $producto->update($validator->validated());

        return response()->json([
            'message' => 'Producto actualizado exitosamente',
            'data'    => $producto->fresh(),
        ]);
    }

    /** DELETE /api/productos/{id} */
    public function destroy(int $id): JsonResponse
    {
        $producto = Producto::find($id);

        if (! $producto) {
            return $this->noEncontrado();
        }

        $producto->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente',
        ]);
    }

    private function noEncontrado(): JsonResponse
    {
        return response()->json(['message' => 'Producto no encontrado'], 404);
    }

    private function errorValidacion($validator): JsonResponse
    {
        return response()->json([
            'message' => 'Error de validación',
            'errors'  => $validator->errors(),
        ], 422);
    }
}
