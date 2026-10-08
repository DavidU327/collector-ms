<?php

namespace Tests\Feature\Collector;

use App\Models\Collector;
use App\Models\State;
use App\Models\User;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Support\CreatesCollectorMsSchema;
use Tests\TestCase;

class CollectorUpdateControllerTest extends TestCase
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

    public function test_update_backoffice_updates_user_fields(): void
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Old Collector',
            'phone' => '3001010101',
            'identification' => '12121212',
            'type_identification_id' => 1,
            'rol_id' => 3,
            'email' => 'old.collector@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => State::where('name', State::ENABLED)->value('id'),
        ]);
        $user->save();

        $collector = new Collector();
        $collector->forceFill([
            'user_id' => $user->id,
            'state_id' => State::where('name', State::PENDING_USER)->value('id'),
        ]);
        $collector->save();

        $response = $this->patchJson("/api/collector/{$collector->id}", [
            'name' => 'New Collector',
            'phone' => '3110000000',
            'email' => 'new.collector@example.com',
            'identification' => '56565656',
            'type_identification' => 1,
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Se ha actualizado el recolector correctamente');

        $user->refresh();
        $this->assertSame('New Collector', $user->name);
        $this->assertSame('3110000000', $user->phone);
        $this->assertSame('new.collector@example.com', $user->email);
        $this->assertSame('56565656', $user->identification);
    }

    public function test_update_backoffice_can_update_documents(): void
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Docs Collector',
            'phone' => '3003030303',
            'identification' => '777888999',
            'type_identification_id' => 1,
            'rol_id' => 3,
            'email' => 'docs.collector@example.com',
            'password' => bcrypt('secret123'),
            'state_id' => 1,
        ]);
        $user->save();

        $collector = new Collector();
        $collector->forceFill([
            'user_id' => $user->id,
            'state_id' => 3,
        ]);
        $collector->save();

        $response = $this->patch("/api/collector/{$collector->id}", [
            'identification_document' => UploadedFile::fake()->create('new-id.pdf', 20, 'application/pdf'),
            'driving_license_document' => UploadedFile::fake()->create('new-license.pdf', 20, 'application/pdf'),
        ]);

        $response->assertOk();

        $collector->refresh();
        $this->assertNotNull($collector->identification_document);
        $this->assertNotNull($collector->driving_license_document);
    }
}
