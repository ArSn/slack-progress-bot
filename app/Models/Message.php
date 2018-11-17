<?php

namespace App\Models;

use App\Storage\StoreableInterface;
use Exception;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Log;

class Message implements StoreableInterface
{
	private $channel;
	private $text;
	private $timestamp;
	private $httpClient;

	public function __construct(ClientInterface $client)
	{
		$this->httpClient = $client;
	}

	/**
	 * Posts or updates the message given on parameters
	 */
	public function send(string $text): bool
	{
		$this->text = $text;
		if ($this->isNewMessage()) {
			return $this->postMessage();
		} else {
			return $this->updateMessage();
		}
	}

	private function isNewMessage()
	{
		return empty($this->channel) || empty($this->timestamp);
	}

	private function postMessage()
	{
		try {
			$response = $this->httpClient->request(
				'POST',
				'https://slack.com/api/chat.postMessage',
				[
					'json' => [
						'text' => $this->text,
						'channel' => config('slack.bot.channel'),
					],
				]
			);
			$this->parseResponse($response->getBody());

			return true;
		} catch (Exception $e) {
			Log::err($e->getMessage());

			return false;
		}
	}

	private function updateMessage()
	{
		try {
			$response = $this->httpClient->request(
				'POST',
				'https://slack.com/api/chat.update',
				[
					'json' => [
						'text' => $this->text,
						'channel' => $this->channel,
						'ts' => $this->timestamp,
					],
				]
			);
			$this->parseResponse($response->getBody());

			return true;
		} catch (Exception $e) {
			Log::err($e->getMessage());

			return false;
		}
	}

	private function parseResponse(string $response)
	{
		$response = json_decode($response, true);
		$this->channel = $response['channel'];
		$this->timestamp = $response['ts'];
	}

	public function getName(): string
	{
		// name does not make sense here, maybe change the interface?
		return 'not-important';
	}

	public function getType(): string
	{
		return get_class($this);
	}

	public function toString(): string
	{
		return json_encode(
			[
				'channel' => $this->channel,
				'text' => $this->text,
				'timestamp' => $this->timestamp,
			]
		);
	}

	public function fromString(string $string)
	{
		$data = json_decode($string, true);
		$this->channel = $data['channel'];
		$this->text = $data['text'];
		$this->timestamp = $data['timestamp'];
	}
}
