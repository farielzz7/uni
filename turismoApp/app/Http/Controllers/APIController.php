<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class APIController extends Controller
{
    private $amadeus_client_id;
    private $amadeus_client_secret;
    private $amadeus_base_url;
    private $booking_api_key;
    private $tripadvisor_api_key;
    private $openweather_api_key;

    public function __construct()
    {
        $this->amadeus_client_id = env('AMADEUS_CLIENT_ID');
        $this->amadeus_client_secret = env('AMADEUS_CLIENT_SECRET');
        $this->amadeus_base_url = env('AMADEUS_BASE_URL', 'https://test.api.amadeus.com');
        $this->booking_api_key = env('BOOKING_API_KEY');
        $this->tripadvisor_api_key = env('TRIPADVISOR_API_KEY');
        $this->openweather_api_key = env('OPENWEATHER_API_KEY');
    }

    /**
     * Obtener token de acceso para Amadeus API
     */
    private function getAmadeusToken()
    {
        $cacheKey = 'amadeus_token';
        
        return Cache::remember($cacheKey, 1800, function () {
            try {
                $response = Http::asForm()->post($this->amadeus_base_url . '/v1/security/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->amadeus_client_id,
                    'client_secret' => $this->amadeus_client_secret,
                ]);

                if ($response->successful()) {
                    return $response->json()['access_token'];
                }
                
                throw new Exception('Error obteniendo token de Amadeus');
            } catch (Exception $e) {
                Log::error('Error obteniendo token Amadeus: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Buscar vuelos en tiempo real
     */
    public function buscarVuelos(Request $request)
    {
        try {
            $request->validate([
                'origen' => 'required|string|size:3',
                'destino' => 'required|string|size:3',
                'fecha_ida' => 'required|date',
                'fecha_vuelta' => 'nullable|date',
                'adultos' => 'required|integer|min:1|max:9',
                'ninos' => 'nullable|integer|min:0|max:9',
                'clase' => 'nullable|in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST'
            ]);

            $token = $this->getAmadeusToken();
            if (!$token) {
                return response()->json(['error' => 'No se pudo obtener token API'], 500);
            }

            $params = [
                'originLocationCode' => $request->origen,
                'destinationLocationCode' => $request->destino,
                'departureDate' => $request->fecha_ida,
                'adults' => $request->adultos,
                'currencyCode' => 'MXN',
                'max' => 50
            ];

            if ($request->fecha_vuelta) {
                $params['returnDate'] = $request->fecha_vuelta;
            }

            if ($request->ninos) {
                $params['children'] = $request->ninos;
            }

            if ($request->clase) {
                $params['travelClass'] = $request->clase;
            }

            $response = Http::withToken($token)
                ->get($this->amadeus_base_url . '/v2/shopping/flight-offers', $params);

            if ($response->successful()) {
                $vuelos = $this->procesarRespuestaVuelos($response->json());
                return response()->json([
                    'success' => true,
                    'data' => $vuelos,
                    'total' => count($vuelos)
                ]);
            }

            return response()->json(['error' => 'Error en la búsqueda de vuelos'], 400);

        } catch (Exception $e) {
            Log::error('Error buscando vuelos: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Buscar hoteles en tiempo real
     */
    public function buscarHoteles(Request $request)
    {
        try {
            $request->validate([
                'ciudad' => 'required|string',
                'fecha_entrada' => 'required|date',
                'fecha_salida' => 'required|date|after:fecha_entrada',
                'huespedes' => 'required|integer|min:1',
                'habitaciones' => 'required|integer|min:1',
                'precio_min' => 'nullable|numeric|min:0',
                'precio_max' => 'nullable|numeric',
                'estrellas' => 'nullable|integer|min:1|max:5'
            ]);

            // Usando Booking.com API
            $params = [
                'ss' => $request->ciudad,
                'checkin_year' => date('Y', strtotime($request->fecha_entrada)),
                'checkin_month' => date('n', strtotime($request->fecha_entrada)),
                'checkin_monthday' => date('j', strtotime($request->fecha_entrada)),
                'checkout_year' => date('Y', strtotime($request->fecha_salida)),
                'checkout_month' => date('n', strtotime($request->fecha_salida)),
                'checkout_monthday' => date('j', strtotime($request->fecha_salida)),
                'no_rooms' => $request->habitaciones,
                'group_adults' => $request->huespedes,
                'format' => 'json',
                'currency' => 'MXN',
                'rows' => 25
            ];

            if ($request->precio_min) {
                $params['price_filter_currencycode'] = 'MXN';
                $params['price_filter_min'] = $request->precio_min;
            }

            if ($request->precio_max) {
                $params['price_filter_max'] = $request->precio_max;
            }

            if ($request->estrellas) {
                $params['class'] = $request->estrellas;
            }

            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->booking_api_key,
                'X-RapidAPI-Host' => 'booking-com.p.rapidapi.com'
            ])->get('https://booking-com.p.rapidapi.com/v1/hotels/search', $params);

            if ($response->successful()) {
                $hoteles = $this->procesarRespuestaHoteles($response->json());
                return response()->json([
                    'success' => true,
                    'data' => $hoteles,
                    'total' => count($hoteles)
                ]);
            }

            return response()->json(['error' => 'Error en la búsqueda de hoteles'], 400);

        } catch (Exception $e) {
            Log::error('Error buscando hoteles: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Buscar actividades y atracciones turísticas
     */
    public function buscarActividades(Request $request)
    {
        try {
            $request->validate([
                'ubicacion' => 'required|string',
                'categoria' => 'nullable|in:attractions,restaurants,hotels,geos',
                'limite' => 'nullable|integer|min:1|max:30'
            ]);

            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->tripadvisor_api_key,
                'X-RapidAPI-Host' => 'tripadvisor1.p.rapidapi.com'
            ])->get('https://tripadvisor1.p.rapidapi.com/restaurants/list', [
                'location_id' => $this->obtenerLocationId($request->ubicacion),
                'restaurant_tagcategory' => '10591',
                'restaurant_tagcategory_standalone' => '10591',
                'currency' => 'MXN',
                'lang' => 'es_MX',
                'limit' => $request->limite ?? 20
            ]);

            if ($response->successful()) {
                $actividades = $this->procesarRespuestaActividades($response->json());
                return response()->json([
                    'success' => true,
                    'data' => $actividades,
                    'total' => count($actividades)
                ]);
            }

            return response()->json(['error' => 'Error en la búsqueda de actividades'], 400);

        } catch (Exception $e) {
            Log::error('Error buscando actividades: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener información del clima
     */
    public function obtenerClima(Request $request)
    {
        try {
            $request->validate([
                'ciudad' => 'required|string',
                'dias' => 'nullable|integer|min:1|max:7'
            ]);

            $dias = $request->dias ?? 5;

            $response = Http::get('https://api.openweathermap.org/data/2.5/forecast', [
                'q' => $request->ciudad,
                'appid' => $this->openweather_api_key,
                'units' => 'metric',
                'lang' => 'es',
                'cnt' => $dias * 8 // 8 pronósticos por día (cada 3 horas)
            ]);

            if ($response->successful()) {
                $clima = $this->procesarRespuestaClima($response->json());
                return response()->json([
                    'success' => true,
                    'data' => $clima
                ]);
            }

            return response()->json(['error' => 'Error obteniendo información del clima'], 400);

        } catch (Exception $e) {
            Log::error('Error obteniendo clima: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Crear paquete personalizado basado en presupuesto
     */
    public function crearPaquetePersonalizado(Request $request)
    {
        try {
            $request->validate([
                'presupuesto_total' => 'required|numeric|min:1000',
                'origen' => 'required|string',
                'destino' => 'required|string',
                'fecha_ida' => 'required|date',
                'fecha_vuelta' => 'required|date|after:fecha_ida',
                'personas' => 'required|integer|min:1',
                'porcentaje_vuelos' => 'nullable|numeric|min:0|max:100',
                'porcentaje_hoteles' => 'nullable|numeric|min:0|max:100',
                'porcentaje_actividades' => 'nullable|numeric|min:0|max:100',
                'categoria_hotel' => 'nullable|in:economico,medio,lujo',
                'tipo_actividades' => 'nullable|array'
            ]);

            // Calcular distribución del presupuesto
            $distribucion = $this->calcularDistribucionPresupuesto($request);

            // Buscar opciones en paralelo
            $resultados = [
                'vuelos' => $this->buscarVuelosParaPaquete($request, $distribucion['vuelos']),
                'hoteles' => $this->buscarHotelesParaPaquete($request, $distribucion['hoteles']),
                'actividades' => $this->buscarActividadesParaPaquete($request, $distribucion['actividades'])
            ];

            // Generar combinaciones de paquetes
            $paquetes = $this->generarCombinacionesPaquetes($resultados, $request->presupuesto_total);

            return response()->json([
                'success' => true,
                'presupuesto_total' => $request->presupuesto_total,
                'distribucion' => $distribucion,
                'paquetes_sugeridos' => $paquetes,
                'total_opciones' => count($paquetes)
            ]);

        } catch (Exception $e) {
            Log::error('Error creando paquete personalizado: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Obtener cotización de cambio de moneda
     */
    public function obtenerTipoCambio(Request $request)
    {
        try {
            $request->validate([
                'from' => 'required|string|size:3',
                'to' => 'required|string|size:3',
                'amount' => 'nullable|numeric|min:0'
            ]);

            $response = Http::get('https://api.exchangerate-api.com/v4/latest/' . $request->from);

            if ($response->successful()) {
                $data = $response->json();
                $rate = $data['rates'][$request->to] ?? null;

                if (!$rate) {
                    return response()->json(['error' => 'Moneda no encontrada'], 400);
                }

                $resultado = [
                    'from' => $request->from,
                    'to' => $request->to,
                    'rate' => $rate,
                    'fecha' => $data['date']
                ];

                if ($request->amount) {
                    $resultado['amount'] = $request->amount;
                    $resultado['converted'] = round($request->amount * $rate, 2);
                }

                return response()->json([
                    'success' => true,
                    'data' => $resultado
                ]);
            }

            return response()->json(['error' => 'Error obteniendo tipo de cambio'], 400);

        } catch (Exception $e) {
            Log::error('Error obteniendo tipo de cambio: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    // Métodos auxiliares para procesar respuestas

    private function procesarRespuestaVuelos($data)
    {
        if (!isset($data['data'])) return [];

        return collect($data['data'])->map(function ($vuelo) {
            $segmentos = $vuelo['itineraries'][0]['segments'] ?? [];
            $precio = $vuelo['price'] ?? [];

            return [
                'id' => $vuelo['id'],
                'precio' => [
                    'total' => $precio['total'] ?? 0,
                    'moneda' => $precio['currency'] ?? 'MXN'
                ],
                'duracion' => $vuelo['itineraries'][0]['duration'] ?? '',
                'segmentos' => collect($segmentos)->map(function ($segmento) {
                    return [
                        'origen' => $segmento['departure']['iataCode'] ?? '',
                        'destino' => $segmento['arrival']['iataCode'] ?? '',
                        'fecha_salida' => $segmento['departure']['at'] ?? '',
                        'fecha_llegada' => $segmento['arrival']['at'] ?? '',
                        'aerolinea' => $segmento['carrierCode'] ?? '',
                        'numero_vuelo' => $segmento['number'] ?? ''
                    ];
                })
            ];
        })->toArray();
    }

    private function procesarRespuestaHoteles($data)
    {
        if (!isset($data['result'])) return [];

        return collect($data['result'])->map(function ($hotel) {
            return [
                'id' => $hotel['hotel_id'] ?? '',
                'nombre' => $hotel['hotel_name'] ?? '',
                'precio_por_noche' => $hotel['min_total_price'] ?? 0,
                'moneda' => $hotel['currency_code'] ?? 'MXN',
                'estrellas' => $hotel['class'] ?? 0,
                'puntuacion' => $hotel['review_score'] ?? 0,
                'imagen' => $hotel['main_photo_url'] ?? '',
                'ubicacion' => $hotel['address'] ?? '',
                'servicios' => $hotel['hotel_facilities'] ?? []
            ];
        })->toArray();
    }

    private function procesarRespuestaActividades($data)
    {
        if (!isset($data['data'])) return [];

        return collect($data['data'])->map(function ($actividad) {
            return [
                'id' => $actividad['location_id'] ?? '',
                'nombre' => $actividad['name'] ?? '',
                'descripcion' => $actividad['description'] ?? '',
                'precio_estimado' => $actividad['price_level'] ?? '',
                'puntuacion' => $actividad['rating'] ?? 0,
                'imagen' => $actividad['photo']['images']['medium']['url'] ?? '',
                'categoria' => $actividad['category']['name'] ?? '',
                'ubicacion' => $actividad['address'] ?? ''
            ];
        })->toArray();
    }

    private function procesarRespuestaClima($data)
    {
        if (!isset($data['list'])) return [];

        $pronosticos = collect($data['list'])->map(function ($item) {
            return [
                'fecha' => $item['dt_txt'],
                'temperatura' => [
                    'actual' => $item['main']['temp'],
                    'sensacion' => $item['main']['feels_like'],
                    'minima' => $item['main']['temp_min'],
                    'maxima' => $item['main']['temp_max']
                ],
                'humedad' => $item['main']['humidity'],
                'descripcion' => $item['weather'][0]['description'],
                'icono' => $item['weather'][0]['icon']
            ];
        });

        return [
            'ciudad' => $data['city']['name'],
            'pais' => $data['city']['country'],
            'pronosticos' => $pronosticos->toArray()
        ];
    }

    private function calcularDistribucionPresupuesto($request)
    {
        $total = $request->presupuesto_total;
        
        // Distribución por defecto
        $vuelos = $request->porcentaje_vuelos ?? 40;
        $hoteles = $request->porcentaje_hoteles ?? 35;
        $actividades = $request->porcentaje_actividades ?? 25;

        // Normalizar si la suma no es 100%
        $suma = $vuelos + $hoteles + $actividades;
        if ($suma != 100) {
            $vuelos = ($vuelos / $suma) * 100;
            $hoteles = ($hoteles / $suma) * 100;
            $actividades = ($actividades / $suma) * 100;
        }

        return [
            'vuelos' => ($total * $vuelos) / 100,
            'hoteles' => ($total * $hoteles) / 100,
            'actividades' => ($total * $actividades) / 100
        ];
    }

    private function buscarVuelosParaPaquete($request, $presupuesto_vuelos)
    {
        // Implementar búsqueda específica para paquetes
        // Similar a buscarVuelos pero filtrado por presupuesto
        return [];
    }

    private function buscarHotelesParaPaquete($request, $presupuesto_hoteles)
    {
        // Implementar búsqueda específica para paquetes
        // Similar a buscarHoteles pero filtrado por presupuesto
        return [];
    }

    private function buscarActividadesParaPaquete($request, $presupuesto_actividades)
    {
        // Implementar búsqueda específica para paquetes
        // Similar a buscarActividades pero filtrado por presupuesto
        return [];
    }

    private function generarCombinacionesPaquetes($resultados, $presupuesto_total)
    {
        // Generar diferentes combinaciones de vuelos + hoteles + actividades
        // que se ajusten al presupuesto total
        return [];
    }

    private function obtenerLocationId($ubicacion)
    {
        // Obtener location_id de TripAdvisor para la búsqueda
        return '60763'; // ID de ejemplo - implementar búsqueda real
    }
}