<?php

namespace OrckidLab\VueTable\Builder;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use JsonSerializable;

/**
 * Class Table
 * @package OrckidLab\VueTable
 */
abstract class VueTable implements Arrayable, Jsonable, JsonSerializable
{
	/**
	 * @var array
	 */
	protected $columns_maps = [];

	/**
	 * @var array
	 */
	protected $columns = [];

	/**
	 * @var array
	 */
	protected $labels = [];

	/**
	 * Formatted results.
	 *
	 * @var array
	 */
	protected $rows = [];

	/**
	 * @var LengthAwarePaginator
	 */
	protected $results = [];

	/**
	 * Amount to display per page.
	 *
	 * @var int
	 */
	protected $per_page = 10;

	/**
	 * Page to start table at.
	 *
	 * @var int
	 */
	protected $start_at = 1;

	/**
	 * Table heading.
	 *
	 * @var callable|string
	 */
	protected $title = 'List title.';

	/**
	 * Enable/Disable paging.
	 *
	 * @var bool
	 */
	protected $paging = true;

	/**
	 * @var string
	 */
	protected $current_path;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var string
	 */
	protected $upload_with;

	/**
	 * Table constructor.
	 */
	public function __construct()
	{
		$this->request = Request::capture();
	}

	/**
	 * @param string $label
	 * @param null $column
	 * @param null $callback
	 * @return $this
	 */
	public function addColumn($label = '', $column = null, $callback = null)
	{
		$this->columns_maps[] = [
			'label' => $label,
			'column' => $column,
			'callback' => $callback
		];

		return $this;
	}

	/**
	 * @return $this
	 */
	public function newQuery()
	{
		$this->build();

		$this->results = $this->query()->paginate($this->per_page, ['*'], 'page', $this->start_at);

		foreach ($this->columns_maps as $column_map) {
			$this->labels[] = $column_map['label'];

			$this->columns[] = $column_map['column'];
		}

		foreach ($this->results as $model) {
			$new_row = [];

			foreach ($this->columns_maps as $column_map) {
				if ($column_map['column']) {
					$new_row[] = $model->{$column_map['column']};
				}

				if ($column_map['callback']) {
					$new_row[] = call_user_func($column_map['callback'], $model);
				}
			}

			$this->rows[] = $new_row;
		}

		return $this;
	}

	/**
	 * @return array
	 */
	public function pagination()
	{
		$window = UrlWindow::make($this->results);

		return array_filter([
			$window['first'],
			is_array($window['slider']) ? '...' : null,
			$window['slider'],
			is_array($window['last']) ? '...' : null,
			$window['last'],
		]);
	}

	/**
	 * @param $page
	 * @return $this
	 */
	public function startAt($page)
	{
		$this->start_at = $page;

		return $this;
	}

	/**
	 * @param $title
	 * @return $this
	 */
	public function title($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * @return mixed
	 */
	protected function getTitle()
	{
		return call_user_func($this->title);
	}

	/**
	 * @return int
	 */
	protected function showing()
	{
		if($this->start_at == 1){
			return $this->results->count();
		}

		return (($this->start_at - 1) * $this->per_page) + $this->results->count();
	}

	/**
	 * @return $this
	 */
	public function disablePaging()
	{
		$this->paging = false;

		return $this;
	}

	/**
	 * @param $condition
	 * @param $callback
	 * @return $this
	 */
	public function when($condition, $callback)
	{
		if($condition){
			call_user_func($callback);
		}

		return $this;
	}

	/**
	 * @return int
	 */
	protected function total()
	{
		return $this->results->total();
	}

	/**
	 * @return string
	 */
	protected function currentPath(){
		if($this->request->has('url')){
			return $this->request->url;
		}

		return $this->request->fullUrl();
	}

	/**
	 * Evaluate if class supports uploading.
	 *
	 * @return \Illuminate\Foundation\Application|mixed
	 */
	public function handleUpload()
	{
		if(
			!(method_exists($this, 'uploadWith') && class_exists($this->uploadWith()))
		){
			abort(500, class_basename($this) . ' does not support upload.');
		}

		return app($this->uploadWith());
	}

	/**
	 * Evaluate if class supports deleting.
	 *
	 * @return \Illuminate\Foundation\Application|mixed
	 */
	public function handleDestroy()
	{
		if(
			!(method_exists($this, 'destroy'))
		){
			abort(500, class_basename($this) . ' does not support deleting.');
		}

		return $this->destroy();
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function renderList()
	{
		return view('vue-table::vue-table')->with([
			'table' => $this,
		]);
	}

	/**
	 * @return mixed
	 */
	public static function render()
	{
		return (new static)->newQuery()->renderList();
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array_merge([
			'hasResult' => $this->total() > 0,
			'hasPagination' => $this->paging && $this->total() > $this->per_page,
			'columns' => $this->columns,
			'labels' => $this->labels,
			'rows' => $this->rows,
			'pagination' => $this->pagination(),
			'showing' => $this->showing(),
			'title' => $this->getTitle(),
			'ajax' => [
				'target' => class_basename($this),
				'url' => $this->currentPath(),
			]
		], $this->results->toArray());
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param  int $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		$json = json_encode($this->jsonSerialize(), $options);

		if (JSON_ERROR_NONE !== json_last_error()) {
			throw JsonEncodingException::forModel($this, json_last_error_msg());
		}

		return $json;
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Define the table options.
	 *
	 * @return void
	 */
	abstract public function build();

	/**
	 * Define the model to query for.
	 *
	 * @return Builder
	 */
	abstract public function query();
}