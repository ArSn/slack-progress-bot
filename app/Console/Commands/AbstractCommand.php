<?php

namespace App\Console\Commands;

use App\Storage\StorageInterface;
use Illuminate\Console\Command;

abstract class AbstractCommand extends Command
{
	protected $storage;

	public function __construct(StorageInterface $storage)
	{
		parent::__construct();

		$this->storage = $storage;
	}
}