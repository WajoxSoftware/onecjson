<?php
namespace wajox\onecjson\base;

abstract class EntityAbstract extends \yii\base\Object
{
	protected $isNew = true;
	protected $attributes;

	abstract public function getRestIdAttribute(): string;
	abstract public function getRestResourcePath(string $id): string;
	abstract public function getRestResourcesPath(): string;
	abstract public function getRestAttributeMap(string $name): string;

	public function getRestId(): string
	{
		return $this->getAttribute($this->getRestIdAttribute());
	}

	public function getIsNew(): bool
	{
		return $this->isNew;
	}

	public function setIsNew(bool $isNew): EntityAbstract
	{
		$this->isNew = $isNew;

		return $this;
	}

	public function setAttributes(array $attributes): EntityAbstract
	{
		$this->attributes = $attributes;

		return $this;
	}

	public function getAttributes(): array
	{
		return $this->attributes;
	}

	public function getAttribute(string $name)
	{
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
	}

	public function setAttribute(string $name, $value)
	{
		$this->attributes[$name] = $value;

		return $this;
	}

	public function getJson(): string
	{
		return json_encode($this->getAttributes());
	}
/*
	public function loadTranslitJson(string $json): EntityAbstract
	{
		foreach ($json as $key => $value) {
			if (($name = $this->getRestAttributeMap()) === null) {
				continue;
			}

			$this->setAttribute($name, $value);
		}

		return $this;
	}

	public function getTranslitJson(): string
	{
		$data = [];

		foreach ($this->getAttributes() as $name => $value) {
			$key = TextHelper::translit($name);
			$data[$key] = $value;
		}

		return json_encode($data);
	}*/
}