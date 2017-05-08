<?php

namespace OrckidLab\VueTable\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OrckidLab\VueTable\VueTable;

/**
 * Class PaginationController
 * @package OrckidLab\VueTable\Controllers
 */
class PagingController extends Controller
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function show(Request $request)
	{
		preg_match('/page=(\d*)&?/', $request->url, $match);

		$page = isset($match[1]) ? (int)$match[1] : 1;

		return VueTable::getInstance()->startAt($page)->newQuery()->toArray();
	}
}