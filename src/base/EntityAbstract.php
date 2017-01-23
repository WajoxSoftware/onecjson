<?php
namespace wajox\onecjson\base;

abstract class EntityAbstract extends EntityRelation
{
    protected $isNew = true;

    abstract public function getRestIdAttribute(): string;
    abstract public function getRestResourcePath(string $id): string;
    abstract public function getRestResourcesPath(): string;

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
