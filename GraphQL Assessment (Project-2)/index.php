<?php
    require('../vendor/autoload.php');

    use GraphQL\Type\Definition\ObjectType;
    use GraphQL\Type\Definition\Type;
    use GraphQL\Type\Definition\Schema;
    use GraphQL\GraphQL;

    try{

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'echo' => [
                    'type' => Type::string(),
                    'args' => [
                        'message' => ['type' => Type::string()],
                    ],
                    'resolve' => function ($rootValue, $args) {
                        return $rootValue['prefix'] . $args['message'];
                    }
                ],
            ],
    ]);

    $mutationType = new ObjectType([

        'name' => 'Calc',

        'fields' => [

            'sum' => [
                'type' => Type::int(),
                'args' => [
                    'x' => ['type' => Type::int()],
                    'y' => ['type' => Type::int()],
                    // X and Y will store

                ],

                'resolve' => function ($calc, $args) {
                    return $args['x'] + $args['y'];

                },
            ],
        ],
    ]);
    $schema = new Schema([
        'query' => $queryType,
        'mutation'=> $mutationType
    ]);
    $rawInput = file_get_contents('php://input');

    $input = json_decode($rawInput,true);

    $query = $input['query'];

    $variableValues = isset($input['variables']) ? $input['variables'] : null;

    $rootValue = ['prefix'=>'You said: '];

    $result = GraphQL::executeQuery($schema,$query,$rootValue,null,$variableValues);

    $output = $result->toArray();
    }
    // catch exception
    catch(\Exception $e){
        $output = [
            'error'=>[
                'message' => $e->getMessage()
            ]
        ];
    }
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($output);
?>