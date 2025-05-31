<?php

namespace App\Http\Controllers;

use App\Models\KahootGame;
use Illuminate\Http\Request;

class KahootGameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Valor por defecto de los parámetros de orden y dirección
        $ordenado_por = $request->post('ordenado_por', 'nombre_concurso');
        $orden = $request->post('orden', 'asc');
        $page = $request->post('page', 1);

        $porPagina = config('kahoot.pagination'); // Número de elementos por página

        // Obtener los juegos ordenados y con paginación
        $kahoot_games = KahootGame::orderBy($ordenado_por, $orden)
            ->paginate($porPagina,['*'], 'page', $page);

        return view('kahoot-games.index', compact('kahoot_games', 'ordenado_por', 'orden'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kahoot-games.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_concurso' => 'required|string',
            'fecha_celebracion' => 'required|date',
            'numero_participantes' => 'required|integer',
        ]);

        KahootGame::create($validated);
        return redirect()->route('kahoot-games.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(KahootGame $kahoot_game)
    {
        return view('kahoot-games.show', compact('kahoot_game'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KahootGame $kahoot_game)
    {
        return view('kahoot-games.edit', compact('kahoot_game'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KahootGame $kahoot_game)
    {
        $validated = $request->validate([
            'nombre_concurso' => 'required|string',
            'fecha_celebracion' => 'required|date',
            'numero_participantes' => 'required|integer',
        ]);

        $kahoot_game->update($validated);
        return redirect()->route('kahoot-games.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KahootGame $kahoot_game)
    {
        $kahoot_game->delete();
        return redirect()->route('kahoot-games.index');
    }
}
