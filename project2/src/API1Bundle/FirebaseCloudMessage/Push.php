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

    public function sendPushNotificationAccident($tokendevice, $ownername, $licenseplate)
    {
        $server_key = 'AIzaSyAbajvFIv0xO-TrqU3IETalKoOGScZ7vaM';
        $client = new Client();
        $client->setApiKey($server_key);
        $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());
        $message = new Message();
        $message->setPriority('high');
        $message->addRecipient(new Device($tokendevice));
        $message->setNotification(new Notification('Notify accident', 'Thông tin xe tai nạn: (Chủ xe: '.$ownername.', Bản số xe: '.$licenseplate));

        $response = $client->send($message);
        return $response;
    }

     public function sendPushNotificationFire($tokendevice, $ownername, $address)
    {
        $server_key = 'AIzaSyAbajvFIv0xO-TrqU3IETalKoOGScZ7vaM';
        $client = new Client();
        $client->setApiKey($server_key);
        $client->injectGuzzleHttpClient(new \GuzzleHttp\Client());
        $message = new Message();
        $message->setPriority('high');
        $message->addRecipient(new Device($tokendevice));
        $message->setNotification(new Notification('Notify fire', 'Thông tin hỏa hoạn: (Chủ nhà: '.$ownername.', Địa chỉ: '.$address));

        $response = $client->send($message);
        return $response;
    }

}