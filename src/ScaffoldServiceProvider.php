<?php

namespace Boonei\Scaffold;

use Illuminate\Support\ServiceProvider;

class ScaffoldServiceProvider extends ServiceProvider
{

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {

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
  }
}
