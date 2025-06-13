<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KahootGame;
use Illuminate\Validation\ValidationException;

class KahootGameController extends Controller
{
    public function __construct()
    {
        // Protected with OAuth2
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kahoots = KahootGame::all();

        return response()->json([
            'message' => 'List of kahoots',
            'data' => $kahoots
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try{
            $request->merge([
                'event_date' => \Carbon\Carbon::createFromFormat('d-m-Y', $request->event_date)->format('Y-m-d')
            ]);

            $validated = $request->validate([
                'contest_name' => 'required|string|max:255',
                'event_date' => 'required|date',
                'participants' => 'required|integer|min:1',
            ]);

            if (KahootGame::where('nombre_concurso', $validated['contest_name'])->exists()) {
                return response()->json([
                    'message' => 'A kahoot with this name already exists'
                ], 409);
            }

            $kahoot = KahootGame::create([
                'nombre_concurso' => $validated['contest_name'],
                'fecha_celebracion' => $validated['event_date'],
                'numero_participantes' => $validated['participants'],
            ]);

            return response()->json([
                'message' => 'Kahoot created',
                'data' => $kahoot
            ], 201);
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $e->errors()
            ], 422);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
