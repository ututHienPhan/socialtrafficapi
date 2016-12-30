<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 26/11/2016
 * Time: 10:10 SA
 */

namespace  API1Bundle\Repository;


use API1Bundle\FirebaseCloudMessage\Firebase;
use API1Bundle\FirebaseCloudMessage\Push;
use API1Bundle\Repository\DeviceTokenRepository;

class ReportAccidentRepository {

    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'report_accident';
    }

    public function getReportAccidentById($id) {

        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ]
        ]);
        return $response;
    }

    // tra ve dong du lieu report_accident
    public function getReportAccidentByCoordinate($status, $latitude, $longitude) {

        $response = $this->dynamodb->scan([
            'TableName' => $this->tableName,
            'ExpressionAttributeNames' => [
                '#statusA' => 'statusA',
                '#latitude' => 'latitude',
                '#longitude' => 'longitude'

            ],
            'ExpressionAttributeValues' =>  [
                ':val1' => ['S' => $status],
                ':val2' => ['N' => $latitude],
                ':val3' => ['N' => $longitude]
            ],
            'FilterExpression' => '#statusA = :val1 AND #latitude = :val2 AND #longitude = :val3',
            'Select' => 'ALL_ATTRIBUTES'
        ]);

        return $response;
    }

    //Them dong du lieu vao bang report_accident khi thong bao tai nan giao thong
    public function insertReportAccident($id, $username, $latitude, $longitude, $timestart, $status,
                                         $description, $image, $licenseplate, $level){

        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S'  => $id],
                'username' => ['S' => $username],
                'latitude' => ['N'  => $latitude],
                'longitude' => ['N'  => $longitude],
                'licenseplate' => ['S' => $licenseplate],
                'time' => ['S'  => $timestart],
                'levelA' => ['S' => $level],
                'desciption' => ['S' => $description],
                'image' => ['S' => $image],
                'statusA' => ['S'  => $status]

            ]
        ]);
        return $response;
    }

    // update them thuoc tinh id_accident report_accident
    public function updateReportAccident($id, $id_accident) {

        $response = $this->dynamodb->updateItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ],
            'Exists' => ['B'],
            'ExpressionAttributeNames' => [
                '#id_accident' => 'id_accident'

            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['S' => $id_accident],

            ],
            'UpdateExpression' => 'set #id_accident = :val1',
            'ReturnValues' => 'ALL_NEW'
        ]);
        return $response;
    }

    // Them dong du lieu vao bang report_accident va cap nhap lai bang accident
       public function comfirmAccident($username, $latitude, $longitude, $agree, $disagree, $status, $time, $id_accident, $id) {

            $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S'  => $id],
                'username' => ['S' => $username],
                'latitude' => ['N'  => $latitude],
                'longitude' => ['N'  => $longitude],
                'agree' => ['N' => $agree],
                'disagree' => ['N'  => $disagree],
                'time' => ['S' => $time],
                'statusA' => ['S'  => $status],
                'id_accident' => ['S' => $id_accident]

            ]
        ]);
        return $response;
        }

        //Xoa report_accident
        public function  delete($id) {

            $response = $this->dynamodb->deleteItem([
                'TableName' => $this->tableName,
                'Key' => [
                    'id' => ['S' => $id]
                ]
            ]);
            return $response;
        }

    // report accident handled
    public function insertReportAccidentHandled($id, $username, $latitude, $longitude, $time, $status, $id_accident)
    {

        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S' => $id],
                'username' => ['S' => $username],
                'latitude' => ['N' => $latitude],
                'longitude' => ['N' => $longitude],
                'time' => ['S' => $time],
                'statusA' => ['S' => $status],
                'id_accident' => ['S' => $id_accident]
            ]
        ]);
        return $response;
    }


}