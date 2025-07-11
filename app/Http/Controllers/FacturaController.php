<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function index(): JsonResponse
    {
        $facturas = Factura::with('pago')->get();
        return response()->json($facturas);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_pago' => 'required|exists:pagos,id|unique:facturas,id_pago',
                'numero_factura' => 'required|string|unique:facturas,numero_factura',
                'rfc_cliente' => 'nullable|string|max:20',
                'nombre_cliente' => 'required|string|max:255',
                'direccion_cliente' => 'nullable|string|max:255',
                'fecha_emision' => 'required|date',
                'subtotal' => 'required|numeric|min:0',
                'iva' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $factura = Factura::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Factura creada exitosamente',
                'data' => $factura
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al crear la factura: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        $factura = Factura::with('pago')->findOrFail($id);
        return response()->json($factura);
    }

    public function generatePdf($id)
    {
        try {
            $factura = Factura::with('pago.paquete.destino', 'pago.turista')->findOrFail($id);

            // Aquí puedes añadir la lógica para obtener las reglas de viaje y sugerencias de itinerario
            // Por ejemplo, desde el modelo Paquete o un servicio dedicado.
            $reglasViaje = "Reglas de viaje de ejemplo para el paquete: {$factura->pago->paquete->nombre}";
            $sugerenciaItinerario = "Sugerencia de itinerario de ejemplo para el paquete: {$factura->pago->paquete->nombre}";

            $data = [
                'factura' => $factura,
                'reglasViaje' => $reglasViaje,
                'sugerenciaItinerario' => $sugerenciaItinerario,
            ];

            $pdf = Pdf::loadView('invoices.pdf_template', $data);
            return $pdf->download('factura_' . $factura->numero_factura . '.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el PDF de la factura: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
