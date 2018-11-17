<?php

namespace App\Console\Commands;

class MarkFailed extends AbstractMarkCommand
{
	protected $signature = "slack:mark-failed
	                        {progress : The name of the progress-bar of the step to be marked}
	                        {step : The key of the step to be marked as failed}";
	protected $reachedState = 'failed';
}