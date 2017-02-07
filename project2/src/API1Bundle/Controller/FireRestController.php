<?php
/**
 * Created by PhpStorm.
 * User: Luu Nhu
 * Date: 12/11/2016
 * Time: 4:50 PM
 */
namespace API1Bundle\Controller;

use API1Bundle\Common\Common;
use API1Bundle\Logic\FireLogic;
use API1Bundle\Entity\Fire;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use API1Bundle\FormatResponse\FormatResponse;
use API1Bundle\Utils\UserValidateHelper;

class FireRestController extends Controller
{
    //Lay thong tin cua hoa hoan
    public function postFireAction()
    {
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        $status = $array["status"];
        if (!$valid->validationLatitude($latitude) || !$valid->validationLongitude($longitude) || !$valid->validationStatus($status))
            return $formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->COMFIRM_FIRE_ERROR_REQUEST, null);
        $response = $fireLogic->getFire($latitude, $longitude, $status);
        if ($response === FAlSE) {
            return $formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->GET_FIRE_FAIL, null);
        }
        if ($response->get('Items')) {
            $arr = $response->get('Items')[0];
            $fire = new Fire($arr);
            return $formatResponse->reportFireResponse($common->RESULT_CODE_SUCCESS, $common->GET_FIRE_SUCCESSULLY, $fire);
        }
        return $formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->GET_FIRE_ERROR_NOT_FOUND, null);
    }

    // get hoa hoan xung quanh mot diem
    public function getFireLocalAction()
    {
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        if (!$valid->validationLatitude($latitude) || !$valid->validationLongitude($longitude)) {
            return $formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->GET_FIRE_LOCAL_ERROR_REQUEST, null);
        }
        $respone = $fireLogic->getFireLocal($latitude, $longitude);
        if ($respone === FALSE)
            return $formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->GET_FIRE_LOCAL_FAIL, null);
        if ($respone === null)
            return $formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->GET_FIRE_LOCAL_ERROR_NOT_FOUND, null);
        return $formatResponse->reportFireResponse($common->RESULT_CODE_SUCCESS, $common->GET_FIRE_LOCAL_SUCCESSULLY, $respone);
    }

    //get do tin cay cua thong tin hoa hoan
    public function getEvaluateAccurateFireAction()
    {
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        if (!$valid->validationLatitude($latitude) || !$valid->validationLongitude($longitude)) {
            return $formatResponse->getResultEvaluateAccurateAccident($common->RESULT_CODE_FAIL, $common->EVALUATE_ACCURATE_FIRE_ERROR_REQUEST, null);
        }
        $response = $fireLogic->getEvaluateAccurateFire($latitude, $longitude);
        if ($response === FALSE)
            return $formatResponse->getResultEvaluateAccurateAccident($common->RESULT_CODE_FAIL, $common->EVALUATE_ACCURATE_FIRE_FAIL, null);
        if ($response === null)
            return $formatResponse->getResultEvaluateAccurateAccident($common->RESULT_CODE_FAIL, $common->EVALUATE_ACCURATE_FIRE_ERROR_NOT_FOUND, null);

        return $formatResponse->getResultEvaluateAccurateAccident($common->RESULT_CODE_SUCCESS, $common->EVALUATE_ACCURATE_FIRE_SUCCESSFULLY, $response);

    }
}