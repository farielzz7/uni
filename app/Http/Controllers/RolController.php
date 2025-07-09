<?php



namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    //  Listar todos los roles
    public function index()
    {
        return response()->json(Rol::all());
    }

    //  Mostrar un rol por ID
    public function show($id)
    {
        $rol = Rol::findOrFail($id);
        return response()->json($rol);
    }

    //  Crear nuevo rol
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'clave_rol' => 'required|string|max:100|unique:roles,clave_rol'
        ]);

        $rol = Rol::create($validated);
        return response()->json($rol, 201);
    }

    //  Actualizar rol existente
    public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'clave_rol' => 'sometimes|string|max:100|unique:roles,clave_rol,' . $rol->id
        ]);

        $rol->update($validated);
        return response()->json($rol);
    }

    //  Eliminar rol
    public function destroy($id)
    {
        $rol = Rol::findOrFail($id);
        $rol->delete();
        return response()->json(['message' => 'Rol eliminado correctamente.']);
    }
}
