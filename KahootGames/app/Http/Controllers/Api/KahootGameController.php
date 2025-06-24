<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
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
        $order_by = $request->query('order_by', 'nombre_concurso');
        $order = strtolower($request->query('order', 'asc'));
        $search = $request->query('search');
        $per_page = (int) $request->query('per_page', config('kahoot.api_pagination'));

        if (!in_array($order, ['asc', 'desc'])) {
            return response()->json([
                'message' => 'The "order" parameter must be "asc" or "desc".'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $query = KahootGame::query();
        if ($search) {
            $query->where('nombre_concurso', 'like', "%$search%");
        }

        $kahoots = $query->orderBy($order_by, $order)->paginate($per_page);


        return response()->json([
            'message' => 'List of Kahoot games.',
            'data' => $kahoots
        ], Response::HTTP_OK);
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
                ], Response::HTTP_CONFLICT);
            }

            $kahoot = KahootGame::create([
                'nombre_concurso' => $validated['contest_name'],
                'fecha_celebracion' => \Carbon\Carbon::createFromFormat('d-m-Y', $validated['event_date']),
                'numero_participantes' => $validated['participants'],
            ]);

            return response()->json([
                'message' => 'Kahoot created.',
                'data' => $kahoot
            ], Response::HTTP_CREATED);
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid input.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
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
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Kahoot game found.',
            'data' => $kahoot
        ], Response::HTTP_OK);
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
                ], Response::HTTP_NOT_FOUND);
            }

            if (KahootGame::where('nombre_concurso', $validated['contest_name'])
                ->where('id', '!=', $id)
                ->exists()) {
                return response()->json([
                    'message' => 'A Kahoot game with this name already exists.'
                ], Response::HTTP_CONFLICT);
            }

            $kahoot->update([
                'nombre_concurso' => $validated['contest_name'],
                'fecha_celebracion' => \Carbon\Carbon::createFromFormat('d-m-Y', $validated['event_date']),
                'numero_participantes' => $validated['participants'],
            ]);

            return response()->json([
                'message' => 'Kahoot game updated.',
                'data' => $kahoot
            ], Response::HTTP_OK);
        }
        catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid input.',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Delete the specified Kahoot game.
     */
    public function destroy(string $id)
    {
        $kahoot = KahootGame::find($id);

        if (!$kahoot) {
            return response()->json([
                'message' => 'Kahoot game not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        $kahoot->delete();

        return response()->json([
            'message' => 'Kahoot game deleted'
        ], Response::HTTP_OK);
    }
}
