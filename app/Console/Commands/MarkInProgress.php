<?php

namespace App\Console\Commands;

use App\Models\ProgressBar;

class MarkInProgress extends AbstractCommand
{
	protected $signature = "slack:mark-in-progress
	                        {progress : The name of the progress-bar of the step to be marked}
	                        {step : The key of the step to be marked as in-progress}";
	protected $description = "Mark a step as in-progress";

	public function handle()
	{
		/** @var ProgressBar $bar */
		$bar = $this->storage->retrieve($this->stringArgument('progress'));
		$bar->getStep($this->stringArgument('step'))->markInProgress();

		if ($bar->update()) {
			$this->storage->store($bar);
			$this->info('Step marked as in-progress.');
		} else {
			$this->error('Step could not be updated. See log.');
		}
	}
}