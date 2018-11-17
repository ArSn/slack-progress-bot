<?php

return [
	'bot' => [
		'token' => env('SLACK_BOT_TOKEN', 'INVALID_BOT_TOKEN'),
		'channel' => env('SLACK_BOT_CHANNEL', '#main'),
	],
];