<?php

namespace OrckidLab\VueTable\Providers;

use Illuminate\Support\ServiceProvider;
use OrckidLab\VueTable\Commands\MakeVueTable;
use OrckidLab\VueTable\Commands\MakeVueTableUpload;

class VueTableServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'vue-table');

		$this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

		if ($this->app->runningInConsole()) {
			$this->commands([
				MakeVueTable::class,
				MakeVueTableUpload::class,
			]);
		}
	}

	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 */
	public function register()
	{

	}
}