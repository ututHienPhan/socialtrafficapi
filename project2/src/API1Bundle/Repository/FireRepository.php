<?php
/**
 * Created by PhpStorm.
 * User: Luu Nhu
 * Date: 12/11/2016
 * Time: 4:20 PM
 */
namespace  API1Bundle\Repository;
use API1Bundle\Reference;

class FireRepository
{

    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'fire';
    }

    public function getFireByStatus($status)
    {
        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeValues' => [
                ':val1' => ['S' => $status]
            ],
            'FilterExpression' => 'statusA = :val1',
        ]);

        return $response;
    }

    public function getFire($latitude, $longitude, $status)
    {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#statusA' => 'statusA',
                '#latitude' => 'latitude',
                '#longitude' => 'longitude'

            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['S' => $status],
                ':val2' => ['N' => (string)$latitude],
                ':val3' => ['N' => (string)$longitude]
            ],
            'FilterExpression' => '#statusA = :val1 AND #latitude = :val2 AND #longitude = :val3',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

    //get fire theo id
    public function getFireById($id)
    {

        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ]
        ]);
        return $response;
    }

    //insert new fire
    public function insertFire($id, $latitude, $longitude, $timestart, $status,
                                   $description, $image, $address, $level)
    {
        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S' => $id],
                'latitude' => ['N' => (string)$latitude],
                'longitude' => ['N' => (string)$longitude],
                'address' => ['S' => $address],
                'timestart' => ['S' => $timestart],
                'levelA' => ['S' => $level],
                'desciption' => ['S' => $description],
                'image' => ['S' => $image],
                'statusA' => ['S' => $status],
                'agree' => ['N' => '0'],
                'disagree' => ['N' => '0']

            ]
        ]);
        return $response;
    }

    //update fire by comfirm
    public function updateFireByComfirm($id, $agree, $disagree)
    {
        $response = $this->dynamodb->updateItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ],
            'Exists' => ['B'],
            'ExpressionAttributeNames' => [
                '#agree' => 'agree',
                '#disagree' => 'disagree'
            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['N' => (string)$agree],
                ':val2' => ['N' => (string)$disagree]
            ],
            'UpdateExpression' => 'set #agree = :val1, #disagree = :val2',
            'ReturnValues' => 'ALL_NEW'
        ]);
        return $response;
    }

    //update fire handled
    public function updateFireHandled($id, $status)
    {
        $response = $this->dynamodb->updateItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ],
            'Exists' => ['B'],
            'ExpressionAttributeNames' => [
                '#status' => 'statusA',
            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['S' => $status],
            ],
            'UpdateExpression' => 'set #status = :val1',
            'ReturnValues' => 'ALL_NEW'
        ]);
        return $response;
    }


}