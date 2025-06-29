<?php

use App\Models\User;
use App\Models\KahootGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

/**
 * Helper function to authenticate a user through a web session
 */
function login(): void
{
    actingAs(User::factory()->create());
}

// Authentication
test('Redirect to login if no session', function () {
    $this->get('/kahoot-games')
        ->assertRedirect('/login');
});


// Index
test('Show list of Kahoot games', function () {
    login();
    $kahoot = KahootGame::factory()->create();

    $this->get('/kahoot-games')
        ->assertStatus(Response::HTTP_OK)
        ->assertSeeText($kahoot->nombre_concurso);
});


// Create (form)
test('Display the create form', function () {
    login();

    $this->get('/kahoot-games/create')
        ->assertOk()
        ->assertSee('Guardar');
});



test('Create a new kahoot game', function () {
    login();

    // Generate a model without save
    $kahoot = KahootGame::factory()->make();

    $payload = [
        'contest_name'  => $kahoot->nombre_concurso,
        'event_date'    => $kahoot->fecha_celebracion,
        'participants'  => $kahoot->numero_participantes,
    ];

    $this->post(route('kahoot-games.store'), $payload)
        ->assertRedirect(route('kahoot-games.index'))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('kahoot_games', [
        'nombre_concurso'       => $kahoot->nombre_concurso,
        'numero_participantes'  => $kahoot->numero_participantes,
    ]);
});


// Show
test('Show Kahoot game data', function () {
    login();
    $kahoot = KahootGame::factory()->create();

    $this->get("/kahoot-games/{$kahoot->id}")
        ->assertOk()
        ->assertSee($kahoot->nombre_concurso);
});


// Edit
test('Show edit form', function () {
    login();
    $kahoot = KahootGame::factory()->create();

    $this->get("/kahoot-games/{$kahoot->id}/edit")
        ->assertOk()
        ->assertSee('value="'.$kahoot->nombre_concurso.'"', false);
});


// Update
test('Update a Kahoot game', function () {
    login();

    // Already stored in the database
    $kahoot = KahootGame::factory()->create();

    // New data without saving
    $nuevo = KahootGame::factory()->make();

    $payload = [
        'contest_name'  => $nuevo->nombre_concurso,
        'event_date'    => $nuevo->fecha_celebracion,
        'participants'  => $nuevo->numero_participantes,
    ];

    $this->put("/kahoot-games/{$kahoot->id}", $payload)
        ->assertRedirect('/kahoot-games')
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('kahoot_games', [
        'id'                  => $kahoot->id,
        'nombre_concurso'     => $nuevo->nombre_concurso,
        'numero_participantes'=> $nuevo->numero_participantes,
    ]);
});

// Delete
test('Delete a kahoot game', function () {
    login();
    $kahoot = KahootGame::factory()->create();

    $this->delete("/kahoot-games/{$kahoot->id}")
        ->assertRedirect('/kahoot-games');

    $this->assertDatabaseMissing('kahoot_games', ['id' => $kahoot->id]);
});

