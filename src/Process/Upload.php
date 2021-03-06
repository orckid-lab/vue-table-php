<?php

namespace OrckidLab\VueTable\Process;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

/**
 * Class Uploader
 * @package OrckidLab\VueTable\Process
 */
abstract class Upload
{
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var
	 */
	protected $path = 'vue-table/imports';

	/**
	 * @var
	 */
	protected $header;

	/**
	 * @var int
	 */
	protected $counter = 0;

	/**
	 * @var
	 */
	protected $file_name;

	/**
	 * @var
	 */
	protected $full_path;

	/**
	 * @var
	 */
	public $uploaded = [];

	/**
	 * @var
	 */
	public $failed = [];

	/**
	 * @var
	 */
	public $progress = 0;

	/**
	 * Upload constructor.
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * @return $this
	 */
	public function copyFile()
	{
		if(!$this->request->hasFile('import')){
			$this->path .= '/' . $this->request->file_name . '.txt';

			return $this;
		}

		$this->path = $this->request->file('import')->store($this->path);

		return $this;
	}

	/**
	 * @return array
	 */
	public function handle()
	{
		$index = $this->request->has('next') ? $this->request->next : 0;

		$this->copyFile();

		$this->file_name = $this->getFileName();

		$this->full_path = storage_path('app/' . $this->path);

		$csv = $this->loadFile();

		$chunk_size = 50;

		$chunk_offset = $index * $chunk_size;

		$row_count = $this->request->has('total') ? $this->request->total : collect($csv->fetchAll())->count();

		$csv->setOffset($chunk_offset);

		foreach($csv->fetchAssoc() as $row_index => $row){
			if($row_index === $chunk_offset + $chunk_size){
				break;
			}

			$this->uploadRow($row, $row_index + $chunk_offset);
		}

		$segment_count = floor($row_count / $chunk_size);

		$this->progress = $segment_count ? floor(($index / $segment_count) * 100) : 100;

		return array_merge([
			'file_name' => $this->file_name,
			'target' => $this->request->target,
			'total' => $row_count,
			'is_last' => $index == $segment_count,
			'next' => $index + 1,
			'uploaded' => count($this->uploaded),
			'failed' => count($this->failed),
			'rows' => [
				'uploaded' => $this->uploaded,
				'failed' => $this->failed,
			],
			'progress' => $this->progress,
		]);
	}

	/**
	 * Get the hashed name of the file from the path.
	 *
	 * @return mixed
	 */
	public function getFileName()
	{
		if($this->request->has('file_name')){
			return $this->request->file_name;
		}

		return pathinfo($this->path, PATHINFO_FILENAME);
	}

	/**
	 * Load the csv.
	 *
	 * @return Reader
	 */
	private function loadFile()
	{
		return Reader::createFromStream(fopen($this->full_path, 'r'));
	}

	/**
	 * Process the row.
	 *
	 * @param $index
	 * @param $row
	 */
	private function uploadRow($row, $index)
	{
		$validate = $this->validate($row);

		if($validate->fails()){
			$this->failed[] = [
				'index' => $index,
				'errors' => $validate->errors()->all(),
			];
		}
		else{
			$this->persist($row);

			$this->uploaded[] = [
				'index' => $index,
				'row' => $row,
			];
		}
	}

	/**
	 * Validate the row.
	 *
	 * @param $row
	 * @return mixed
	 */
	private function validate($row)
	{
		return Validator::make($row, $this->rules($row));
	}

	/**
	 * Define the logic to persist the row into database.
	 *
	 * @param $row array
	 */
	abstract public function persist($row);

	/**
	 * Define the validation rules to be applied to each row.
	 *
	 * @param $row array
	 * @return array
	 */
	abstract public function rules($row);
}