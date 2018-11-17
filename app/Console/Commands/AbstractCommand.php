<?php

namespace App\Console\Commands;

use App\Storage\StorageInterface;
use InvalidArgumentException;
use Illuminate\Console\Command;

abstract class AbstractCommand extends Command
{
	protected $storage;

	public function __construct(StorageInterface $storage)
	{
		parent::__construct();

		$this->storage = $storage;
	}

	public function stringArgument($key): string
	{
		$value = $this->argument($key);
		$this->guardAgainstNonStringArgument($value);

		return $value;
	}

	private function guardAgainstNonStringArgument($value): void
	{
		if (!is_string($value)) {
			throw new InvalidArgumentException('Argument is not a string: ' . print_r($value, true));
		}
	}
}