<?php

use App\Http\Controllers\KahootGameController;
use App\Models\KahootGame;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use function Pest\Laravel\actingAs;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Helper → crea un controller ya resuelto por el contenedor
 */
function controller(): KahootGameController
{
    return app(KahootGameController::class);
}

/**
 * Helper → construye un Request idéntico al que llegaría por HTTP
 */
function makeRequest(string $method, array $data = [], array $query = []): Request
{
    return Request::create('/', $method, [...$query, ...$data]);
}

/**
 * 1. Acceso sin sesión ─ esperamos que el middleware lance AuthException
 */
test('redirect to login if no session', function () {
    $this->expectException(\Illuminate\Auth\AuthenticationException::class);

    // Llamamos al método index sin usuario autenticado
    controller()->index(makeRequest('GET'));
});

/**
 * 2. Listado (index)
 */
test('show list of kahoot games', function () {
    actingAs(User::factory()->create());
    $kahoot = KahootGame::factory()->create();

    $response = controller()->index(makeRequest('GET'));

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('kahoot-games.index')
        ->and($response->render())->toContain($kahoot->nombre_concurso);
});

/**
 * 3. Formulario create
 */
test('display the create form', function () {
    actingAs(User::factory()->create());

    $response = controller()->create();

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('kahoot-games.create')
        ->and($response->render())->toContain('Guardar');
});

/**
 * 4. Almacenar nuevo registro (store)
 */
test('create a new kahoot game', function () {
    actingAs(User::factory()->create());

    $kahoot  = KahootGame::factory()->make();   // no persiste
    $payload = [
        'contest_name' => $kahoot->nombre_concurso,
        'event_date'   => $kahoot->fecha_celebracion,
        'participants' => $kahoot->numero_participantes,
    ];

    $response = controller()->store(makeRequest('POST', $payload));

    expect($response->isRedirect(route('kahoot-games.index')))->toBeTrue();
    $this->assertDatabaseHas('kahoot_games', [
        'nombre_concurso'      => $kahoot->nombre_concurso,
        'numero_participantes' => $kahoot->numero_participantes,
    ]);
});

/**
 * 5. Mostrar un kahoot concreto (show)
 */
test('show kahoot game data', function () {
    actingAs(User::factory()->create());
    $kahoot = KahootGame::factory()->create();

    $response = controller()->show($kahoot);

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('kahoot-games.show')
        ->and($response->render())->toContain($kahoot->nombre_concurso);
});

/**
 * 6. Formulario edición (edit)
 */
test('show edit form', function () {
    actingAs(User::factory()->create());
    $kahoot = KahootGame::factory()->create();

    $response = controller()->edit($kahoot);

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('kahoot-games.edit')
        ->and($response->render())
        ->toContain('value="'.$kahoot->nombre_concurso.'"');
});

/**
 * 7. Actualizar registro existente (update)
 */
test('update a kahoot game', function () {
    actingAs(User::factory()->create());

    $kahoot = KahootGame::factory()->create();  // existente
    $nuevo  = KahootGame::factory()->make();    // datos nuevos

    $payload = [
        'contest_name' => $nuevo->nombre_concurso,
        'event_date'   => $nuevo->fecha_celebracion,
        'participants' => $nuevo->numero_participantes,
    ];

    $response = controller()->update(makeRequest('PUT', $payload), $kahoot);

    expect($response->isRedirect(route('kahoot-games.index')))->toBeTrue();
    $this->assertDatabaseHas('kahoot_games', [
        'id'                   => $kahoot->id,
        'nombre_concurso'      => $nuevo->nombre_concurso,
        'numero_participantes' => $nuevo->numero_participantes,
    ]);
});

/**
 * 8. Eliminar registro (destroy)
 */
test('delete a kahoot game', function () {
    actingAs(User::factory()->create());
    $kahoot = KahootGame::factory()->create();

    $response = controller()->destroy($kahoot);

    expect($response->isRedirect(route('kahoot-games.index')))->toBeTrue();
    $this->assertDatabaseMissing('kahoot_games', ['id' => $kahoot->id]);
});
