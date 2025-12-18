<?php

namespace App\Providers;

use App\Helpers\CustomHandleComponents;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Livewire\Mechanisms\HandleComponents\HandleComponents;


class CustomLivewireServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->extend(HandleComponents::class, function (HandleComponents $handleComponents) {
//            //TODO With this enabled, Filament notifications not working anymire
//            return new CustomHandleComponents($handleComponents);
//        });
//        $this->loadComponents();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    protected function loadComponents()
    {
        $filesystem = new Filesystem();

        collect(\Module::allEnabled())->map(function ($module) use ($filesystem) {
            $modulePath = $module->getPath();

            $moduleName = $module->getName();

            $path = $modulePath.'/app/Livewire';

            $files = collect( $filesystem->isDirectory($path) ? $filesystem->allFiles($path) : [] );

            $files->map(function ($file) use ($moduleName, $path) {
                $componentPath = \Str::after($file->getPathname(), $path.'/');

                $componentClassPath = strtr( $componentPath , ['/' => '\\', '.php' => '']);

                $componentName = $this->getComponentName($componentClassPath, $moduleName);

                $componentClassStr = "\\Modules\\{$moduleName}\\Livewire\\".$componentClassPath;

                $componentClass = get_class(new $componentClassStr);
                $loadComponent = \Livewire::component($componentName, $componentClass);
            });
        });
    }

    protected function getComponentName($componentClassPath, $moduleName = null)
    {
        $dirs = explode('\\', $componentClassPath);

        $componentName = '';

        foreach ($dirs as $dir) {
            $componentName .= \Str::kebab( lcfirst($dir) ).'.';
        }

        $moduleNamePrefix = ($moduleName) ? \Str::lower($moduleName).'::' : null;

        return \Str::substr($moduleNamePrefix.$componentName, 0, -1);
    }
}
