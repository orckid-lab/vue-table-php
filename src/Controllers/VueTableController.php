<?php

namespace OrckidLab\VueTable\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OrckidLab\VueTable\VueTable;

/**
 * Class PaginationController
 * @package OrckidLab\VueTable\Controllers
 */
class VueTableController extends Controller
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		preg_match('/page=(\d*)&?/', $request->url, $match);

		$page = isset($match[1]) ? (int)$match[1] : 1;

		return VueTable::getInstance()->startAt($page)->newQuery()->toArray();
	}

	/**
	 * @return bool
	 */
	public function destroy()
	{
		return VueTable::getInstance()->destroy();
	}
}