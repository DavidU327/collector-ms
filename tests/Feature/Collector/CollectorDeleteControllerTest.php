<?php

namespace Tests\Feature\Collector;

use App\Models\Collector;
use App\Models\State;
use App\Models\User;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Support\CreatesCollectorMsSchema;
use Tests\TestCase;

class CollectorDeleteControllerTest extends TestCase
{
    use CreatesCollectorMsSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCollectorMsSchema();
        $this->withoutMiddleware([AuthenticateJwt::class, RoleMiddleware::class]);

        config()->set('filesystems.disks.azure.url', 'https://example.test');
        config()->set('filesystems.disks.azure.container', 'container');
        Storage::fake('azure');
    }

    public function test_delete_collector_marks_collector_and_user_as_deleted(): void
    {
        Storage::disk('azure')->put('documents/collectors-identification/id.pdf', 'id');
        Storage::disk('azure')->put('documents/collectors-driving_license/license.pdf', 'license');
        Storage::disk('azure')->put('images/collectors/photo.png', 'img');

        $user = new User();
        $user->forceFill([
            'name' => 'Delete Collector User',
            'phone' => '3006060606',
            'identification' => '333666999',
            'type_identification_id' => 1,
            'rol_id' => 3,
            'email' => 'delete.collector@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => State::where('name', State::ENABLED)->value('id'),
            'image' => 'https://example.test/container/images/collectors/photo.png',
        ]);
        $user->save();

        $collector = new Collector();
        $collector->forceFill([
            'user_id' => $user->id,
            'state_id' => State::where('name', State::ENABLED)->value('id'),
            'identification_document' => 'https://example.test/container/documents/collectors-identification/id.pdf',
            'driving_license_document' => 'https://example.test/container/documents/collectors-driving_license/license.pdf',
        ]);
        $collector->save();

        $response = $this->deleteJson("/api/deleteCollector/{$collector->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Recolector eliminado');

        $collector->refresh();
        $user->refresh();

        $deletedState = State::where('name', State::DELETE_USER)->value('id');

        $this->assertSame($deletedState, $collector->state_id);
        $this->assertSame($deletedState, $user->state_id);
        $this->assertNotNull($collector->deleted_at);
        $this->assertNotNull($user->deleted_at);
        $this->assertNull($collector->identification_document);
        $this->assertNull($collector->driving_license_document);
        $this->assertNull($user->image);
    }
}
