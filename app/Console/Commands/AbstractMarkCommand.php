<?php

namespace App\Console\Commands;

use App\Models\ProgressBar;
use App\Storage\StorageInterface;

class AbstractMarkCommand extends AbstractCommand
{
	protected $description = "Mark a step as --reached-state--";
	protected $reachedState = '';

	public function __construct(StorageInterface $storage)
	{
		parent::__construct($storage);

		foreach (['signature', 'description'] as $property) {
			$this->$property .= str_replace('--reached-state--', $this->reachedState, $this->$property);
		}
	}

	public function handle()
	{
		$this->handleState($this->reachedState);
	}

	public function handleState(string $state): void
	{
		$markMethod = 'mark' . ucfirst(camel_case($state));

		/** @var ProgressBar $bar */
		$bar = $this->storage->retrieve($this->stringArgument('progress'));
		$bar->getStep($this->stringArgument('step'))->$markMethod();

		if ($bar->update()) {
			$this->storage->store($bar);
			$this->info('Step marked as ' . $state . '.');
		} else {
			$this->error('Step could not be updated. See log.');
		}
	}
}