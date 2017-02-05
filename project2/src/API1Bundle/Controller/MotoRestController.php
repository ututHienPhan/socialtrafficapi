<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 16/11/2016
 * Time: 4:02 CH
 */

namespace API1Bundle\Controller;

use API1Bundle\Common\Common;
use API1Bundle\Logic\MotoLogic;
use API1Bundle\Logic\TokenLogic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use API1Bundle\FormatResponse\FormatResponse;
use API1Bundle\Utils\UserValidateHelper;

class MotoRestController extends Controller {

    // api dang ki thong tin xe
    public function postMotoAction() {
        
        $common = new Common();
        $registerResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $motoLogic = new MotoLogic($this->get('aws.dynamodb'));
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $datas = $this->get('request')->getContent();
        var_dump("123");die;
        $array = json_decode($datas, true);
        $token = $array["token"];
        $licenseplate = $array["licenseplate"];
        $ownername = $array["ownername"];

        if(!$valid->validationIdToken($token) || !$valid->validationOwnername($ownername) ||
            !$valid->validationLicenseplate($licenseplate))
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if($username === FALSE)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_MOTO_FAIL);
        else if($username === NULL)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_MOTO_ERROR_NOT_FOUND);
        else {
            $moto = $motoLogic->getMotoInfo($username, $licenseplate);
            if($moto === FAlSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_MOTO_FAIL);
            }
            if(!$moto->get('Item')) {

                $result = $motoLogic->insertNewMoto($username, $licenseplate, $ownername);
                if($result === FALSE) {
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_MOTO_FAIL);
                }

                return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REGISTER_MOTO_SUCCSESSFULLY);

            }

            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTER_MOTO_INFO_EXISTED);
        }

    }

    public  function  getMotoAction()
    {


    }
}