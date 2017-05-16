<?php

namespace OrckidLab\VueTable\Process;

use Illuminate\Support\Facades\DB;

abstract class Destroy implements ProcessInterface
{
	public function truncate($table)
	{
		return DB::table($table)->truncate();
	}

	public function deleteAll($table)
	{
		return DB::table($table)->delete();
	}
}