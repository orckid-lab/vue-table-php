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
	 * Triggers the handler for uploading to the table.
	 *
	 * @return array
	 */
	public function upload()
	{
		return VueTable::uploadWith()->fire();
	}

	/**
	 * Triggers the handler for destroying the table.
	 *
	 * @return array
	 */
	public function destroy()
	{
		return VueTable::destroyWith()->fire();
	}
}