<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 22/12/2016
 * Time: 10:54 CH
 */
namespace  API1Bundle\Repository;



class DeviceTokenRepository
{
    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'device';
    }

    public function findByUsername($username) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#username' => 'username',

            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $username]

            ],
            'FilterExpression' => '#username = :val1',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

    public function findByToken($token)
    {
        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'token' => ['S' => $token]
            ]
        ]);

        if ($response->get('Item')) {
            return new $response->get('Item');
        }
        return FALSE;

    }

    // luu token moi
    public function newToken($username, $token, $time)
    {
        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'username' => ['S' => $username],
                'token' => ['S' => $token], //key
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
    public function deleteToken($tokendevice)
    {
        $response = $this->dynamodb->deleteItem([
            'TableName' => $this->tableName,
            'Key' => [
                'token' => ['S' => $tokendevice]
            ],
            "ReturnValues" => "ALL_OLD"
        ]);
        return $response;
    }

    //
    public function getAllToken() {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

}