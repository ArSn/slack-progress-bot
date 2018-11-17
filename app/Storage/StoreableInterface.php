<?php

namespace App\Storage;

interface StoreableInterface
{
	public function getName(): string;

	public function getType(): string;

	public function toString(): string;

	public function fromString(string $string);
}