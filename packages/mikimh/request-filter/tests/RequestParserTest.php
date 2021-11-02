<?php

use Mikimh\RequestFilter\RequestParser;
use Tests\TestCase;

class RequestParserTest extends TestCase
{



    public function dataProvider()
    {
        /**
        * $fields = ['request_field_name' => ['model_field_name' => 'laravel_request_validation']];
        * $fields = ['model_field_name' => ['laravel_request_validation'] ]
        * $fields = ['request_field_name' => 'model_field_name']
        * $fields = ['models_field_name']
        **/
        return [
            //0------Fields------------------------------------Parsed Fields------------------------------------Parsed Validation
            [['name', 'surname', 'email'],      ['name' => 'name', 'surname' => 'surname', 'email' => 'email'], [],
            //-----Request Filter-------Parsed Filter------------
            ['name' => 'first name'],  [['where' => ['name', '=', 'first name']] ]],

            //1----Fields--------------Parsed Fields-------------Parsed Validation-------------
            [['first_name' => 'name'], ['first_name' => 'name'], [],
            //----Request Filter-------------Parsed Filter------------
             ['first_name' => 'first name'], [['where' => ['name', '=', 'first name']] ]],

            //2------Fields------------------------------------
            [['first_name' => ['name' => 'string|nullable'], 'last_name' => ['surname' => 'string|nullable']],
            //---Parsed Fields-----------
             ['first_name' => 'name', 'last_name' => 'surname'],
            //--Parsed Validation-------------
             ['first_name' => 'string|nullable', 'last_name' => 'string|nullable'],
            //---Request Filter-------
             ['first_name' => ['eq' => 'some name']],
            //---Parsed Filter--------
             [['where' => ['name', '=', 'some name']]]],

            //3------Fields--------------------
            [ ['name' => ['string', 'nullable'], 'surname' => ['string', 'nullable'] ],
            //---Parsed Fields-----------------
              ['name' => 'name', 'surname' => 'surname'],
            //---Parsed Validation-------------
              ['name' => ['string', 'nullable'], 'surname' => ['string', 'nullable']],
            //---Request filter----------------
              ['and' => ['name' => 'some name', 'surname' => 'last name']],
            //---Parsed Filter-----------------
              [['where' => [['where' => ['name', '=', 'some name']], ['where' => ['surname', '=', 'last name']]]]]],

            //4------Fields---------------------
            [['first_name' => 'name', 'last_name' => 'surname', 'email', 'age' ],
            //---Parsed Fields------------------
             ['first_name' => 'name', 'last_name' => 'surname', 'email' => 'email', 'age' => 'age'],
            //---Parsed Validation--------------
             [],
            //---Request Filter-----------------
             ['and' => [
                 'or' => [
                     'first_name' => 'some name',
                     'last_name' => 'last name'
                 ],
                 'and' => [
                     'email' => ['neq' => 'example@example.com'],
                     'age' => ['gt' => 20]
                 ]
             ]],
             //---Parsed Filter-------------------
             [['where' => [
                    ['orWhere' => [
                         ['where' => ['name', '=', 'some name']],
                         ['where' => ['surname', '=', 'last name']]]
                    ],
                    ['where' => [
                         ['where' => ['email', '!=', 'example@example.com']],
                         ['where' => ['age', '>', 20]]
                    ]]
                 ]
             ]]],

            //5-----Fields------------------------
            [['name', 'surname', 'email'],
            //------Parsed Fields-----------------
            ['name' => 'name', 'surname' => 'surname', 'email' => 'email'],
            //-----Parsed Validation--------------
            [],
            //---Request Filter-------------------
            ['and' => [
                'name' => ['in' => ['some name', 'name']],
                'surname' => ['neq' => 'hmmm']
                ],
            ],
            [['where' =>  [
                    ['whereIn' => ['name', ['some name', 'name']]],
                    ['where' => ['surname', '!=', 'hmmm']]
             ]]]
            ]
        ];
    }


    /**
     * @dataProvider dataProvider
     */
    public function testFieldsParser($fields, $parsedFields, $parsedValidation, $reqFilter, $parsedFilter)
    {
        $parser = new RequestParser();

        $parser->__init($fields);

        $this->assertEquals($parsedFields, $parser->getFields());

        $this->assertEquals($parsedValidation, $parser->getValidation());
    }


    /**
     * @dataProvider dataProvider
     */
    public function testParseRequestFilter($fields, $parsedFields, $parsedValidation, $reqFilter, $parsedFilter)
    {
        $parser = new RequestParser();

        $parser->setFields($parsedFields);
        $parser->setValidation($parsedValidation);

        $result = $parser->parseFilter($reqFilter);

        $this->assertEquals($parsedFilter,$result);
    }


}
