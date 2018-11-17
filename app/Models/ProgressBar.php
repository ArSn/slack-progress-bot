<?php

namespace App\Models;

use App\Storage\StoreableInterface;

class ProgressBar implements StoreableInterface
{
	private $name;
	private $steps = [];

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	public function addStep($step): void
	{
		$this->steps[] = $step;
	}

	public function toString(): string
	{
		return json_encode(
			[
				'name' => $this->getName(),
				'steps' => $this->steps,
			]
		);
	}

	public function fromString(string $string)
	{
		$data = json_decode($string, true);
		$this->name = $data['name'];
		$this->steps = $data['steps'];
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getType(): string
	{
		return get_class($this);
	}
}
