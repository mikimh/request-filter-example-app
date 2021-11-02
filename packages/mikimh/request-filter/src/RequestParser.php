<?php namespace Mikimh\RequestFilter;


class RequestParser
{
    // ['request_field_name' => 'model_field_name']
    protected $fields = [];

    // ['request_field_name' => 'laravel_request_validation']
    protected $validation = [];


    protected $relationalOperator = [
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>=',
        'eq' => '=',
        'neq' => '!=',
        'in' => 'IN',
        'nin' => 'NOT IN',
        'like' => 'LIKE',
    ];

    protected $whereRelOperator = [
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>=',
        'eq' => '=',
        'neq' => '!=',
        'like' => 'LIKE',
    ];

    protected $logicalOperator = [
        'and' => 'AND',
        'or' => 'OR',
    ];

    protected $conditionFunctions = [
        'AND' => 'where',
        'OR' => 'orWhere',
        'IN' => 'whereIn',
        'NOT IN' => 'whereNotIn'
    ];

    public static function getFilter($fields, $request=null)
    {
        if(is_null($request)){
            $request = request()->getQueryString();
        }
        if(empty($request['filter'])){
            return false;
        }

        $parser = new RequestParser();

        $parser->__init($fields);

        $parser->validate();

        $parser->parseFilter($request['filter']);
    }


    /**
     * @params $fields = ['request_field_name' => ['model_field_name' => 'laravel_request_validation']];
     *         $fields = ['model_field_name' => ['laravel_request_validation'] ]
     *         $fields = ['request_field_name' => 'model_field_name']
     *         $fields = ['models_field_name']
     */
    public function __init($fields)
    {
       foreach ($fields as $key => $item){

           if (is_integer($key) && is_string($item)){  // $fields = ['models_field_name']
               $this->fields[$item] = $item;
           }elseif (is_string($key)){
               if(is_array($item)){
                   // $fields = ['model_field_name' => ['laravel_request_validation'] ]
                   if(is_integer(array_key_first($item)) ) {
                       $this->fields[$key] = $key;

                       $this->validation[$key] = $item;
                   // $fields = ['request_field_name' => ['model_field_name' => 'laravel_request_validation']];
                   }else{
                        $this->fields[$key] = array_key_first($item);

                        $this->validation[$key] = $item[$this->fields[$key]];
                   }
               // $fields = ['request_field_name' => 'model_field_name']
               }else{
                    $this->fields[$key] = $item;
               }
           }
       }
    }


    public function validate()
    {
        if(!empty($this->validation)) {
            request()->validate($this->validation);
        }
    }


    public function parseFilter($filter)
    {
        return $this->__parseFilter($filter);
    }


    public function __parseFilter($filter, $clause='where')
    {
        $result = [];

        foreach ($filter as $key => $item){

            if($this->isLogicOperator($key)){

                $result[][$this->getConditionalFunction($key)] = $this->__parseFilter($item);

            } elseif ($this->isField($key)){
                if(is_array($item)) {
                    $result[] = $this->parseCondition($item, $this->getField($key));
                } else {
                    $result[] = [$clause => [$this->getField($key), $this->getRelationalOperator('eq'), $item]];
                }

            }elseif ($this->isRelationalOperator($key)){


            }
        }


        return $result;
    }


    /**
     * @param string $logicalOperator e.g. 'AND', 'OR', 'IN'
     * @return string e.g. 'orWhere'
     */
    public function getConditionalFunction(string $logicalOperator): string
    {
        $logicalOperator = strtoupper($logicalOperator);

        if (!empty($this->conditionFunctions[$logicalOperator])) {
            return $this->conditionFunctions[$logicalOperator];
        }

        return 'where';
    }


    public function parseCondition($condition, $field)
    {
        $result = [$field];

        foreach ($condition as $key => $item){

            if($this->isWhereRelationalOperator($key)){
                $result[] = $this->getRelationalOperator($key);
            }// Else Throw exception

           $result[] = $item;
        }

        return [$this->getConditionalFunction($key) => $result];
    }



    public function getRelationalOperator($operator)
    {
        return $this->relationalOperator[$operator];
    }

    /**
     * AND, OR
     **/
    public function isLogicOperator($operator)
    {
        if(!empty($operator) && !empty($this->logicalOperator[strtolower($operator)])){
            return true;
        }

        return  false;
    }


    public function isField($requestField)
    {
        if(!empty($requestField) && !empty($this->fields[$requestField])) {
            return true;
        }

        return false;
    }

    // =, <>
    public function isRelationalOperator($operator)
    {
        if(!empty($operator) && !empty($this->relationalOperator[$operator])) {
            return true;
        }

        return false;
    }


    public function isWhereRelationalOperator($operator)
    {
        if(!empty($operator) && !empty($this->whereRelOperator[$operator])) {
            return true;
        }

        return false;
    }

    public function getField($requestField)
    {
        if($this->isField($requestField)) {
            return $this->fields[$requestField];
        }else {
            return false;
        }

    }


    public function getFields()
    {
        return $this->fields;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }


    public function getValidation()
    {
        return $this->validation;
    }

    public function setValidation($validation)
    {
        $this->validation = $validation;
    }
}
