<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (file_exists(storage_path('workflows/workflows.json'))){
            $workflows = json_decode(file_get_contents(storage_path('workflows/workflows.json')), true);

            $paths = [];

            foreach ($workflows as $workflow)
            {
                $path = $workflow['Path'];
                if (!in_array($path, $paths))
                {
                    $paths[] = $path;
                }
            }

            config(['workflows' => $workflows]);
            config(['paths' => $paths]);
        }
    }
}
