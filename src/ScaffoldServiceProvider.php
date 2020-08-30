<?php

namespace Boonei\Scaffold;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class ScaffoldServiceProvider extends ServiceProvider
{

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(__DIR__.'/../config/scaffold.php', 'scaffold');
  }

  /**
   * Bootstrap any package services.
   *
   * @return void
   */
  public function boot()
  {
      $this->loadRoutesFrom(__DIR__.'../../routes/web.php');
      $this->loadViewsFrom(__DIR__.'/../resources/views', 'scaffold');

      if ($this->app->runningInConsole()) {
          $this->publishes([
              __DIR__ . '/../public' => public_path('boonei/scaffold'),
          ], 'assets');
      }

      Cache::rememberForever('scaffold.translations', function () {
          $translations = collect();
          $locales = array_map(
              function($dir) {
                  return basename($dir, '.json');
              }, glob(resource_path("lang") . DIRECTORY_SEPARATOR . '*')
          );

          foreach ($locales as $locale) {
              $translations[$locale] = [
                  'php' => $this->phpTranslations($locale),
                  'json' => $this->jsonTranslations($locale),
              ];
          }

          return $translations;
      });
  }

    private function phpTranslations($locale)
    {

        $path = resource_path("lang" . DIRECTORY_SEPARATOR . $locale);
        if(! is_readable($path)) {
            return [];
        }

        return collect(File::allFiles($path))->flatMap(function ($file) use ($locale) {
            $key = ($translation = $file->getBasename('.php'));

            return [$key => trans($translation, [], $locale)];
        });
    }

    private function jsonTranslations($locale)
    {
        $path = resource_path("lang/$locale.json");

        if (is_string($path) && is_readable($path)) {
            return json_decode(file_get_contents($path), true);
        }

        return [];
    }
}
