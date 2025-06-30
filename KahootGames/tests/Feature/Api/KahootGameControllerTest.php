<?php

use App\Models\User;
use App\Models\KahootGame;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

/** helper to authenticate Passport via */
function apiLogin(): void
{
    Passport::actingAs(
        User::factory()->create()
    );
}


test('Refuse requests without token', function () {
    $this->getJson('/api/kahoot-games')
        ->assertStatus(Response::HTTP_UNAUTHORIZED);
});


test('Get list of Kahoot games', function () {
    apiLogin();

    // Prevent false positives
    KahootGame::factory()->count(2)->create();

    $this->getJson('/api/kahoot-games')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'data' => [
                'current_page',
                'data' => [[
                    'id',
                    'nombre_concurso',
                    'fecha_celebracion',
                    'numero_participantes',
                    'created_at',
                    'updated_at',
                ]],
                'first_page_url',
                'last_page',
                'links',
                'per_page',
                'total',
            ],
        ]);
});


test('Returns a 422 if the data is invalid when creating a new Kahoot', function () {
    apiLogin();

    /*
     * Payload that violates the rules:
        - empty name
        - invalid date format
        - fewer than 1 participant
     */
    $payload = [
        'contest_name' => '',
        'event_date'   => '2025-01-01',
        'participants' => 0,
    ];

    $this->postJson('/api/kahoot-games', $payload)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'message' => 'Invalid input.',
        ])
        ->assertJsonStructure([
            'errors' => [
                'contest_name',
                'event_date',
                'participants',
            ],
        ]);

    // no se crea ningún registro con ese nombre
    $this->assertDatabaseCount('kahoot_games', 0);
});


test('Create a new Kahoot game', function () {
    apiLogin();
    $fake = KahootGame::factory()->make();

    $payload = [
        'contest_name' => $fake->nombre_concurso,
        'event_date'   => Carbon::parse($fake->fecha_celebracion)->format('d-m-Y'),
        'participants' => $fake->numero_participantes,
    ];

    $this->postJson('/api/kahoot-games', $payload)
         ->assertStatus(Response::HTTP_CREATED)
         ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'nombre_concurso',
                'fecha_celebracion',
                'numero_participantes',
            ],
        ])
        ->assertJson(fn ($json) =>
            $json->where('message', 'Kahoot created.')
                ->where('data.nombre_concurso',      $fake->nombre_concurso)
                ->where('data.numero_participantes', $fake->numero_participantes)
                ->where('data.fecha_celebracion', fn ($fecha) =>
                    str_starts_with($fecha, Carbon::parse($fake->fecha_celebracion)->toDateString())
                )
                ->hasAll('data.id', 'data.created_at', 'data.updated_at')
        );

    // BD
    $this->assertDatabaseHas('kahoot_games', [
        'nombre_concurso'      => $fake->nombre_concurso,
        'numero_participantes' => $fake->numero_participantes,
    ]);


    // Date: a record already exists for that same day, regardless of the time
    $this->assertTrue(
        KahootGame::where('nombre_concurso', $fake->nombre_concurso)
            ->whereDate('fecha_celebracion', $fake->fecha_celebracion)
            ->exists(),
        'The event date doesn’t match.'
    );
});


test('Returns a 404 if the Kahoot you’re trying to display doesn’t exist', function () {
    apiLogin();
    $uuid = Str::uuid();

    $this->getJson("/api/kahoot-games/{$uuid}")
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJson(['message' => 'Kahoot game not found.']);
});


test('Get Kahoot data', function () {
    apiLogin();
    $k = KahootGame::factory()->create();

    $this->getJson("/api/kahoot-games/{$k->id}")
        ->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'message' => 'Kahoot game found.',
            'data' => [
                'id'                   => $k->id,
                'nombre_concurso'      => $k->nombre_concurso,
                'fecha_celebracion'    => $k->fecha_celebracion,
                'numero_participantes' => $k->numero_participantes,
            ],
        ]);
});


test('Returns a 404 if you try to update a Kahoot game that doesn’t exist', function () {
    apiLogin();
    $uuid = Str::uuid();

    $payload = [
        'contest_name' => 'Nuevo nombre',
        'event_date'   => '01-01-2026',
        'participants' => 99,
    ];

    $this->putJson("/api/kahoot-games/{$uuid}", $payload)
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJson(['message' => 'Kahoot game not found.']);
});


test('Returns a 422 if the data is invalid when updating a Kahoot', function () {
    apiLogin();
    // Record not found
    $kahoot = KahootGame::factory()->create();

    // Payload that violates every validation rule
    $payload = [
        'contest_name' => '',
        'event_date'   => '2025-01-01', // incorrect format
        'participants' => 0,
    ];

    $this->putJson("/api/kahoot-games/{$kahoot->id}", $payload)
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJson([
            'message' => 'Invalid input.',
        ])
        ->assertJsonStructure([
            'errors' => [
                'contest_name',
                'event_date',
                'participants',
            ],
        ]);

    // The record remained unchanged
    $this->assertDatabaseHas('kahoot_games', [
        'id'                   => $kahoot->id,
        'nombre_concurso'      => $kahoot->nombre_concurso,
        'fecha_celebracion'    => $kahoot->fecha_celebracion,
        'numero_participantes' => $kahoot->numero_participantes,
    ]);
});


test('Update a Kahoot game', function () {
    apiLogin();
    $k     = KahootGame::factory()->create();
    $nuevo = KahootGame::factory()->make();

    $payload = [
        'contest_name' => $nuevo->nombre_concurso,
        'event_date'   => Carbon::parse($nuevo->fecha_celebracion)->format('d-m-Y'),
        'participants' => $nuevo->numero_participantes,
    ];

    $this->putJson("/api/kahoot-games/{$k->id}", $payload)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'nombre_concurso',
                'fecha_celebracion',
                'numero_participantes',
            ]
        ])
        ->assertJson(fn ($json) =>
        $json->where('message', 'Kahoot game updated.')
            ->where('data.id', $k->id)
            ->where('data.nombre_concurso',      $nuevo->nombre_concurso)
            ->where('data.numero_participantes', $nuevo->numero_participantes)
            ->where('data.fecha_celebracion', fn ($fecha) =>
            str_starts_with($fecha, Carbon::parse($nuevo->fecha_celebracion)->toDateString())
            )
            ->hasAll('data.created_at', 'data.updated_at')
        );

    // BD
    $this->assertDatabaseHas('kahoot_games', [
        'id'                   => $k->id,
        'nombre_concurso'      => $nuevo->nombre_concurso,
        'numero_participantes' => $nuevo->numero_participantes,
    ]);

    // Date: a record already exists for that same day, regardless of the time
    $this->assertTrue(
        KahootGame::where('nombre_concurso', $nuevo->nombre_concurso)
            ->whereDate('fecha_celebracion', $nuevo->fecha_celebracion)
            ->exists(),
        'The event date doesn’t match.'
    );
});


test('Returns a 404 if you try to delete a Kahoot game that doesn’t exist', function () {
    apiLogin();
    $uuid = Str::uuid();   // id que no está en la BD

    $this->deleteJson("/api/kahoot-games/{$uuid}")
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJson(['message' => 'Kahoot game not found.']);
});


test('Delete a Kahoot game', function () {
    apiLogin();
    $k = KahootGame::factory()->create();

    $this->deleteJson("/api/kahoot-games/{$k->id}")
        ->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'message' => 'Kahoot game deleted.',
        ]);

    $this->assertDatabaseMissing('kahoot_games', ['id' => $k->id]);
});

