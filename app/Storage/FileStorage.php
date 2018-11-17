<?php

namespace App\Storage;

use Storage;

class FileStorage implements StorageInterface
{
	public function store(StoreableInterface $object): void
	{
		Storage::disk('local')->put($object->getName(), $this->encode($object));
	}

	private function encode(StoreableInterface $object): string
	{
		return json_encode(
			[
				'type' => $object->getType(),
				'data' => $object->toString(),
			]
		);
	}

	public function retrieve(string $name): StoreableInterface
	{
		return $this->decode(Storage::disk('local')->get($name));
	}

	private function decode(string $data)
	{
		$data = json_decode($data, true);
		/** @var StoreableInterface $object */
		$object = new $data['type'];
		$object->fromString($data['data']);

		return $object;
	}
}