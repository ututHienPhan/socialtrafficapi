<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 23/12/2016
 * Time: 10:33 SA
 */
namespace API1Bundle\FirebaseCloudMessage;

use sngrl\PhpFirebaseCloudMessaging\Client;
use sngrl\PhpFirebaseCloudMessaging\Message;
use sngrl\PhpFirebaseCloudMessaging\Recipient\Device;
use sngrl\PhpFirebaseCloudMessaging\Notification;

class Push {

    public function sendPushNotification($tokendevice, $latitude, $longitude, $ownername, $licenseplate, $status)
    {
        $server_key = 'AIzaSyAbajvFIv0xO-TrqU3IETalKoOGScZ7vaM';
        $client = new Client();
        $client->setApiKey($server_key);
        $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());

        $message = new Message();
        $message->setPriority('high');
        $message->addRecipient(new Device($tokendevice));
        $message->setNotification(new Notification('Notify accident', 'Infomation accident'))
            ->setData(
                ['latitude' => $latitude],
                ['longitude' => $longitude],
                ['ownername' => $ownername],
                ['licenseplate' => $licenseplate],
                ['status' => $status]);

        $response = $client->send($message);
        var_dump($response->getStatusCode());
        var_dump($response->getBody()->getContents());
        return "";
    }

}