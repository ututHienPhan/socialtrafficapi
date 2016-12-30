<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 16/11/2016
 * Time: 4:12 CH
 */

namespace  API1Bundle\Repository;

use API1Bundle\Entity\Motor;


class MotoRepository
{
    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'motor';
    }

    // lay thong tin moto theo ten dang nhap nguoi dang ki va bang so xe
    public function findByEmailAndLicensePlate ($username, $licenseplate){
        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'licenseplate' => ['S' => $licenseplate],
                'username' => ['S' => $username]
            ]
        ]);

        return $response;
    }

    // Tao tai thong tin dang ki xe moi
    public function newMoto($username, $licenseplate, $ownername) {
        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'licenseplate' => ['S'  => $licenseplate],
                'username' => ['S'  => $username],
                'ownername' => ['S'  => $ownername],
            ]
        ]);
        return $response;
    }

    public function getMotos($username) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#username' => 'username',


            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $username],

            ],
            'FilterExpression' => '#username = :val1',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

    public function getUsername($licenseplate) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#licenseplate' => 'licenseplate',


            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $licenseplate],

            ],
            'FilterExpression' => '#licenseplate = :val1',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }
}