<?php

namespace OrckidLab\VueTable\Commands;

use OrckidLab\VueTable\GeneratorCommand;

/**
 * Class MakeVueTable
 * @package OrckidLab\VueTable\Commands
 */
class MakeVueTable extends GeneratorCommand
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'make:vue-table {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new instance of VueTable class.';

	/**
	 * Define the namespace.
	 *
	 * @return string
	 */
	protected function getNamespace()
	{
		return $this->rootNamespace() . 'VueTables';
	}

	/**
	 * Get the path of the stub file.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/stubs/vue-table.stub';
	}

	/**
	 * Define the logic to replace patterns within the class to be generated.
	 *
	 * @param $stub string
	 */
	protected function replace(&$stub)
	{
		$stub = str_replace('DummyNamespace', $this->getNamespace(), $stub);

		$stub = str_replace('DummyClass', $this->getClassName(), $stub);
	}

	/**
	 * Get the class name.
	 *
	 * @return string
	 */
	protected function getClassName()
	{
		return $this->argument('name') . 'VueTable';
	}
}
