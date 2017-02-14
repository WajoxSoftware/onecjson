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

    public function __construct($manager, $resource)
    {
        $this->setManager($manager)
             ->setResource($resource)
             ->initFilter();
    }

    public function select($columns)
    {
        $this->setSelect($columns);

        return $this;
    }

    public function orderBy($order)
    {
        $this->setOrder($order);

        return $this;
    }

    public function with($with)
    {
        $this->setWith($with);

        return $this;
    }

    public function limit($limit, $offset = 0)
    {
        $this->setLimit($limit)
             ->setOffset($offset);

        return $this;
    }

    public function where($where)
    {
        $this->getFilter()
             ->where($where);

        return $this;
    }

    public function orWhere($where)
    {
        $this->getFilter()
             ->orWhere($where);

        return $this;
    }

    public function andWhere($where)
    {
        $this->getFilter()
             ->andWhere($where);

        return $this;
    }

    public function one()
    {
        $this->limit(1);

        $results = $this->getManager()->all(
            $this->getResource(),
            $this->getQueryParams()
        );

        return array_shift($results);
    }

    public function all()
    {
        return $this->getManager()->all(
            $this->getResource(),
            $this->getQueryParams()
        );
    }

    public function count()
    {
        return $this->getManager()->count(
            $this->getResource(),
            $this->getQueryParams()
        );
    }

    protected function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    protected function getManager()
    {
        return $this->manager;
    }

    protected function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    protected function getResource()
    {
        return $this->resource;
    }

    protected function getQueryParams()
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

    protected function initFilter()
    {
        $this->setFilter(
            \Yii::createObject(
                EntityFilter::className()
            )
        );

        return $this;
    }

    protected function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    protected function getFilter()
    {
        return $this->filter;
    }

    protected function setLimit($limit)
    {
        $this->limit = $limit;
        
        return $this;
    }

    protected function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    protected function setSelect($select)
    {
        $this->select = $select;

        return $this;
    }

    protected function setOrderBy($order)
    {
        $this->order = $order;

        return $this;
    }

    protected function setWith($with)
    {
        $this->with = $with;

        return $this;
    }

    protected function getFilterString()
    {
        return $this->getFilter()->getExpressionsString();
    }

    protected function getSelectString()
    {
        return implode(', ', $this->select);
    }

    protected function getExpandString()
    {
        return implode(', ', $this->with);
    }

    protected function getOrderByString()
    {
        return implode(', ', $this->order);
    }

    protected function getTopString()
    {
        return (string) $this->limit;
    }

    protected function getSkipString()
    {
        return (string) $this->offset;
    }
}
