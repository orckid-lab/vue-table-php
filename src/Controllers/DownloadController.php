<?php

namespace OrckidLab\VueTable\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OrckidLab\VueTable\ExportSegment;
use OrckidLab\VueTable\Jobs\AppendExport;
use OrckidLab\VueTable\VueTable;

/**
 * Class DownloadController
 * @package OrckidLab\VueTable\Controllers
 */
class DownloadController extends Controller
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
// generate blank file
		$export_id = md5(encrypt(time() . '-vue-table-export'));

		$extension = 'csv';

		$path = "public/vue-table/downloads/$export_id.$extension";

		Storage::put($path, '');

		// pick up query
		$query = VueTable::getInstance()->query();

		$total_rows = $query->count();

		// loop into chunks
		$count = 0;

		foreach($query->get()->chunk(50) as $index => $chunk){
			$count += count($chunk);

			$segment = ExportSegment::create()
				->id($export_id)
				->chunk($chunk)
				->total($total_rows)
				->path($path)
				->index($count);

			// dispatch job that will perform
			dispatch(new AppendExport($segment));
		}

		// return array with

		// channel id to listen to
		// download url
		return [
			'export_id' => $export_id,
			'download' => asset(str_replace('public', 'storage', $path)),
		];
	}
}