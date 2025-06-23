<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::all();
        return response()->json($notificaciones);
    }

    public function markAsRead($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update(['leido' => true]);
        return response()->json(['message' => 'Notificación marcada como leída']);
    }
}
