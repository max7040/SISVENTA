<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('ventas.index')
        : redirect()->route('login');
});

// Breeze usa /dashboard; lo redirigimos a ventas (dashboard “eliminado” para el usuario)
Route::get('/dashboard', function () {
    return redirect()->route('ventas.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Perfil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ====== SISVENTA ======

    // Ventas (admin y vendedor) - historial + ver
    Route::get('ventas', [VentaController::class, 'index'])
        ->name('ventas.index')
        ->middleware('role:admin,vendedor');

    Route::get('ventas/create', [VentaController::class, 'create'])
        ->name('ventas.create')
        ->middleware('role:admin,vendedor');

    Route::post('ventas', [VentaController::class, 'store'])
        ->name('ventas.store')
        ->middleware('role:admin,vendedor');

    // Acciones sobre venta (finalizar / pdf)
    Route::post('ventas/{venta}/finalizar', [VentaController::class, 'finalizar'])
        ->name('ventas.finalizar')
        ->middleware('role:admin,vendedor');

    Route::get('ventas/{venta}/pdf', [VentaController::class, 'pdf'])
        ->name('ventas.pdf')
        ->middleware('role:admin,vendedor');

    // Ver venta (solo cerradas, lo controlas en el controller)
    Route::get('ventas/{venta}', [VentaController::class, 'show'])
        ->name('ventas.show')
        ->middleware('role:admin,vendedor');

    // (Si ya no vas a usar agregar items porque no habrá ventas abiertas, puedes borrar esto)
    Route::post('ventas/{venta}/items', [VentaController::class, 'addItem'])
        ->name('ventas.items.add')
        ->middleware('role:admin,vendedor');

    // Clientes (solo admin)
    Route::resource('clientes', ClienteController::class)
        ->middleware('role:admin');

    // Productos (solo admin)
    Route::resource('productos', ProductoController::class)
        ->middleware('role:admin');

    // Activar/Desactivar producto (solo admin)
    Route::patch('productos/{producto}/toggle', [ProductoController::class, 'toggle'])
        ->name('productos.toggle')
        ->middleware('role:admin');
});

require __DIR__.'/auth.php';
