<?php

namespace App\Providers;

use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use AzureOss\Storage\Blob\BlobServiceClient;
use Illuminate\Filesystem\FilesystemAdapter;
use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('azure', function ($app, $config) {
            $client = BlobServiceClient::fromConnectionString(
                "DefaultEndpointsProtocol=https;AccountName={$config['account-name']};AccountKey={$config['account-key']};EndpointSuffix=core.windows.net"
            );

            $containerClient = $client->getContainerClient($config['container']);
            $adapter = new AzureBlobStorageAdapter($containerClient);
            $filesystem = new Filesystem($adapter);

            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}
