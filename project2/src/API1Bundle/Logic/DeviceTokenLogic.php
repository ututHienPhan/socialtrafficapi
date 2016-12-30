<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 22/12/2016
 * Time: 11:00 CH
 */

namespace API1Bundle\Logic;


use API1Bundle\Repository\DeviceTokenRepository;


class DeviceTokenLogic
{

    private $deviceTokenRepository;

    function __construct($dynamodb)
    {
        $this->deviceTokenRepository = new DeviceTokenRepository($dynamodb);
    }

    public function insertDeviceToken($username, $tokendevice) {
        $response = $this->deviceTokenRepository->newToken($username, $tokendevice, date('Y-m-d H:i:s'));
        return $response;
    }

    public function deleteDeviceToken($tokendevice) {
        $response = $this->deviceTokenRepository->deleteToken($tokendevice);
        return $response;
    }
}
