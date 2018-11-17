<?php

namespace App\Console\Commands;

use App\Models\ProgressBar;

class MarkSuccessful extends AbstractCommand
{
	protected $signature = "slack:mark-successful
	                        {progress : The name of the progress-bar of the step to be marked}
	                        {step : The key of the step to be marked successfully}";
	protected $description = "Mark a step as completed successfully";

	public function handle()
	{
		/** @var ProgressBar $bar */
		$bar = $this->storage->retrieve($this->argument('progress'));
		$bar->getStep($this->argument('step'))->markSuccessful();

		if ($bar->update()) {
			$this->storage->store($bar);
			$this->info('Step marked as successful.');
		} else {
			$this->error('Step could not be updated. See log.');
		}
	}
}