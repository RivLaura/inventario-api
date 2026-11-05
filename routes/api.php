<?php

use App\Http\Controllers\Api\ProductoController; 
use Illuminate\Support\Facades\Route; 
use App\Models\Producto;
use Illuminate\Http\Request;

Route::apiResource('productos', ProductoController::class); 

Route::get('/interno/productos/{producto}', function (
    Request $request,
    Producto $producto
) {
    $clave = (string) env('GRPC_INTERNAL_KEY', '');

    abort_if(
        $clave === '' ||
        !hash_equals($clave, (string) $request->header('X-Internal-Key')),
        403
    );

    return response()->json($producto);
});