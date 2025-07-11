<?php

namespace App\Http\Controllers;

use App\Models\ImagenDestino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ImagenDestinoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ImagenDestino::query();

        if ($request->has('id_destino')) {
            $query->where('id_destino', $request->id_destino);
        }

        $imagenes = $query->get();
        return response()->json($imagenes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_destino' => 'required|integer|exists:destinos,id',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'es_principal' => 'nullable|boolean',
            'descripcion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Guardar la imagen y obtener la ruta
        $path = $request->file('imagen')->store('public/imagenes_destinos');
        $url = Storage::url($path);

        $data = $validator->validated(); // Usar datos validados
        $data['url_imagen'] = $url;

        $imagenDestino = ImagenDestino::create($data);

        return response()->json($imagenDestino, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $imagenDestino = ImagenDestino::find($id);

        if (is_null($imagenDestino)) {
            return response()->json([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($imagenDestino);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $imagenDestino = ImagenDestino::find($id);

        if (is_null($imagenDestino)) {
            return response()->json([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Solo actualizamos metadatos, no el archivo en sí.
        $validator = Validator::make($request->all(), [
            'es_principal' => 'nullable|boolean',
            'descripcion' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $imagenDestino->update($validator->validated()); // Usar datos validados

        return response()->json($imagenDestino);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $imagenDestino = ImagenDestino::find($id);

        if (is_null($imagenDestino)) {
            return response()->json([
                'success' => false,
                'message' => 'Imagen no encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        // Extraer la ruta relativa del almacenamiento desde la URL completa
        $path = str_replace('/storage', 'public', parse_url($imagenDestino->url_imagen, PHP_URL_PATH));

        // Eliminar el archivo físico
        if (Storage::exists($path)) {
            Storage::delete($path);
        }

        // Eliminar el registro de la base de datos
        $imagenDestino->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
