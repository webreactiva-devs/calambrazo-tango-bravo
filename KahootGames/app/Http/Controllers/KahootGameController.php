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
        // Default values
        $order_by = 'nombre_concurso';
        $order = 'asc';
        $search_by_name = '';

        // Number of items per page
        $itemsPerPage = config('kahoot.pagination');

        $kahoot_games = KahootGame::orderBy($order_by, $order)
            ->paginate($itemsPerPage);

        return view('kahoot-games.index',
            compact('kahoot_games', 'order_by', 'order', 'search_by_name')
        );
    }


    public function filtered(Request $request)
    {
        // Values
        $order_by = $request->post('order_by', 'nombre_concurso');
        $order = $request->post('order', 'asc');
        $page = $request->post('page', 1);
        $search_by_name = $request->post('search_by_name');

        // Number of items per page
        $itemsPerPage = config('kahoot.pagination');

        // Get the games ordered, with pagination and search (if any)
        $kahoot_games = KahootGame::when($search_by_name, function ($query, $search_by_name) {
            return $query->where('nombre_concurso', 'like', "%$search_by_name%");
        })
            ->orderBy($order_by, $order)
            ->paginate($itemsPerPage, ['*'], 'page', $page);

        return view('kahoot-games.index',
            compact('kahoot_games', 'order_by', 'order', 'search_by_name'));
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
            'contest_name' => 'required|string',
            'event_date' => 'required|date',
            'participants' => 'required|integer',
        ]);

        KahootGame::create([
            'nombre_concurso' => $validated['contest_name'],
            'fecha_celebracion' => $validated['event_date'],
            'numero_participantes' => $validated['participants'],
        ]);
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
            'contest_name' => 'required|string',
            'event_date' => 'required|date',
            'participants' => 'required|integer',
        ]);

        $kahoot_game->update([
            'nombre_concurso' => $validated['contest_name'],
            'fecha_celebracion' => $validated['event_date'],
            'numero_participantes' => $validated['participants'],
        ]);
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
