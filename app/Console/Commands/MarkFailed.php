<?php

namespace App\Console\Commands;

use App\Models\ProgressBar;
use App\Storage\StorageInterface;
use Illuminate\Console\Command;

class MarkFailed extends Command
{
	protected $signature = "slack:mark-failed
	                        {progress : The name of the progress-bar of the step to be marked}
	                        {step : The key of the step to be marked as failed}";
	protected $description = "Mark a step as failed";
	private $storage;

	public function __construct(StorageInterface $storage)
	{
		parent::__construct();

		$this->storage = $storage;
	}

	public function handle()
	{
		$bar = new ProgressBar($this->argument('name'));

		collect($this->argument('step'))->each(
			function($step) use ($bar) {
				$bar->addStep($step);
			}
		);

		if ($bar->start()) {
			$this->storage->store($bar);
			$this->info('Progress started.');
		} else {
			$this->error('Progress could not be started. See log.');
		}
	}
}