<?php
/**
 * Created by PhpStorm.
 * User: 19872406
 * Date: 24/11/2016
 * Time: 8:26 CH
 */
namespace API1Bundle\Controller;

use API1Bundle\Common\Common;
use API1Bundle\Logic\HouseLogic;
use Aws\DynamoDb\DynamoDbClient;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use API1Bundle\FormatResponse\FormatResponse;
use API1Bundle\Utils\UserValidateHelper;
use API1Bundle\Logic\TokenLogic;

class HouseRestController extends Controller
{
    public function getHouseAction($latitude, $longtitude, $username){

        $houseLogic = new HouseLogic($this->get('aws.dynamodb'));
        $house = $houseLogic->getHouseInfoByUsername($latitude, $longtitude, $username);
        if($house === FALSE){
            throw $this->createNotFoundException();
        }
        $view = View::create();
        $view->setData($house)->setStatusCode(200);
        return $view;
    }

    public function postHouseAction()
    {
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $houseLogic = new HouseLogic($this->get('aws.dynamodb'));
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
        $token = $array["token"];
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        $address = $array["address"];
        $ownername = $array["ownername"];
        if(!$valid->validationIdToken($token))
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_ADDRESS_TOKEN_ERROR);
        $username = $tokenLogic->getUsername($token);
        if($username === FALSE)
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_ADDRESS_FAIL);
        else if($username === NULL)
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_ADDRESS_TOKEN_ERROR_NOT_FOUND);
        else {
            if (!$valid->validationLatitude($latitude) || !$valid->validationAddress($address) ||
                !$valid->validationLongitude($longitude) || !$valid->validationOwnername($ownername)) {
                return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_ADDRESS_ERROR_INPUT);
            }

           $result = $houseLogic->getHouseInfoByUsername($latitude, $longitude, $username);
            if($result->get('Count') > 0) {
                return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_ADDRESS_EXISTED);
            } else { 
                $id = uniqid();
                $houseAddress = $houseLogic->insertNewAddress($id, $username, $latitude, $longitude, $address, $ownername);
                if($houseAddress === FALSE)
                    return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->REGISTER_ADDRESS_FAIL, null);
                return $formatResponse->updateInfoResponse($common->RESULT_CODE_SUCCESS, $common->REGISTER_ADDRESS_SUCCESSFULLY, $houseLogic->getHouseInfoById($id));
            }
            
        }
    }
}