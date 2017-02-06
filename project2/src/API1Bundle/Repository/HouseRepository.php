<?php
/**
 * Created by PhpStorm.
 * User: 19872406
 * Date: 24/11/2016
 * Time: 8:21 CH
 */
namespace  API1Bundle\Repository;

use API1Bundle\Entity\HouseAddress;


class HouseRepository
{
    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'houseaddress';
    }

    public function findAddress ($id){
        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id],
            ]
        ]);

        return $response->get('Item');
    }

    public function getAddressByCoordinate($latitude, $longitude) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#latitude' => 'latitude',
                '#longitude' => 'longitude'


            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['N' => $latitude],
                ':val2' => ['N' => $longitude]

            ],
            'FilterExpression' => '#latitude = :val1 AND #longitude = :val2',
            'Select' => 'ALL_ATTRIBUTES'
        ]);
        return $response;
    }

    public function getAddressByCoordinateAndUser($latitude, $longitude, $username) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#username' => 'username',
                '#latitude' => 'latitude'
                /*'#longitude' => 'longitude' */
            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $username],
                ':val2' => ['S' => $latitude]
               /* ':val3' => ['N' => $longitude] */
            ],
            'FilterExpression' => '#username = :val1 AND #latitude = :val2', /*AND #longitude = :val3', */
            'Select' => 'ALL_ATTRIBUTES'
        ]);
        var_dump("123"); die;
        return $response;
    }

    public function newAddress($id, $username, $latitude, $longitude, $address, $ownername) {
        var_dump("123");die;
        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S' => $id],
                'username' => ['S'  => $username],
                'latitude' => ['N' => $latitude],
                'longitude' => ['N' => $longitude],
                'address' => ['S' => $address],
                'ownername' => ['S'  => $ownername],
            ]
        ]);
        return $response;
    }
}