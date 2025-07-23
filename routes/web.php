<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PruebaXdebugController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $mensaje = "Hola desde el debugger"; // ← aquí el breakpoint
    return view('welcome');
});

Route::get('/debug-test', [PruebaXdebugController::class, 'index']);

Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/upload', function (\Illuminate\Http\Request $request) {
    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('uploads', 's3');

        // URL completa del archivo en S3
        $url = Storage::disk('s3')->url($path);

        return "Archivo subido a S3: <a href='$url' target='_blank'>$url</a>";
    }

    return "No se ha subido ningún archivo.";
})->name('upload');