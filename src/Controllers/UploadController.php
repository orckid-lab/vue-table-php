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
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		return VueTable::uploadWith()->handle();
	}
}