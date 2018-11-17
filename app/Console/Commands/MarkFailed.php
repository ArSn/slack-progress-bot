<?php

namespace App\Console\Commands;

use App\Models\ProgressBar;

class MarkFailed extends AbstractCommand
{
	protected $signature = "slack:mark-failed
	                        {progress : The name of the progress-bar of the step to be marked}
	                        {step : The key of the step to be marked as failed}";
	protected $description = "Mark a step as failed";

	public function handle()
	{
		/** @var ProgressBar $bar */
		$bar = $this->storage->retrieve($this->stringArgument('progress'));
		$bar->getStep($this->stringArgument('step'))->markFailed();

		if ($bar->update()) {
			$this->storage->store($bar);
			$this->info('Step marked as failed.');
		} else {
			$this->error('Step could not be updated. See log.');
		}
	}
}