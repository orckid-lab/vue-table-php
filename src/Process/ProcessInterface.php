<?php

namespace OrckidLab\VueTable\Process;

interface ProcessInterface
{
	/**
	 * Define the logic required to destroy the table.
	 *
	 * @return array
	 */
	public function fire();
}