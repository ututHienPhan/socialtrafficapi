<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 05/12/2016
 * Time: 10:22 SA
 */
namespace  API1Bundle\Repository;



class TokenRepository
{
    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'token';
    }

   /* public function getUserName($token) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#token' => 'token'
            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $token],

            ],
            'FilterExpression' => '#token = :val1',
            'Select' => 'ALL_ATTRIBUTES'
          //  'AttributesToGet' => [
           // 'username',
       // ],
        ]);

        return $response;
    } */

    public function getUsernameByToken($token) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#token' => 'token',

            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $token]

            ],
            'FilterExpression' => '#token = :val1',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

    public function findByUsername($username)
    {
        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'username' => ['S' => $username]
            ]
        ]);

        if ($response->get('Item')) {
            return new User($response->get('Item'));
        }
        return FALSE;
        //return $response;
    }

    // luu token moi
    public function newToken($username, $token, $time)
    {
        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'username' => ['S' => $username],
                'token' => ['S' => $token],
                'time' => ['S' => $time],
            ]
        ]);
        return $response;
    }

    //update token
    public function updateToken($username, $token, $time)
    {
        if ($this->findByUsername($username) === FALSE)
            return FALSE;
        else {
            $response = $this->dynamodb->updateItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'username' => ['S' => $username]
                ],
                'Exists' => ['B'],
                'ExpressionAttributeNames' => [
                    '#token' => 'token',
                    '#time' => 'time'
                ],
                'ExpressionAttributeValues' => [
                    ':val1' => ['S' => $token],
                    ':val2' => ['S' => $time],
                ],
                'UpdateExpression' => 'set #token = :val1, #time = :val2',
                'ReturnValues' => 'ALL_NEW'
            ]);
            return $response;
        }
    }

    //delete
    public function deleteToken($username)
    {
        $response = $this->dynamodb->deleteItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'username' => ['S' => $username]
                ],
            "ReturnValues" => "ALL_OLD"
            ]);
            return $response;
    }

}