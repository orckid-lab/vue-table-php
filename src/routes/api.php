<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')
	->middleware('api')
	->namespace('\OrckidLab\VueTable\Controllers')
	->group(function () {
		Route::post('vue-table/page', 'VueTableController@index');

		Route::post('vue-table/destroy', 'VueTableController@destroy');

		Route::post('vue-table/download', 'DownloadController@store');

		Route::post('vue-table/upload', 'VueTableController@upload');
	});