<?php
namespace wajox\onecjson\services\entities;

use wajox\onecjson\base\Finder;
use wajox\onecjson\base\EntityAbstract;
use wajox\onecjson\base\EntityRelation;
use wajox\onecjson\services\data\JsonDataProvider;

class EntitiesManager extends \yii\base\Object
{
    protected $restAdapter;

    public function __construct(array $params = [])
    {
        $this
            ->initAdapter(
                $params['adapterClass'],
                $params['adapterConfig']
            );
    }

    public function one(string $className, string $id, bool $cache = false)
    {
        $object = \Yii::createObject($className);
        $path = $object->getRestResourcePath($id);
        $json = $this->getRestAdapter()->get($path, [], $cache);

        $object->setAttributes($json)
               ->setIsNew(false);

        return $object;
    }

    public function finder(string $className)
    {
        $finder = \Yii::createObject(
            Finder::className(),
            [$this, $className]
        );

        return $finder;
    }

    public function count(string $className, array $query = [], bool $cache = false): int
    {
        $object = \Yii::createObject($className);
        $path = $object->getRestResourcesPath();

        return $this->getRestAdapter()->count($path, $query, [], $cache);
    }

    public function getRelation(EntityAbstract $entityObject, string $relationAttribute, bool $cache = false): EntityRelation
    {
        $path = $entityObject->getAttribute($relationAttribute);

        $entity = \Yii::createObject(EntityRelation::className());

        if (!empty($path)) {
            $json = $this->getRestAdapter()->get($path, [], $cache);
            $entity->setAttributes($json);
        }

        return $entity;
    }

    public function all(
        string $className,
        array $query = [],
        bool $cache = false
    ): array {
        $path = \Yii::createObject($className)->getRestResourcesPath();

        $json = $this->getRestAdapter()->get($path, $query, $cache);
        if (sizeof($json['value']) == 0) {
            return [];
        }

        $items = $json['value'];

        if (isset($items['RecordSet'])) {
            $items = $items['RecordSet'];
        }

        return array_map(
            function ($attributes) use ($className) {
                return \Yii::createObject($className)
                    ->setAttributes($attributes)
                    ->setIsNew(false);
            },
            $items
        );
    }

    protected function persist(EntityAbstract $object): EntityAbstract
    {
        $params = ['json' => $object->getJson()];
        $path = $object->getRestResourcesPath();

        if ($object->getIsNew()) {
            $json = $this->getRestAdapter()->post(
                $path,
                [],
                $params
            );
        } else {
            $json = $this->getRestAdapter()->patch(
                $path,
                [],
                $params
            );
        }

        return $object
            ->setAttributes($json)
            ->setIsNew(false);
    }

    protected function setRestAdapter(JsonDataProvider $restAdapter): EntitiesManager
    {
        $this->restAdapter = $restAdapter;

        return $this;
    }

    public function getRestAdapter(): JsonDataProvider
    {
        return $this->restAdapter;
    }

    protected function initAdapter(string $adapterClass, array $adapterConfig): EntitiesManager
    {
        $adapterObject = \Yii::createObject(
            $adapterClass,
            [$adapterConfig]
        );

        $this->setRestAdapter($adapterObject);

        return $this;
    }
}
