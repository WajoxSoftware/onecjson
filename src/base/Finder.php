<?php
namespace wajox\onecjson\base;

use wajox\onecjson\base\EntityAbstract;
use wajox\onecjson\services\entities\EntitiesManager;

class Finder extends \yii\base\Object
{
    protected $manager;
    protected $resource;
    protected $filter;
    protected $select = [];
    protected $order = [];
    protected $with = [];
    protected $limit;
    protected $offset;

    public function __construct(EntitiesManager $manager, string $resource)
    {
        $this->setManager($manager)
             ->setResource($resource)
             ->initFilter();
    }

    public function select(array $columns): Finder
    {
        $this->setSelect($columns);

        return $this;
    }

    public function orderBy(array $order): Finder
    {
        $this->setOrderBy($order);

        return $this;
    }

    public function with(array $with): Finder
    {
        $this->setWith($with);

        return $this;
    }

    public function limit(int $limit, int $offset = 0): Finder
    {
        $this->setLimit($limit)
             ->setOffset($offset);

        return $this;
    }

    public function where($where): Finder
    {
        $this->getFilter()
             ->where($where);

        return $this;
    }

    public function orWhere($where): Finder
    {
        $this->getFilter()
             ->orWhere($where);

        return $this;
    }

    public function andWhere($where): Finder
    {
        $this->getFilter()
             ->andWhere($where);

        return $this;
    }

    public function one(bool $cache = false)
    {
        $this->limit(1);

        $results = $this->getManager()->all(
            $this->getResource(),
            $this->getQueryParams(),
            $cache
        );

        return array_shift($results);
    }

    public function all(bool $cache = false): array
    {
        return $this->getManager()->all(
            $this->getResource(),
            $this->getQueryParams(),
            $cache
        );
    }

    public function count(bool $cache = false): int
    {
        return $this->getManager()->count(
            $this->getResource(),
            $this->getQueryParams(),
            $cache
        );
    }

    protected function setManager(EntitiesManager $manager): Finder
    {
        $this->manager = $manager;

        return $this;
    }

    protected function getManager(): EntitiesManager
    {
        return $this->manager;
    }

    protected function setResource(string $resource): Finder
    {
        $this->resource = $resource;

        return $this;
    }

    protected function getResource(): string
    {
        return $this->resource;
    }

    protected function getQueryParams(): array
    {
        $params = [
            '$filter' => $this->getFilterString(),
            '$select' => $this->getSelectString(),
            '$expand' => $this->getExpandString(),
            '$top' => $this->getTopString(),
            '$skip' => $this->getSkipString(),
            '$orderby' => $this->getOrderByString(),
        ];

        $params = array_filter($params);

        return $params;
    }

    protected function initFilter(): Finder
    {
        $this->setFilter(
            \Yii::createObject(
                EntityFilter::className()
            )
        );

        return $this;
    }

    protected function setFilter(EntityFilter $filter): Finder
    {
        $this->filter = $filter;

        return $this;
    }

    protected function getFilter(): EntityFilter
    {
        return $this->filter;
    }

    protected function setLimit(int $limit): Finder
    {
        $this->limit = $limit;
        
        return $this;
    }

    protected function setOffset(int $offset): Finder
    {
        $this->offset = $offset;

        return $this;
    }

    protected function setSelect(array $select): Finder
    {
        $this->select = $select;

        return $this;
    }

    protected function setOrderBy(array $order): Finder
    {
        $this->order = $order;

        return $this;
    }

    protected function setWith(array $with): Finder
    {
        $this->with = $with;

        return $this;
    }

    protected function getFilterString(): string
    {
        return $this->getFilter()->getExpressionsString();
    }

    protected function getSelectString(): string
    {
        return implode(', ', $this->select);
    }

    protected function getExpandString(): string
    {
        return implode(', ', $this->with);
    }

    protected function getOrderByString(): string
    {
        return implode(', ', $this->order);
    }

    protected function getTopString(): string
    {
        return (string) $this->limit;
    }

    protected function getSkipString(): string
    {
        return (string) $this->offset;
    }
}
