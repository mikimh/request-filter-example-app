<?php namespace Mikimh\RequestFilter;


trait RequestFilter
{


    /**
     * @params $fields = ['request_field_name' => ['model_field_name' => 'laravel_request_validation']];
     *         $fields = ['model_field_name' => ['laravel_request_validation'] ]
     *         $fields = ['request_field_name' => 'model_field_name']
     *         $fields = ['models_field_name']
     *
     *          if is null $this->filterFields will be used
     */
    public  function scopeRequestFilter($q, $fields=null, $request=null)
    {
        if(is_null($fields)){
            $fields = $this->filterFields;
        }

        $filter = RequestParser::getFilter($fields,$request);

        return QueryBuilder::getQuery($q, $filter);
    }



}
