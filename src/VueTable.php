<?php

namespace OrckidLab\VueTable;

use Illuminate\Http\Request;
use OrckidLab\VueTable\Builder\VueTable as VueTableBuilder;
use OrckidLab\VueTable\Process\Upload;

/**
 * Class VueTable
 * @package OrckidLab\VueTable
 */
class VueTable
{
	/**
	 * Return an instance of VueTable Builder.
	 *
	 * @return VueTableBuilder
	 */
	public static function getInstance()
	{
		$class_basename = request()->offsetGet('target');

		$class = "App\VueTables\\$class_basename";

		if(!class_exists($class)){
			abort(500, "VueTable class does not exist.");
		}

		return app($class);
	}

	/**
	 * Return an instance of Upload.
	 *
	 * @return Upload
	 */
	public static function uploadWith()
	{
		return self::getInstance()->handleUpload();
	}
}