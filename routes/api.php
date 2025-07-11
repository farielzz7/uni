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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});


// Roles Routes

Route::get('/roles', [RolController::class, 'index']);
Route::get('/roles/{id}', [RolController::class, 'show']);
Route::post('/roles', [RolController::class, 'store']);
Route::put('/roles/{id}', [RolController::class, 'update']);
Route::patch('/roles/{id}', [RolController::class, 'update']);
Route::delete('/roles/{id}', [RolController::class, 'destroy']);

// Permisos Routes

Route::get('/permisos', [PermisoController::class, 'index']);
Route::get('/permisos/{id}', [PermisoController::class, 'show']);
Route::post('/permisos', [PermisoController::class, 'store']);
Route::put('/permisos/{id}', [PermisoController::class, 'update']);
Route::patch('/permisos/{id}', [PermisoController::class, 'update']);
Route::delete('/permisos/{id}', [PermisoController::class, 'destroy']);

// Turistas Routes

    Route::get('/turistas', [TuristaController::class, 'index']);
    Route::post('/turistas', [TuristaController::class, 'store']);
    Route::get('/turistas/{id}', [TuristaController::class, 'show']);
    Route::put('/turistas/{id}', [TuristaController::class, 'update']);
    Route::delete('/turistas/{id}', [TuristaController::class, 'destroy']);

Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});
// Servicios Routes

    Route::get('/servicios', [ServicioController::class, 'index']);
    Route::get('/servicios/{id}', [ServicioController::class, 'show']);
    Route::post('/servicios', [ServicioController::class, 'store']);
    Route::put('/servicios/{id}', [ServicioController::class, 'update']);
    Route::delete('/servicios/{id}', [ServicioController::class, 'destroy']);

  // Destino Routes

    Route::get('/destino', [DestinoController::class, 'index']);
    Route::get('/destino/{id}', [DestinoController::class, 'show']);
    Route::post('/destino', [DestinoController::class, 'store']);
    Route::put('/destino/{id}', [DestinoController::class, 'update']);
    Route::delete('/destino/{id}', [DestinoController::class, 'destroy']);

// Hoteles Routes
Route::get('hoteles', [HotelController::class, 'index']);
Route::post('hoteles', [HotelController::class, 'store']);
Route::put('hoteles/{id}', [HotelController::class, 'update']);
Route::delete('hoteles/{id}', [HotelController::class, 'destroy']);

// Roles Routes
Route::get('/roles', [RolController::class, 'index']);
Route::post('/roles', [RolController::class, 'store']);
Route::get('/roles/{id}', [RolController::class, 'show']);
Route::put('/roles/{id}', [RolController::class, 'update']);
Route::patch('/roles/{id}', [RolController::class, 'update']);
Route::delete('/roles/{id}', [RolController::class, 'destroy']);

// Permisos Routes
Route::get('/permisos', [PermisoController::class, 'index']);
Route::post('/permisos', [PermisoController::class, 'store']);
Route::get('/permisos/{id}', [PermisoController::class, 'show']);
Route::put('/permisos/{id}', [PermisoController::class, 'update']);
Route::patch('/permisos/{id}', [PermisoController::class, 'update']);
Route::delete('/permisos/{id}', [PermisoController::class, 'destroy']);

// Turistas Routes
Route::get('/turistas', [TuristaController::class, 'index']);
Route::post('/turistas', [TuristaController::class, 'store']);
Route::get('/turistas/{id}', [TuristaController::class, 'show']);
Route::put('/turistas/{id}', [TuristaController::class, 'update']);
Route::delete('/turistas/{id}', [TuristaController::class, 'destroy']);

// Servicios Routes
Route::get('/servicios', [ServicioController::class, 'index']);
Route::post('/servicios', [ServicioController::class, 'store']);
Route::get('/servicios/{id}', [ServicioController::class, 'show']);
Route::put('/servicios/{id}', [ServicioController::class, 'update']);
Route::delete('/servicios/{id}', [ServicioController::class, 'destroy']);

// Destino Routes
Route::get('/destino', [DestinoController::class, 'index']);
Route::post('/destino', [DestinoController::class, 'store']);
Route::get('/destino/{destino}', [DestinoController::class, 'show']);
Route::put('/destino/{destino}', [DestinoController::class, 'update']);
Route::patch('/destino/{destino}', [DestinoController::class, 'update']);
Route::delete('/destino/{destino}', [DestinoController::class, 'destroy']);

// Hoteles Routes
Route::get('hoteles', [HotelController::class, 'index']);
Route::post('hoteles', [HotelController::class, 'store']);
Route::put('hoteles/{id}', [HotelController::class, 'update']);
Route::delete('hoteles/{id}', [HotelController::class, 'destroy']);

// API Routes
Route::post('/api/vuelos', [APIController::class, 'buscarVuelos']);
Route::post('/api/hoteles', [APIController::class, 'buscarHoteles']);
Route::post('/api/actividades', [APIController::class, 'buscarActividades']);
Route::get('/api/clima', [APIController::class, 'obtenerClima']);
Route::post('/api/paquete-personalizado', [APIController::class, 'crearPaquetePersonalizado']);
Route::get('/api/tipo-cambio', [APIController::class, 'obtenerTipoCambio']);

// Buscador Routes
Route::get('/buscador', [BuscadorController::class, 'index']);
Route::post('/buscador/buscar', [BuscadorController::class, 'buscar']);
Route::post('/buscador/personalizar', [BuscadorController::class, 'personalizar']);

// CategoriaDestino Routes
Route::get('/categorias-destino', [CategoriaDestinoController::class, 'index']);
Route::post('/categorias-destino', [CategoriaDestinoController::class, 'store']);
Route::get('/categorias-destino/{id}', [CategoriaDestinoController::class, 'show']);
Route::put('/categorias-destino/{id}', [CategoriaDestinoController::class, 'update']);
Route::patch('/categorias-destino/{id}', [CategoriaDestinoController::class, 'update']);
Route::delete('/categorias-destino/{id}', [CategoriaDestinoController::class, 'destroy']);

// Comentario Routes
Route::get('/comentarios', [ComentarioController::class, 'index']);
Route::post('/comentarios', [ComentarioController::class, 'store']);
Route::get('/comentarios/{id}', [ComentarioController::class, 'show']);
Route::put('/comentarios/{id}', [ComentarioController::class, 'update']);
Route::patch('/comentarios/{id}', [ComentarioController::class, 'update']);
Route::delete('/comentarios/{id}', [ComentarioController::class, 'destroy']);

// Configuracion Routes
Route::get('/configuracion', [ConfiguracionController::class, 'index']);
Route::post('/configuracion', [ConfiguracionController::class, 'update']);

// Contacto Routes
Route::post('/contacto/send-message', [ContactoController::class, 'sendMessage']);

// Contrato Routes
Route::get('/contrato/{turistaId}/{paqueteId}', [ContratoController::class, 'generateContract']);

// Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'index']);

// Destino Routes
Route::get('/destinos', [DestinoController::class, 'index']);
Route::post('/destinos', [DestinoController::class, 'store']);
Route::get('/destinos/{destino}', [DestinoController::class, 'show']);
Route::put('/destinos/{destino}', [DestinoController::class, 'update']);
Route::patch('/destinos/{destino}', [DestinoController::class, 'update']);
Route::delete('/destinos/{destino}', [DestinoController::class, 'destroy']);

// Factura Routes
Route::get('/facturas', [FacturaController::class, 'index']);
Route::post('/facturas', [FacturaController::class, 'store']);
Route::get('/facturas/{id}', [FacturaController::class, 'show']);
Route::get('/facturas/{id}/pdf', [FacturaController::class, 'generatePdf']);

// Home Routes
Route::get('/home', [HomeController::class, 'index']);

// Hotel Routes
Route::get('/hoteles', [HotelController::class, 'index']);
Route::post('hoteles', [HotelController::class, 'store']);
Route::get('/hoteles/{id}', [HotelController::class, 'show']);
Route::put('hoteles/{id}', [HotelController::class, 'update']);
Route::delete('hoteles/{id}', [HotelController::class, 'destroy']);

// ImagenDestino Routes
Route::get('/imagenes-destino', [ImagenDestinoController::class, 'index']);
Route::post('/imagenes-destino', [ImagenDestinoController::class, 'store']);
Route::get('/imagenes-destino/{id}', [ImagenDestinoController::class, 'show']);
Route::put('/imagenes-destino/{id}', [ImagenDestinoController::class, 'update']);
Route::patch('/imagenes-destino/{id}', [ImagenDestinoController::class, 'update']);
Route::delete('/imagenes-destino/{id}', [ImagenDestinoController::class, 'destroy']);

// Itinerario Routes
Route::get('/itinerarios', [ItinerarioController::class, 'index']);
Route::get('/itinerarios/{id}', [ItinerarioController::class, 'show']);

// ItinerarioPersonalizado Routes
Route::get('/itinerarios-personalizados', [ItinerarioPersonalizadoController::class, 'index']);
Route::post('/itinerarios-personalizados', [ItinerarioPersonalizadoController::class, 'store']);
Route::get('/itinerarios-personalizados/{id}', [ItinerarioPersonalizadoController::class, 'show']);
Route::put('/itinerarios-personalizados/{id}', [ItinerarioPersonalizadoController::class, 'update']);
Route::patch('/itinerarios-personalizados/{id}', [ItinerarioPersonalizadoController::class, 'update']);
Route::delete('/itinerarios-personalizados/{id}', [ItinerarioPersonalizadoController::class, 'destroy']);
Route::post('/itinerarios-personalizados/generar', [ItinerarioPersonalizadoController::class, 'generatePersonalizedItinerary']);

// MetodoPago Routes
Route::get('/metodos-pago', [MetodoPagoController::class, 'index']);
Route::post('/metodos-pago', [MetodoPagoController::class, 'store']);
Route::get('/metodos-pago/{id}', [MetodoPagoController::class, 'show']);
Route::put('/metodos-pago/{id}', [MetodoPagoController::class, 'update']);
Route::patch('/metodos-pago/{id}', [MetodoPagoController::class, 'update']);
Route::delete('/metodos-pago/{id}', [MetodoPagoController::class, 'destroy']);

// Notificacion Routes
Route::get('/notificaciones', [NotificacionController::class, 'index']);
Route::put('/notificaciones/{id}/read', [NotificacionController::class, 'markAsRead']);

// Pago Routes
Route::get('/pagos', [PagoController::class, 'index']);
Route::post('/pagos', [PagoController::class, 'store']);
Route::get('/pagos/{id}', [PagoController::class, 'show']);
Route::post('/pagos/stripe/payment-intent', [PagoController::class, 'createPaymentIntent']);
Route::post('/pagos/mercadopago/preference', [PagoController::class, 'createMercadoPagoPreference']);
Route::post('/pagos/paypal/order', [PagoController::class, 'createPayPalOrder']);
Route::post('/pagos/paypal/order/{orderId}/capture', [PagoController::class, 'capturePayPalOrder']);

// Paquete Routes
Route::get('/paquetes', [PaqueteController::class, 'index']);
Route::post('/paquetes', [PaqueteController::class, 'store']);
Route::get('/paquetes/{id}', [PaqueteController::class, 'show']);
Route::put('/paquetes/{id}', [PaqueteController::class, 'update']);
Route::patch('/paquetes/{id}', [PaqueteController::class, 'update']);
Route::delete('/paquetes/{id}', [PaqueteController::class, 'destroy']);

// Perfil Routes
Route::get('/perfil/{id}', [PerfilController::class, 'show']);
Route::put('/perfil/{id}', [PerfilController::class, 'update']);

// Permiso Routes
Route::get('/permisos', [PermisoController::class, 'index']);
Route::post('/permisos', [PermisoController::class, 'store']);
Route::get('/permisos/{id}', [PermisoController::class, 'show']);
Route::put('/permisos/{id}', [PermisoController::class, 'update']);
Route::patch('/permisos/{id}', [PermisoController::class, 'update']);
Route::delete('/permisos/{id}', [PermisoController::class, 'destroy']);

// Promocion Routes
Route::get('/promociones', [PromocionController::class, 'index']);
Route::post('/promociones', [PromocionController::class, 'store']);
Route::get('/promociones/{id}', [PromocionController::class, 'show']);
Route::put('/promociones/{id}', [PromocionController::class, 'update']);
Route::patch('/promociones/{id}', [PromocionController::class, 'update']);
Route::delete('/promociones/{id}', [PromocionController::class, 'destroy']);
Route::post('/promociones/aplicar', [PromocionController::class, 'aplicarDescuento']);

// Proveedor Routes
Route::get('/proveedores', [ProveedorController::class, 'index']);
Route::post('/proveedores', [ProveedorController::class, 'store']);
Route::get('/proveedores/{id}', [ProveedorController::class, 'show']);
Route::put('/proveedores/{id}', [ProveedorController::class, 'update']);
Route::patch('/proveedores/{id}', [ProveedorController::class, 'update']);
Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy']);

// RedSocial Routes
Route::get('/red-social', [RedSocialController::class, 'index']);
Route::post('/red-social/seguir/{id}', [RedSocialController::class, 'seguirUsuario']);
Route::post('/red-social/dejar-de-seguir/{id}', [RedSocialController::class, 'dejarDeSeguirUsuario']);

// Reserva Routes
Route::get('/reservas', [ReservaController::class, 'index']);
Route::post('/reservas', [ReservaController::class, 'store']);
Route::get('/reservas/{id}', [ReservaController::class, 'show']);
Route::put('/reservas/{id}', [ReservaController::class, 'update']);
Route::patch('/reservas/{id}', [ReservaController::class, 'update']);
Route::delete('/reservas/{id}', [ReservaController::class, 'destroy']);

// Rol Routes
Route::get('/roles', [RolController::class, 'index']);
Route::post('/roles', [RolController::class, 'store']);
Route::get('/roles/{id}', [RolController::class, 'show']);
Route::put('/roles/{id}', [RolController::class, 'update']);
Route::patch('/roles/{id}', [RolController::class, 'update']);
Route::delete('/roles/{id}', [RolController::class, 'destroy']);

// RolePermission Routes
Route::get('/roles/{roleId}/permisos', [RolePermissionController::class, 'getRolePermissions']);
Route::post('/roles/{roleId}/permisos', [RolePermissionController::class, 'assignPermission']);
Route::delete('/roles/{roleId}/permisos', [RolePermissionController::class, 'revokePermission']);

// Soporte Routes
Route::get('/soportes', [SoporteController::class, 'index']);
Route::post('/soportes', [SoporteController::class, 'store']);
Route::get('/soportes/{id}', [SoporteController::class, 'show']);
Route::put('/soportes/{id}', [SoporteController::class, 'update']);
Route::patch('/soportes/{id}', [SoporteController::class, 'update']);
Route::delete('/soportes/{id}', [SoporteController::class, 'destroy']);

// Suscripcion Routes
Route::get('/suscripciones', [SuscripcionController::class, 'index']);
Route::post('/suscripciones', [SuscripcionController::class, 'store']);
Route::get('/suscripciones/{id}', [SuscripcionController::class, 'show']);
Route::put('/suscripciones/{id}', [SuscripcionController::class, 'update']);
Route::patch('/suscripciones/{id}', [SuscripcionController::class, 'update']);
Route::delete('/suscripciones/{id}', [SuscripcionController::class, 'destroy']);

// TipoPaquete Routes
Route::get('/tipos-paquete', [TipoPaqueteController::class, 'index']);
Route::post('/tipos-paquete', [TipoPaqueteController::class, 'store']);
Route::get('/tipos-paquete/{id}', [TipoPaqueteController::class, 'show']);
Route::put('/tipos-paquete/{id}', [TipoPaqueteController::class, 'update']);
Route::patch('/tipos-paquete/{id}', [TipoPaqueteController::class, 'update']);
Route::delete('/tipos-paquete/{id}', [TipoPaqueteController::class, 'destroy']);

// TipoServicio Routes
Route::get('/tipos-servicio', [TipoServicioController::class, 'index']);
Route::post('/tipos-servicio', [TipoServicioController::class, 'store']);
Route::get('/tipos-servicio/{id}', [TipoServicioController::class, 'show']);
Route::put('/tipos-servicio/{id}', [TipoServicioController::class, 'update']);
Route::patch('/tipos-servicio/{id}', [TipoServicioController::class, 'update']);
Route::delete('/tipos-servicio/{id}', [TipoServicioController::class, 'destroy']);

// TransaccionExterna Routes
Route::get('/transacciones-externas', [TransaccionExternaController::class, 'index']);
Route::post('/transacciones-externas', [TransaccionExternaController::class, 'store']);
Route::get('/transacciones-externas/{id}', [TransaccionExternaController::class, 'show']);
Route::put('/transacciones-externas/{id}', [TransaccionExternaController::class, 'update']);
Route::patch('/transacciones-externas/{id}', [TransaccionExternaController::class, 'update']);
Route::delete('/transacciones-externas/{id}', [TransaccionExternaController::class, 'destroy']);

// Turista Routes
Route::get('/turistas', [TuristaController::class, 'index']);
Route::post('/turistas', [TuristaController::class, 'store']);
Route::get('/turistas/{id}', [TuristaController::class, 'show']);
Route::put('/turistas/{id}', [TuristaController::class, 'update']);
Route::delete('/turistas/{id}', [TuristaController::class, 'destroy']);

// User Routes
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::patch('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::get('/users/roles', [UserController::class, 'getRoles']);

// UserRole Routes
Route::get('/users/{userId}/roles', [UserRoleController::class, 'getUserRoles']);
Route::post('/users/{userId}/roles', [UserRoleController::class, 'assignRole']);
Route::delete('/users/{userId}/roles', [UserRoleController::class, 'revokeRole']);