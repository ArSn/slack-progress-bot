<?php

namespace App\Console\Commands;

class MarkSuccessful extends AbstractMarkCommand
{
	protected $signature = "slack:mark-successful
	                        {progress : The name of the progress-bar of the step to be marked}
	                        {step : The key of the step to be marked as successful}";
	protected $reachedState = 'successful';
}