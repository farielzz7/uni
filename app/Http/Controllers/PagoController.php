<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class PagoController extends Controller
{
    public function index(): JsonResponse
    {
        $pagos = Pago::all();
        return response()->json($pagos);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'monto' => 'required|numeric|min:0.01',
                'moneda' => 'required|string|size:3',
                'estado' => 'required|string|in:pendiente,completado,fallido',
                'metodo_pago_id' => 'required|exists:metodos_pago,id',
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

            $pago = Pago::create($validator->validated());

            return response()->json($pago, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        $pago = Pago::findOrFail($id);
        return response()->json($pago);
    }

    public function createPaymentIntent(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
        ]);

        \Stripe\Stripe::setApiKey(config('stripe.secret'));

        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $request->amount * 100, // Stripe expects the amount in cents
                'currency' => $request->currency,
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createMercadoPagoPreference(Request $request): JsonResponse
    {
        \MercadoPago\SDK::setAccessToken(config('mercadopago.access_token'));

        $request->validate([
            'title' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0.01',
        ]);

        $preference = new \MercadoPago\Preference();

        $item = new \MercadoPago\Item();
        $item->title = $request->title;
        $item->quantity = $request->quantity;
        $item->unit_price = (float)$request->unit_price;

        $preference->items = array($item);
        $preference->back_urls = array(
            "success" => env('APP_URL') . "/success", // Replace with your success URL
            "failure" => env('APP_URL') . "/failure", // Replace with your failure URL
            "pending" => env('APP_URL') . "/pending"  // Replace with your pending URL
        );
        $preference->auto_return = "approved";

        try {
            $preference->save();
            return response()->json([
                'id' => $preference->id,
                'init_point' => $preference->init_point,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createPayPalOrder(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
        ]);

        $client = new Client([
            'base_uri' => config('services.paypal.base_uri'),
        ]);

        try {
            // Obtain Access Token
            $response = $client->post('/v1/oauth2/token', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                ],
                'auth' => [
                    config('services.paypal.client_id'),
                    config('services.paypal.secret')
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $accessToken = $data['access_token'];

            // Create Order
            $response = $client->post('/v2/checkout/orders', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'amount' => [
                                'currency_code' => $request->currency,
                                'value' => $request->amount,
                            ],
                        ],
                    ],
                    'application_context' => [
                        'return_url' => env('APP_URL') . '/paypal/success',
                        'cancel_url' => env('APP_URL') . '/paypal/cancel',
                    ],
                ],
            ]);

            $order = json_decode($response->getBody(), true);

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function capturePayPalOrder(Request $request, $orderId): JsonResponse
    {
        $client = new Client([
            'base_uri' => config('services.paypal.base_uri'),
        ]);

        try {
            // Obtain Access Token
            $response = $client->post('/v1/oauth2/token', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en_US',
                ],
                'auth' => [
                    config('services.paypal.client_id'),
                    config('services.paypal.secret')
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $accessToken = $data['access_token'];

            // Capture Order
            $response = $client->post('/v2/checkout/orders/' . $orderId . '/capture', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $capture = json_decode($response->getBody(), true);

            // Here you would typically update your database with the payment status
            // For example, find the Pago record associated with this order and mark it as 'completado'

            return response()->json($capture);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

