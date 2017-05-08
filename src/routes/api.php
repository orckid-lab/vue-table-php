<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')
	->middleware('api')
	->namespace('\OrckidLab\VueTable\Controllers')
	->group(function () {
		Route::post('/vue-table/page', 'PagingController@show');

		Route::post('/vue-table/download', 'DownloadController@store');

		Route::post('/vue-table/upload', 'UploadController@store');
	});