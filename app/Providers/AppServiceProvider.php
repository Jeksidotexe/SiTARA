<?php

namespace App\Providers;

use App\Models\Wilayah;
use App\Observers\WilayahObserver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

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
        Wilayah::observe(WilayahObserver::class);

        $this->backupGoogleDrive();
    }

    private function backupGoogleDrive()
    {
        try {
            Storage::extend('google', function($app, $config) {
                $options = [];

                if (!empty($config['folder'] ?? null)) {
                    $options['folder'] = $config['folder'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
                $driver = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch(\Exception $e) {
            Log::error("Gagal memuat driver Google Drive: " . $e->getMessage());
        }
    }
}
