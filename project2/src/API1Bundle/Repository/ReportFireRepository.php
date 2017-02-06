<?php
/**
 * Created by PhpStorm.
 * User: Luu Nhu
 * Date: 12/11/2016
 * Time: 3:54 PM
 */
namespace  API1Bundle\Repository;
use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

class ReportFireRepository
{
    private $dynamodb;
    private $tableName;

    function __construct($dynamodb)
    {
        $this->dynamodb = $dynamodb;
        $this->tableName = 'report_fire';
    }

    public function getReportFireById($id)
    {

        $response = $this->dynamodb->getItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ]
        ]);
        return $response;
    }

    // tra ve dong du lieu report_fire
    public function getReportFireByCoordinate($status, $latitude, $longitude)
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

    //Them dong du lieu vao bang report_fire khi thong bao hoa hoan
    public function insertReportFire($id, $username, $latitude, $longitude, $timestart, $status,
                                         $description, $image, $address, $level)
    {

        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S' => $id],
                'username' => ['S' => $username],
                'latitude' => ['N' => (string)$latitude],
                'longitude' => ['N' => (string)$longitude],
                'address' => ['S' => $address],
                'time' => ['S' => $timestart],
                'levelA' => ['S' => $level],
                'desciption' => ['S' => $description],
                'image' => ['S' => $image],
                'statusA' => ['S' => $status]
            ]
        ]);
        return $response;
    }

    // update them thuoc tinh id_fire report_fire
    public function updateReportFire($id, $id_fire)
    {
        $response = $this->dynamodb->updateItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ],
            'Exists' => ['B'],
            'ExpressionAttributeNames' => [
                '#id_fire' => 'id_fire'

            ],
            'ExpressionAttributeValues' => [
                ':val1' => ['S' => $id_fire],

            ],
            'UpdateExpression' => 'set #id_fire = :val1',
            'ReturnValues' => 'ALL_NEW'
        ]);
        return $response;
    }

    // Them dong du lieu vao bang report_fire va cap nhap lai bang fire
    public function comfirmFire($username, $latitude, $longitude, $agree, $disagree, $status, $time, $id_fire, $id)
    {
        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S' => $id],
                'username' => ['S' => $username],
                'latitude' => ['N' => (string)$latitude],
                'longitude' => ['N' => (string)$longitude],
                'agree' => ['N' => (string)$agree],
                'disagree' => ['N' => (string)$disagree],
                'time' => ['S' => $time],
                'statusA' => ['S' => $status],
                'id_fire' => ['S' => $id_fire]

            ]
        ]);
        return $response;
    }

    //Xoa report_fire
    public function delete($id)
    {

        $response = $this->dynamodb->deleteItem([
            'TableName' => $this->tableName,
            'Key' => [
                'id' => ['S' => $id]
            ]
        ]);
        return $response;
    }

    // report fire handled
    public function insertReportFireHandled($id, $username, $latitude, $longitude, $time, $status, $id_fire)
    {
        $response = $this->dynamodb->putItem([
            'TableName' => $this->tableName,
            'Item' => [
                'id' => ['S' => $id],
                'username' => ['S' => $username],
                'latitude' => ['N' => (string)$latitude],
                'longitude' => ['N' => (string)$longitude],
                'time' => ['S' => $time],
                'statusA' => ['S' => $status],
                'id_fire' => ['S' => $id_fire]
            ]
        ]);
        return $response;
    }

    public function sendPushNotification($data, $ids)
    {
        $server_key = 'AIzaSyAbajvFIv0xO-TrqU3IETalKoOGScZ7vaM';
        $client = new Client();
        $client->setApiKey($server_key);
        $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());

        $message = new Message();
        $message->setPriority('high');
        $message->addRecipient(new Device('d-tBRQgYL2M:APA91bGYLtYucBX70v4x6YqAYUU5BZpxmu8WnVnd6SV13WSBDOfM-dI7WEl1Lp4gEyzVNrNumYgRI3LajOLg67zbECjUtC-vgjzXo_QmGDupqer5AO8FGr928oJu5bXsjqF6f0zCHNvD'));
        $message
            ->setNotification(new Notification('some title', 'cong hoa xa hoi chu nghia viet nam'))
            ->setData(['key' => 'value'])
        ;

        $response = $client->send($message);
        var_dump($response->getStatusCode());
        var_dump($response->getBody()->getContents());
        return "";
    }
}