<?php

namespace OrckidLab\VueTable;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

abstract class GeneratorCommand extends Command
{
	/**
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * Create a new command instance.
	 *
	 * @param Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem)
	{
		parent::__construct();

		$this->files = $filesystem;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$path = $this->getFullPath();

		if ($this->files->exists($this->getTargetPath())) {
			$this->error($this->getClassName() . ' already exists!');

			return false;
		}

		$stub = $this->files->get($this->getStub());

		$this->replace($stub);

		if (!$this->files->isDirectory(dirname($path))) {
			$this->files->makeDirectory(dirname($path), 0777, true, true);
		}

		$this->files->put($path, $stub);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array_merge(
			[
				['name', InputArgument::REQUIRED, 'The name of the class'],
			],
			method_exists($this, 'customArguments')
				? $this->customArguments()
				: []
		);
	}

	/**
	 * Get the class name.
	 *
	 * @return string
	 */
	protected function getClassName()
	{
		return $this->argument('name');
	}

	/**
	 * Get the root namespace for the class.
	 *
	 * @return string
	 */
	protected function rootNamespace()
	{
		return $this->laravel->getNamespace();
	}

	/**
	 * Get the base path where the file will be generated relative to the project root.
	 *
	 * @return string
	 */
	protected function getBasePath()
	{
		return str_replace('\\', '//', $this->getNameSpace()) . '/';
	}

	/**
	 * Get the path where the file will be generated relative to the project root.
	 *
	 * @return string
	 */
	protected function getTargetPath()
	{
		return $this->getBasePath() . $this->getClassName() . '.php';
	}

	/**
	 * Get the path where the file will be generated relative to the server root.
	 *
	 * @return string
	 */
	protected function getFullPath()
	{
		return base_path($this->getTargetPath());
	}

	/**
	 * Define the namespace.
	 *
	 * @return string
	 */
	abstract protected function getNamespace();

	/**
	 * Get the path of the stub file.
	 *
	 * @return string
	 */
	abstract protected function getStub();

	/**
	 * Define the logic to replace patterns within the class to be generated.
	 *
	 * @param $stub string
	 */
	abstract protected function replace(&$stub);
}