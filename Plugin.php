<?php namespace Winter\DriverGoogleDrive;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use System\Classes\PluginBase;

/**
 * DriverGoogleDrive Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'winter.drivergoogledrive::lang.plugin.name',
            'description' => 'winter.drivergoogledrive::lang.plugin.description',
            'author'      => 'Winter',
            'icon'        => 'icon-leaf',
        ];
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot(): void
    {
        Storage::extend('googledrive', function($app, $config) {
            $options = [];

            if (!empty($config['teamDriveId'] ?? null)) {
                $options['teamDriveId'] = $config['teamDriveId'];
            }

            $client = new Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
            $driver = new Filesystem($adapter);

            return new FilesystemAdapter($driver, $adapter);
        });
    }
}
