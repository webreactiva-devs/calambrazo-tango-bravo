<?php

use App\Http\Controllers\KahootGameController;
use App\Models\KahootGame;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
 * 1. Access without a session — the middleware should throw an AuthenticationException
 */
test('Redirect to login if no session', function () {
    $this->expectException(\Illuminate\Auth\AuthenticationException::class);

    $request  = makeRequest('GET');
    // Load the auth middleware
    /** @var \Illuminate\Auth\Middleware\Authenticate $authMw */
    $authMw = app(\Illuminate\Auth\Middleware\Authenticate::class);

    // Call the index method without a logged-in user
    $authMw->handle(
        $request,
        fn ($req) => controller()->index($req) // next step
    );
});

/**
 * 2. List
 */
test('Show list of Kahoot games', function () {
    actingAs(User::factory()->create());
    $kahoot = KahootGame::factory()->create();

    $response = controller()->index(makeRequest('GET'));

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('kahoot-games.index')
        ->and($response->render())->toContain($kahoot->nombre_concurso);
});

/**
 * 3. Create form
 */
test('Display the create form', function () {
    actingAs(User::factory()->create());

    $response = controller()->create();

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('kahoot-games.create')
        ->and($response->render())->toContain('Guardar');
});

/**
 * 4. Store/save new record
 */
test('Create a new Kahoot game', function () {
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
 * 5. Show a specific Kahoot
 */
test('Show Kahoot game data', function () {
    actingAs(User::factory()->create());
    $kahoot = KahootGame::factory()->create();

    $response = controller()->show($kahoot);

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('kahoot-games.show')
        ->and($response->render())->toContain($kahoot->nombre_concurso);
});

/**
 * 6. Edit form
 */
test('Show edit form', function () {
    actingAs(User::factory()->create());
    $kahoot = KahootGame::factory()->create();

    $response = controller()->edit($kahoot);

    expect($response)->toBeInstanceOf(View::class)
        ->and($response->name())->toBe('kahoot-games.edit')
        ->and($response->render())
        ->toContain('value="'.$kahoot->nombre_concurso.'"');
});

/**
 * 7. Update an existing record
 */
test('Update a Kahoot game', function () {
    actingAs(User::factory()->create());

    $kahoot = KahootGame::factory()->create();  // exits
    $nuevo  = KahootGame::factory()->make();    // new data

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
 * 8. Destroy record
 */
test('Delete a kahoot game', function () {
    actingAs(User::factory()->create());
    $kahoot = KahootGame::factory()->create();

    $response = controller()->destroy($kahoot);

    expect($response->isRedirect(route('kahoot-games.index')))->toBeTrue();
    $this->assertDatabaseMissing('kahoot_games', ['id' => $kahoot->id]);
});
