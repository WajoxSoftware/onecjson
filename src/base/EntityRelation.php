<?php
namespace wajox\onecjson\base;

class EntityRelation extends \yii\base\Object
{
    protected $attributes;

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
}
