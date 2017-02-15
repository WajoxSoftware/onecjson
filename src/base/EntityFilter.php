<?php
namespace wajox\onecjson\base;

class EntityFilter extends \yii\base\Object
{
    const GLUE_AND = 'And';
    const GLUE_OR = 'Or';

    protected $expressions;

    public function getExpressionsString()
    {
        return implode(' ', $this->getExpressions());
    }

    public function getExpressions()
    {
        $resultExp = [];
        foreach ($this->expressions as $value) {
            if (is_object($value)) {
                $value = '(' . $value->getExpressionsString() . ')';
            }

            $resultExp[] = $value;
        }

        return $resultExp;
    }

    public function where(
        $filter,
        $glue = self::GLUE_AND
    ) {
        $this->addFilter(
            $glue,
            $this->parseExpression($filter)
        );

        return $this;
    }

    public function andWhere($filter)
    {
        return $this->where($filter, self::GLUE_AND);
    }

    public function orWhere($filter)
    {
        return $this->where($filter, self::GLUE_OR);
    }

    protected function addFilter($glue, $expression)
    {
        if ($this->hasExpressions()) {
            $this->addExpression($glue);
        }
        
        return $this->addExpression($expression);
    }

    protected function hasExpressions()
    {
        return count($this->expressions) > 0;
    }

    protected function addExpression($expression)
    {
        $this->expressions[] = $expression;

        return $this;
    }

    protected function parseExpression($filter)
    {
        if (is_string($filter)) {
            return $filter;
        }

        if (!is_array($filter)) {
            throw new \Exception('Wrong filter');
        }

        if (isset($filter[0])
            && count($filter) == 3
        ) {
            return  $filter[1]
                    . ' ' . $filter[0]
                    . ' ' . $filter[2];
        }

        $expressions = [];
        foreach ($filter as $key => $value) {
            $value = is_string($value) ? '\'' . $value . '\'' : $value;
            $expressions[] = $key . ' eq ' . $value;
        }

        return implode(
            ' ' . self::GLUE_AND . ' ',
            $expressions
        );
    }
}
