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