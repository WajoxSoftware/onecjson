<?php
namespace wajox\onecjson\base;

class EntityRelation extends \yii\base\Object
{
    protected $attributes;

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function getJson()
    {
        return json_encode($this->getAttributes());
    }
}
