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
     * Display a listing of Kahoot games.
     */
    public function index(Request $request)
    {
        $order_by = $request->query('order_by');
        $order = strtolower($request->query('order'));

        if (!$order_by) {
            return response()->json([
                'message' => 'The "order_by" parameter is required.'
            ], 422);
        }

        if (!in_array($order, ['asc', 'desc'])) {
            return response()->json([
                'message' => 'The "order" parameter must be "asc" or "desc".'
            ], 422);
        }

        $per_page = (int) $request->query('per_page', config('kahoot.api_pagination'));
        $kahoots =  KahootGame::orderBy($order_by, $order)->paginate($per_page);

        return response()->json([
            'message' => 'List of Kahoot games.',
            'data' => $kahoots
        ], 200);
    }

    /**
     * Store a new Kahoot game.
     */
    public function store(Request $request)
    {

        try{

            $validated = $request->validate([
                'contest_name' => 'required|string|max:255',
                'event_date' => 'required|date_format:d-m-Y',
                'participants' => 'required|integer|min:1',
            ]);

            if (KahootGame::where('nombre_concurso', $validated['contest_name'])->exists()) {
                return response()->json([
                    'message' => 'A kahoot game with this name already exists.'
                ], 409);
            }

            $kahoot = KahootGame::create([
                'nombre_concurso' => $validated['contest_name'],
                'fecha_celebracion' => \Carbon\Carbon::createFromFormat('d-m-Y', $validated['event_date']),
                'numero_participantes' => $validated['participants'],
            ]);

            return response()->json([
                'message' => 'Kahoot created.',
                'data' => $kahoot
            ], 201);
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid input.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified Kahoot game.
     */
    public function show(string $id)
    {
        $kahoot = KahootGame::find($id);

        if (!$kahoot) {
            return response()->json([
                'message' => 'Kahoot game not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Kahoot game found.',
            'data' => $kahoot
        ], 200);
    }

    /**
     * Update the specified Kahoot game.
     */
    public function update(Request $request, string $id)
    {
        try{

            $validated = $request->validate([
                'contest_name' => 'required|string|max:255',
                'event_date' => 'required|date_format:d-m-Y',
                'participants' => 'required|integer|min:1',
            ]);

            $kahoot = KahootGame::find($id);
            if (!$kahoot) {
                return response()->json([
                    'message' => 'Kahoot game not found.'
                ], 404);
            }

            if (KahootGame::where('nombre_concurso', $validated['contest_name'])
                ->where('id', '!=', $id)
                ->exists()) {
                return response()->json([
                    'message' => 'A Kahoot game with this name already exists.'
                ], 409);
            }

            $kahoot->update([
                'nombre_concurso' => $validated['contest_name'],
                'fecha_celebracion' => \Carbon\Carbon::createFromFormat('d-m-Y', $validated['event_date']),
                'numero_participantes' => $validated['participants'],
            ]);

            return response()->json([
                'message' => 'Kahoot game updated.',
                'data' => $kahoot
            ], 200);
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid input.',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Remove the specified Kahoot game.
     */
    public function destroy(string $id)
    {
        $kahoot = KahootGame::find($id);

        if (!$kahoot) {
            return response()->json([
                'message' => 'Kahoot game not found.'
            ], 404);
        }

        $kahoot->delete();

        return response()->json([
            'message' => 'Kahoot game deleted'
        ], 200);
    }
}
