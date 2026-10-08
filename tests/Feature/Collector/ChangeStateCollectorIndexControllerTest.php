<?php

namespace Tests\Feature\Collector;

use App\Models\Collector;
use App\Models\State;
use App\Models\User;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Tests\Feature\Support\CreatesCollectorMsSchema;
use Tests\TestCase;

class ChangeStateCollectorIndexControllerTest extends TestCase
{
    use CreatesCollectorMsSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCollectorMsSchema();
        $this->withoutMiddleware([AuthenticateJwt::class, RoleMiddleware::class]);
    }

    public function test_admin_state_change_updates_collector_and_user_state(): void
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Admin State Collector',
            'phone' => '3005050505',
            'identification' => '12125555',
            'type_identification_id' => 1,
            'rol_id' => 3,
            'email' => 'admin.state.collector@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => State::where('name', State::PENDING_USER)->value('id'),
        ]);
        $user->save();

        $collector = new Collector();
        $collector->forceFill([
            'user_id' => $user->id,
            'state_id' => State::where('name', State::PENDING_USER)->value('id'),
        ]);
        $collector->save();

        $enabledId = State::where('name', State::ENABLED)->value('id');
        $response = $this->getJson("/api/change_state_collector/{$enabledId}/{$collector->id}");

        $response->assertOk();
        $response->assertJsonPath('data.state.id', $enabledId);

        $collector->refresh();
        $user->refresh();

        $this->assertSame($enabledId, $collector->state_id);
        $this->assertSame($enabledId, $user->state_id);
    }
}
