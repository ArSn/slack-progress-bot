<?php

namespace App\Models;

use App\Storage\StoreableInterface;
use OutOfBoundsException;

class ProgressBar implements StoreableInterface
{
	private $name;
	/** @var Step[] */
	private $steps = [];
	/** @var Message */
	private $message;
	private $startTime;
	private $lastUpdated;

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function addStep(string $name): void
	{
		$step = app()->make(Step::class);
		$step->setName($name);
		$this->steps[] = $step;
	}

	public function getStep(string $name): Step
	{
		foreach ($this->steps as $step) {
			if ($step->getName() == $name) {
				return $step;
			}
		}
		throw new OutOfBoundsException('No step with name "' . $name . '" was found.');
	}

	public function toString(): string
	{
		$steps = [];
		foreach ($this->steps as $step) {
			$steps[] = $step->toString();
		}

		return json_encode(
			[
				'name' => $this->getName(),
				'steps' => $steps,
				'message' => $this->message->toString(),
				'startTime' => $this->startTime,
				'lastUpdated' => $this->lastUpdated,
			]
		);
	}

	public function fromString(string $string)
	{
		$data = json_decode($string, true);
		$this->name = $data['name'];

		$steps = [];
		foreach ($data['steps'] as $stepData) {
			$step = app()->make(Step::class);
			$step->fromString($stepData);
			$steps[] = $step;
		}
		$this->steps = $steps;

		$message = app()->make(Message::class);
		$message->fromString($data['message']);
		$this->message = $message;

		$this->startTime = $data['startTime'];
		$this->lastUpdated = $data['lastUpdated'];
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): string
	{
		return get_class($this);
	}

	public function start(): bool
	{
		$this->startTime = time();
		$this->lastUpdated = time();

		$message = app()->make(Message::class);
		$this->message = $message;

		return $this->updateMessage();
	}

	public function update(): bool
	{
		$this->lastUpdated = time();

		return $this->updateMessage();
	}

	private function updateMessage()
	{
		return $this->message->send($this->composeProgressMessage());
	}

	private function composeProgressMessage(): string
	{
		$message = $this->name . ' (start ' . $this->composeTime($this->startTime) . '):';
		foreach ($this->steps as $step) {
			$message .= ' ' . $step->composeStepMessage();
		}

		$message .= ' (updated: ' . $this->composeTime($this->lastUpdated) . ')';

		return $message;
	}

	private function composeTime(int $time): string
	{
		return date('H:i:s', $time);
	}
}
