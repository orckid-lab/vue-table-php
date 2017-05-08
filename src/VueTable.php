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
		return app(decrypt(request()->offsetGet('target')));
	}

	/**
	 * Return an instance of Upload.
	 *
	 * @return Upload
	 */
	public static function uploadWith()
	{
		return self::getInstance()->handleUploadWith();
	}
}