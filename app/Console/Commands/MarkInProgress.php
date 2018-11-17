<?php

namespace App\Console\Commands;

class MarkInProgress extends AbstractMarkCommand
{
	protected $signature = "slack:mark-in-progress
	                        {progress : The name of the progress-bar of the step to be marked}
	                        {step : The key of the step to be marked as in-progress}";
	protected $reachedState = 'in-progress';
}