<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('id', 'desc')->paginate(10);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = ['artesanias', 'textiles', 'gastronomia'];
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'in:artesanias,textiles,gastronomia'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        Producto::create($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = ['artesanias', 'textiles', 'gastronomia'];
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'in:artesanias,textiles,gastronomia'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $producto->update($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->activo = false;
        $producto->save();

        return redirect()->route('productos.index')
            ->with('success', 'Producto desactivado (porque ya puede estar en ventas registradas).');
    }

    public function toggle(Producto $producto)
    {
        $producto->activo = !$producto->activo;
        $producto->save();

        return back()->with('success', 'Estado del producto actualizado.');
    }


}
