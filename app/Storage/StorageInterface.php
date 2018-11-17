<?php

namespace App\Storage;

interface StorageInterface
{
	public function store(StoreableInterface $object): void;

	public function retrieve(string $name): StoreableInterface;
}