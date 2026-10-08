<?php

namespace Tests\Feature\Collector;

use App\Models\Collector;
use App\Models\State;
use App\Models\User;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Tests\Feature\Support\CreatesCollectorMsSchema;
use Tests\TestCase;

class CollectorChangeStateControllerTest extends TestCase
{
    use CreatesCollectorMsSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCollectorMsSchema();
        $this->withoutMiddleware([AuthenticateJwt::class, RoleMiddleware::class]);
    }

    public function test_change_state_toggles_collector_between_enabled_and_disabled(): void
    {
        $user = new User();
        $user->forceFill([
            'name' => 'State Collector',
            'phone' => '3004040404',
            'identification' => '90909090',
            'type_identification_id' => 1,
            'rol_id' => 3,
            'email' => 'state.collector@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => State::where('name', State::ENABLED)->value('id'),
        ]);
        $user->save();

        $collector = new Collector();
        $collector->forceFill([
            'user_id' => $user->id,
            'state_id' => State::where('name', State::ENABLED)->value('id'),
        ]);
        $collector->save();

        $response = $this->getJson("/api/change_state/{$collector->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Cambio de estado correctamente');

        $collector->refresh();
        $this->assertSame(
            State::where('name', State::DISABLED)->value('id'),
            $collector->state_id
        );
    }
}
