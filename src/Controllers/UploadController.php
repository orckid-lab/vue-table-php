<?php

namespace OrckidLab\VueTable\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OrckidLab\VueTable\VueTable;

/**
 * Class UploadController
 * @package OrckidLab\VueTable\Controllers
 */
class UploadController extends Controller
{
	/**
	 * @return array
	 */
	public function store()
	{
		return VueTable::uploadWith()->handle();
	}
}