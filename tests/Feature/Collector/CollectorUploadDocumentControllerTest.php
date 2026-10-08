<?php

namespace Tests\Feature\Collector;

use App\Models\Collector;
use App\Models\User;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Support\CreatesCollectorMsSchema;
use Tests\TestCase;

class CollectorUploadDocumentControllerTest extends TestCase
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

    public function test_upload_document_updates_collector_document_urls(): void
    {
        $user = new User();
        $user->forceFill([
            'name' => 'Doc Collector',
            'phone' => '3009999999',
            'identification' => '555333111',
            'type_identification_id' => 1,
            'rol_id' => 3,
            'email' => 'doc.collector@example.com',
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

        $response = $this->post("/api/upload-document/{$collector->id}", [
            'identification_document' => UploadedFile::fake()->create('id.pdf', 20, 'application/pdf'),
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Documento cargado correctamente');

        $collector->refresh();
        $this->assertNotNull($collector->identification_document);
        $this->assertStringContainsString('/documents/collectors-identification/id.pdf', $collector->identification_document);
    }
}
