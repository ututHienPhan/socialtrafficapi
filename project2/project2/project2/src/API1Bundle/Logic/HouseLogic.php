<?php
/**
 * Created by PhpStorm.
 * User: 19872406
 * Date: 24/11/2016
 * Time: 8:24 CH
 */
namespace API1Bundle\Logic;

use API1Bundle\Repository\HouseRepository;
use Symfony\Component\Validator\Constraints\True;

class HouseLogic
{
    private $houseRepository;

    function __construct($dynamodb)
    {
        $this->houseRepository = new HouseRepository($dynamodb);
    }

    public function getHouseInfoById($id)
    {
        $house = $this->houseRepository->findAddress($id);
        return $house;
    }

    public function getHouseInfo($latitude, $longitude)
    {
        $house = $this->houseRepository->getAddressByCoordinate($latitude, $longitude);
        if($house === FALSE)
            return FALSE;
        return $house;
    }

    public function getHouseInfoByUsername($latitude, $longitude, $username)
    {
        $house = $this->houseRepository->getAddressByCoordinate($latitude, $longitude, $username);
        return $house;
    }

    public function insertNewAddress($id, $username, $latitude, $longitude, $address, $ownername) {
        $response = $this->houseRepository->newAddress($id, $username, $latitude, $longitude, $address, $ownername);
        return $response;
    }
}