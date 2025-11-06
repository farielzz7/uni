<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\TuristaController;
use App\Http\Controllers\DestinoController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\APIController;
use App\Http\Controllers\BuscadorController;
use App\Http\Controllers\CategoriaDestinoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImagenDestinoController;
use App\Http\Controllers\ItinerarioController;
use App\Http\Controllers\ItinerarioPersonalizadoController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RedSocialController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SoporteController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\TipoPaqueteController;
use App\Http\Controllers\TipoServicioController;
use App\Http\Controllers\TransaccionExternaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('user', fn (Request $request) => $request->user());

    Route::apiResource('roles', RolController::class);
    Route::apiResource('permisos', PermisoController::class);
    Route::apiResource('turistas', TuristaController::class);
    Route::apiResource('servicios', ServicioController::class);
    Route::apiResource('destinos', DestinoController::class);
    Route::apiResource('hoteles', HotelController::class);
    Route::apiResource('categorias-destino', CategoriaDestinoController::class);
    Route::apiResource('comentarios', ComentarioController::class);
    Route::apiResource('imagenes-destino', ImagenDestinoController::class);
    Route::apiResource('itinerarios', ItinerarioController::class)->only(['index', 'show']);
    Route::apiResource('itinerarios-personalizados', ItinerarioPersonalizadoController::class);
    Route::post('itinerarios-personalizados/generar', [ItinerarioPersonalizadoController::class, 'generatePersonalizedItinerary']);
    Route::apiResource('metodos-pago', MetodoPagoController::class);
    Route::apiResource('pagos', PagoController::class)->only(['index', 'store', 'show']);
    Route::post('pagos/stripe/payment-intent', [PagoController::class, 'createPaymentIntent']);
    Route::post('pagos/mercadopago/preference', [PagoController::class, 'createMercadoPagoPreference']);
    Route::post('pagos/paypal/order', [PagoController::class, 'createPayPalOrder']);
    Route::post('pagos/paypal/order/{orderId}/capture', [PagoController::class, 'capturePayPalOrder']);
    Route::apiResource('paquetes', PaqueteController::class);
    Route::apiResource('promociones', PromocionController::class);
    Route::post('promociones/aplicar', [PromocionController::class, 'aplicarDescuento']);
    Route::apiResource('proveedores', ProveedorController::class);
    Route::apiResource('reservas', ReservaController::class);
    Route::get('roles/{roleId}/permisos', [RolePermissionController::class, 'getRolePermissions']);
    Route::post('roles/{roleId}/permisos', [RolePermissionController::class, 'assignPermission']);
    Route::delete('roles/{roleId}/permisos', [RolePermissionController::class, 'revokePermission']);
    Route::apiResource('soportes', SoporteController::class);
    Route::apiResource('suscripciones', SuscripcionController::class);
    Route::apiResource('tipos-paquete', TipoPaqueteController::class);
    Route::apiResource('tipos-servicio', TipoServicioController::class);
    Route::apiResource('transacciones-externas', TransaccionExternaController::class);
    Route::apiResource('users', UserController::class);
    Route::get('users/roles', [UserController::class, 'getRoles']);
    Route::get('users/{userId}/roles', [UserRoleController::class, 'getUserRoles']);
    Route::post('users/{userId}/roles', [UserRoleController::class, 'assignRole']);
    Route::delete('users/{userId}/roles', [UserRoleController::class, 'revokeRole']);
    Route::get('perfil/{id}', [PerfilController::class, 'show']);
    Route::put('perfil/{id}', [PerfilController::class, 'update']);
    Route::get('notificaciones', [NotificacionController::class, 'index']);
    Route::put('notificaciones/{id}/read', [NotificacionController::class, 'markAsRead']);
    Route::get('configuracion', [ConfiguracionController::class, 'index']);
    Route::post('configuracion', [ConfiguracionController::class, 'update']);
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('facturas/{id}/pdf', [FacturaController::class, 'generatePdf']);
    Route::apiResource('facturas', FacturaController::class)->only(['index', 'store', 'show']);
    Route::get('contrato/{turista}/{paquete}', [ContratoController::class, 'generateContract']);
});

Route::get('test', fn () => response()->json(['status' => 'ok']));

Route::prefix('buscador')->group(function () {
    Route::get('/', [BuscadorController::class, 'index']);
    Route::post('buscar', [BuscadorController::class, 'buscar']);
    Route::post('personalizar', [BuscadorController::class, 'personalizar']);
});

Route::prefix('integraciones')->group(function () {
    Route::post('vuelos', [APIController::class, 'buscarVuelos']);
    Route::post('hoteles', [APIController::class, 'buscarHoteles']);
    Route::post('actividades', [APIController::class, 'buscarActividades']);
    Route::get('clima', [APIController::class, 'obtenerClima']);
    Route::post('paquete-personalizado', [APIController::class, 'crearPaquetePersonalizado']);
    Route::get('tipo-cambio', [APIController::class, 'obtenerTipoCambio']);
});

Route::post('contacto/send-message', [ContactoController::class, 'sendMessage']);
Route::get('home', [HomeController::class, 'index']);

Route::get('red-social', [RedSocialController::class, 'index']);
Route::post('red-social/seguir/{id}', [RedSocialController::class, 'seguirUsuario']);
Route::post('red-social/dejar-de-seguir/{id}', [RedSocialController::class, 'dejarDeSeguirUsuario']);

