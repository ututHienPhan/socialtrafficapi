<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 16/11/2016
 * Time: 4:02 CH
 */

namespace API1Bundle\Controller;

use API1Bundle\Common\Common;
use API1Bundle\Entity\Accident;
use API1Bundle\Logic\AccidentLogic;
use API1Bundle\Logic\MotoLogic;
use API1Bundle\Logic\TokenLogic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use API1Bundle\FormatResponse\FormatResponse;
use API1Bundle\Utils\UserValidateHelper;

class AccidentRestController extends Controller {


    // Lay thong tin tai nan giao thong
    public function postAccidentAction()
    {
        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        $status = $array["status"];
        if(!$valid->validationLatitude($latitude) || !$valid->validationLongitude($longitude) || !$valid->validationStatus($status))
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->COMFIRM_ACCIDENT_ERROR_REQUEST, null);
        $response = $accidentLogic->getAccident($latitude, $longitude, $status);
        if($response === FAlSE) {
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->GET_ACCIDENT_FAIL, null);
        }
        if ($response->get('Items')) {
            $arr = $response->get('Items')[0];
            $accident = new Accident($arr);
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_SUCCESS, $common->GET_ACCIDENT_SUCCESSULLY, $accident);
        }
        return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->GET_ACCIDENT_ERROR_NOT_FOUND, null);

    }

    // get tai nan giao thong xung quanh mot diem
    public  function getAccidentsLocalAction() {

        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        if(!$valid->validationLatitude($latitude) || !$valid->validationLongitude($longitude)) {
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->GET_ACCIDENTS_LOCAL_ERROR_REQUEST, null);
        }
        $respone = $accidentLogic->getAccidentsLocal($latitude, $longitude);
        if($respone === FALSE)
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->GET_ACCIDENTS_LOCAL_FAIL, null);
        if($respone === null)
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->GET_ACCIDENTS_LOCAL_ERROR_NOT_FOUND, null);
        return $formatResponse->updateInfoResponse($common->RESULT_CODE_SUCCESS, $common->GET_ACCIDENTS_LOCAL_SUCCESSULLY, $respone);
    }

    //get do tin cay cua thong tin tai nan
    public function getEvaluateAccurateAccidentAction() {

        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        if(!$valid->validationLatitude($latitude) || !$valid->validationLongitude($longitude)) {
            return $formatResponse->getResultEvaluateAccurateAccident($common->RESULT_CODE_FAIL, $common->EVALUATE_ACCURATE_ACCIDENT_ERROR_REQUEST, null);
        }
        $response = $accidentLogic->getEvaluateAccurateAccident($latitude, $longitude);
        if($response===FALSE)
            return $formatResponse->getResultEvaluateAccurateAccident($common->RESULT_CODE_FAIL, $common->EVALUATE_ACCURATE_ACCIDENT_FAIL, null);
        if($response === null)
            return $formatResponse->getResultEvaluateAccurateAccident($common->RESULT_CODE_FAIL, $common->EVALUATE_ACCURATE_ACCIDENT_ERROR_NOT_FOUND, null);

        return $formatResponse->getResultEvaluateAccurateAccident($common->RESULT_CODE_SUCCESS, $common->EVALUATE_ACCURATE_ACCIDENT_SUCCESSFULLY, $response);

    }

    //api thong bao tai nan giao thong den dien thoai
    public function getNotificationAccidentAction() {

        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $motoLogic = new MotoLogic($this->get('aws.dynamodb'));
        $formatResponse = new FormatResponse();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $valid = new UserValidateHelper();
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $token = $array["token"];
        if(!$valid->validationIdToken($token)) {
            return $formatResponse->getNotification($common->RESULT_CODE_FAIL, $common->GET_NOTIFICATION_ACCIDENT_ERROR_REQUEST, null);
        }
        $username = $tokenLogic->getUsername($token);
        if($username === FALSE)
            return $formatResponse->getNotification($common->RESULT_CODE_FAIL, $common->GET_NOTIFICATION_ACCIDENT_FAIL, null);
        else if($username === NULL)
            return $formatResponse->getNotification($common->RESULT_CODE_FAIL, $common->GET_NOTIFICATION_ACCIDENT_ERROR_NOT_FOUND, null);
        else {
            $resposeMoto = $motoLogic->getMotos($username);
            if($resposeMoto === FALSE)
                return $formatResponse->getNotification($common->RESULT_CODE_FAIL, $common->GET_NOTIFICATION_ACCIDENT_FAIL, null);
            if($resposeMoto == null)
                return $formatResponse->getNotification($common->RESULT_CODE_FAIL, $common->GET_NOTIFICATION_ACCIDENT_ERROR_NOT_FOUND, null);
            $numberMoto = $resposeMoto->get('Count');
            $notify = array();
            for ($i = 0; $i < $numberMoto; $i++) {
                $licenseplate = $resposeMoto->get('Items')[$i]['licenseplate']['S'];
                $result = $accidentLogic->getLicenseplateAccident($licenseplate);
                if($result){
                    $acci = new Accident($result);
                    $ownername = $resposeMoto->get('Items')[$i]['ownername']['S'];
                    $arr = array('info_accident' => $acci, 'ownername' => $ownername);
                    array_push($notify, $arr);
                }
            }
            if(count($notify) == 0)
                return $formatResponse->getNotification($common->RESULT_CODE_FAIL, $common->GET_NOTIFICATION_ACCIDENT_ERROR_NOT_FOUND, null);
            return $formatResponse->getNotification($common->RESULT_CODE_SUCCESS, $common->GET_NOTIFICATION_ACCIDENT_SUCCESSULLY, $notify);
        }
    }



}