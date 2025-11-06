<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\Turista;
use App\Models\Paquete;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ContratoController extends Controller
{
    public function generateContract(Request $request, $turistaId, $paqueteId): JsonResponse
    {
        try {
            $validator = Validator::make([
                'turista_id' => $turistaId,
                'paquete_id' => $paqueteId,
            ], [
                'turista_id' => 'required|exists:turistas,id',
                'paquete_id' => 'required|exists:paquetes,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $turista = Turista::findOrFail($turistaId);
            $paquete = Paquete::findOrFail($paqueteId);

            $user = $request->user();

            if (!$user || ($user->id !== $turista->id_usuario && !$user->hasRole(Config::get('roles.admin', 'admin')))) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado para generar este contrato'
                ], Response::HTTP_FORBIDDEN);
            }

            // Obtener datos de la empresa de la configuración
            $empresaNombre = Configuracion::where('clave', 'empresa_nombre')->value('valor') ?? 'GoPlan';
            $empresaDireccion = Configuracion::where('clave', 'empresa_direccion')->value('valor') ?? 'Calle Ficticia 123, Ciudad Imaginaria';
            $empresaTelefono = Configuracion::where('clave', 'empresa_telefono')->value('valor') ?? '+123 456 7890';
            $empresaEmail = Configuracion::where('clave', 'empresa_email')->value('valor') ?? 'contacto@goplan.com';

            $data = [
                'turista' => $turista,
                'paquete' => $paquete,
                'fecha_contrato' => now()->format('d/m/Y'),
                'empresa_nombre' => $empresaNombre,
                'empresa_direccion' => $empresaDireccion,
                'empresa_telefono' => $empresaTelefono,
                'empresa_email' => $empresaEmail,
            ];

            $pdf = PDF::loadView('contracts.template', $data);

            $fileName = sprintf(
                'contrato-%s-%s-%s.pdf',
                $turista->id,
                $paquete->id,
                now()->format('YmdHis')
            );

            $path = 'contracts/' . $fileName;
            Storage::disk('public')->put($path, $pdf->output());

            return response()->json([
                'success' => true,
                'message' => 'Contrato generado exitosamente',
                'pdf_url' => Storage::disk('public')->url($path)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al generar el contrato: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
