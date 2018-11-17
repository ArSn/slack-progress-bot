<?php

namespace App\Console\Commands;

use App\Models\Bot;
use App\Models\ProgressBar;
use App\Storage\StorageInterface;
use Illuminate\Console\Command;

class StartProgress extends Command
{
	protected $signature = "slack:start-progress 
	                        {name : The name of the progress-bar to be started}
	                        {step* : A key for each step to report the progress on}";
	protected $description = "Start a new progress bar";
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