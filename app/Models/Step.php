<?php

namespace App\Models;

use App\Storage\StoreableInterface;
use DomainException;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class Step implements StoreableInterface
{
	private $name = '';
	private $state = self::STATE_NEW;
	public const STATE_NEW = 'new';
	public const STATE_IN_PROGRESS = 'in-progress';
	public const STATE_SUCCESSFUL = 'successful';
	public const STATE_FAILED = 'failed';
	private const STATES = [
		self::STATE_NEW,
		self::STATE_IN_PROGRESS,
		self::STATE_SUCCESSFUL,
		self::STATE_FAILED,
	];

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getName(): string
	{
		// different context than storing here, maybe change the interface?
		return $this->name;
	}

	public function getType(): string
	{
		return get_class($this);
	}

	public function toString(): string
	{
		return json_encode(
			[
				'name' => $this->name,
				'state' => $this->state,
			]
		);
	}

	public function fromString(string $string)
	{
		$data = json_decode($string, true);
		$this->name = $data['name'];
		$this->state = $data['state'];
	}

	private function guardAgainstIvalidState(string $desiredState): void
	{
		if (!in_array($desiredState, self::STATES)) {
			$msg = 'State "' . $desiredState . '" is invalid.';
			Log::err($msg);
			throw new DomainException($msg);
		}
	}

	public function setState(string $state): void
	{
		$this->guardAgainstIvalidState($state);
		$this->state = $state;
	}

	public function getState(): string
	{
		return $this->state;
	}

	public function composeStepMessage(): string
	{
		return $this->name . ' [' . $this->determineStateIcon() . ']';
	}

	private function determineStateIcon(): string
	{
		switch ($this->state) {
			case self::STATE_NEW:
				return '...';
			case self::STATE_IN_PROGRESS:
				return ':hourglass_flowing_sand:';
			case self::STATE_SUCCESSFUL:
				return ':heavy_check_mark:';
			case self::STATE_FAILED:
				return ':x:';
			default:
				throw new RuntimeException('State "' . $this->state . '" was not expected.');
		}
	}

	public function markInProgress(): void
	{
		$this->state = self::STATE_IN_PROGRESS;
	}

	public function markSuccessful(): void
	{
		$this->state = self::STATE_SUCCESSFUL;
	}

	public function markFailed(): void
	{
		$this->state = self::STATE_FAILED;
	}
}
