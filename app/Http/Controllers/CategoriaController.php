<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    /**
     * Muestra la lista de categorías con sus subcategorías.
     */
    public function index()
    {
        $categorias = Categoria::with('subcategorias')->orderBy('nom')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * Crea una nueva categoría.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255', 'unique:categorias,nom'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            Categoria::create($validated);
            DB::commit();
            return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la categoría: ' . $e->getMessage()]);
        }
    }

    /**
     * Devuelve el partial de edición de categoría.
     */
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('admin.categorias.partial.editar_categoria', compact('categoria'));
    }

    /**
     * Actualiza una categoría.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'min:2', 'max:255', 'unique:categorias,nom,' . $id],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            $categoria = Categoria::findOrFail($id);
            $categoria->update($validated);
            DB::commit();
            return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la categoría: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina una categoría y sus subcategorías.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $categoria = Categoria::findOrFail($id);
            // Eliminar subcategorías asociadas
            $categoria->subcategorias()->delete();
            $categoria->delete();
            DB::commit();
            return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar la categoría: ' . $e->getMessage()]);
        }
    }

    // ========== SUBCATEGORÍAS ==========

    /**
     * Crea una nueva subcategoría.
     */
    public function storeSubcategoria(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            Subcategoria::create($validated);
            DB::commit();
            return redirect()->route('admin.categorias.index')->with('success', 'Subcategoría creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la subcategoría: ' . $e->getMessage()]);
        }
    }

    /**
     * Devuelve el partial de edición de subcategoría.
     */
    public function editSubcategoria($id)
    {
        $subcategoria = Subcategoria::findOrFail($id);
        $categorias = Categoria::orderBy('nom')->get();
        return view('admin.categorias.partial.editar_subcategoria', compact('subcategoria', 'categorias'));
    }

    /**
     * Actualiza una subcategoría.
     */
    public function updateSubcategoria(Request $request, $id)
    {
        $validated = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            $subcategoria = Subcategoria::findOrFail($id);
            $subcategoria->update($validated);
            DB::commit();
            return redirect()->route('admin.categorias.index')->with('success', 'Subcategoría actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la subcategoría: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina una subcategoría.
     */
    public function destroySubcategoria($id)
    {
        DB::beginTransaction();
        try {
            $subcategoria = Subcategoria::findOrFail($id);
            $subcategoria->delete();
            DB::commit();
            return redirect()->route('admin.categorias.index')->with('success', 'Subcategoría eliminada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar la subcategoría: ' . $e->getMessage()]);
        }
    }
}
