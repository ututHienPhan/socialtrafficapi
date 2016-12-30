<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 16/11/2016
 * Time: 4:12 CH
 */

namespace API1Bundle\Logic;

use API1Bundle\Repository\MotoRepository;
use Symfony\Component\Validator\Constraints\True;

class MotoLogic
{
    private $motoRepository;

    function __construct($dynamodb)
    {
        $this->motoRepository = new MotoRepository($dynamodb);
    }

    public function getMotoInfo($username, $licenseplate)
    {
        $moto = $this->motoRepository->findByEmailAndLicensePlate($username, $licenseplate);
        return $moto;
    }

    public  function  insertNewMoto($username, $licenseplate, $ownername) {

        $response = $this->motoRepository->newMoto($username, $licenseplate, $ownername);
        return $response;
    }
    public function getMotos($username) {

        $response = $this->motoRepository->getMotos( $username);
        if($response === FALSE) {
            return FALSE;
        }
        if($response->get('Count') <= 0)
            return null;
        return $response;
    }

    public function getUsernames($licenseplate) {

        $response = $this->motoRepository->getUsername($licenseplate);
        if($response === FALSE)
            return FALSE;
        $numberUser = $response->get('Count');
        if($numberUser == 0)
            return null;
        $arrUser = array();
        for($i = 0; $i < $numberUser; $i++ ){
            array_push($arrUser, $response->get('Items')[$i]);
        }
        return $arrUser;
    }
}