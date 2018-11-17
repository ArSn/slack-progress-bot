<?php

namespace App\Console\Commands;

use App\Models\ProgressBar;

class StartProgress extends AbstractCommand
{
	protected $signature = "slack:start-progress 
	                        {name : The name of the progress-bar to be started}
	                        {step* : A key for each step to report the progress on}";
	protected $description = "Start a new progress bar";

	public function handle()
	{
		/** @var ProgressBar $bar */
		$bar = app()->make(ProgressBar::class);
		$bar->setName($this->stringArgument('name'));

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