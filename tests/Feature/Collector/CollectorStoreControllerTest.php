<?php

namespace Tests\Feature\Collector;

use App\Models\Collector;
use App\Models\Rol;
use App\Models\State;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Support\CreatesCollectorMsSchema;
use Tests\TestCase;

class CollectorStoreControllerTest extends TestCase
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

    public function test_create_backoffice_collector_with_documents_is_enabled(): void
    {
        $response = $this->post('/api/collector-backoffice', [
            'name' => 'Backoffice Collector',
            'phone' => '3007777777',
            'identification' => '888777666',
            'type_identification' => 1,
            'email' => 'backoffice.collector@example.com',
            'images' => UploadedFile::fake()->image('collector.png'),
            'identification_document' => UploadedFile::fake()->create('id.pdf', 50, 'application/pdf'),
            'driving_license_document' => UploadedFile::fake()->create('drive.pdf', 50, 'application/pdf'),
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Se ha creado el recolector correctamente');

        $userId = (int) \DB::table('users')->where('email', 'backoffice.collector@example.com')->value('id');

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'rol_id' => Rol::where('name', Rol::RECYCLER)->value('id'),
            'state_id' => State::where('name', State::ENABLED)->value('id'),
        ]);

        $this->assertDatabaseHas('collectors', [
            'user_id' => $userId,
            'state_id' => State::where('name', State::ENABLED)->value('id'),
        ]);
    }

    public function test_create_backoffice_collector_without_documents_is_pending(): void
    {
        $response = $this->post('/api/collector-backoffice', [
            'name' => 'Pending Collector',
            'phone' => '3005555555',
            'identification' => '111444777',
            'type_identification' => 1,
            'email' => 'pending.collector@example.com',
            'images' => UploadedFile::fake()->image('collector.png'),
        ]);

        $response->assertOk();

        $userId = (int) \DB::table('users')->where('email', 'pending.collector@example.com')->value('id');

        $this->assertDatabaseHas('collectors', [
            'user_id' => $userId,
            'state_id' => State::where('name', State::PENDING_USER)->value('id'),
        ]);
    }
}
