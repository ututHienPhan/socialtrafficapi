<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 26/11/2016
 * Time: 10:17 SA
 */

namespace API1Bundle\Controller;

use API1Bundle\Common\Common;
use API1Bundle\FirebaseCloudMessage\Push;
use API1Bundle\Logic\MotoLogic;
use API1Bundle\Logic\ReportAccidentLogic;
use API1Bundle\Logic\TokenLogic;
use API1Bundle\Logic\AccidentLogic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use API1Bundle\FormatResponse\FormatResponse;
use API1Bundle\Utils\UserValidateHelper;
use Symfony\Component\Validator\Constraints\Count;
use API1Bundle\Reference\Reference;

class ReportAccidentController extends Controller {

    // api lay thong tin report accident
    public function getAction() {
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $status = $array["status"];
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        $reportaccidentlogic = new ReportAccidentLogic($this->get('aws.dynamodb'));
        $reponse = $reportaccidentlogic->getReportAccidentByCoordinate($status, $latitude, $longitude);
        return $reponse;
    }

    // api thong bao tai nan giao thong
    public function postReportAccidentAction() {

        $common = new Common();
        $registerResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $motoLogic = new MotoLogic($this->get('aws.dynamodb'));
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $token = $array["token"];
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        $timestart = $array["timestart"];
        $description = $array["description"];
        $image = $array["image"];
        $licenseplate = $array['licenseplate'];
        $level = $array['level'];
        $status = $array["status"];
        if(!$valid->validationIdToken($token) || !$valid->validationLatitude($latitude) ||
            !$valid->validationLongitude($longitude) || !$valid->validationDescription($description) ||
            !$valid->validationImage($image) || !$valid->validationStatus($status) ||
            !$valid->validationTime($timestart) || !$valid->validationLevel($level) ||
            !$valid->validationLicenseplate($licenseplate))
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);

        if($username === FALSE)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_FAIL);
        else if($username === NULL)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_ERROR_NOT_FOUND);
        else { // tai khoan ton tai
            $reportaccidentlogic = new ReportAccidentLogic($this->get('aws.dynamodb'));
            // xac nhan tai nan da duoc report chua
            $resultComf = $reportaccidentlogic->comfirmAccidentByCoordinate($latitude, $longitude); 
            //arr username da dang ki ban so xe gap tai nan
            $arrUser = $motoLogic->getUsernames($licenseplate);
            if($resultComf) { // tai nan da duoc report roi
                $id_accident = $resultComf['id']['S'];
                $latitude = $resultComf['latitude']["N"];
                $longitude = $resultComf['longitude']["N"];
                //xu ly vao phan xac nhan tai nan
                $reponse =  $reportaccidentlogic->comfirmAccident($username, $latitude, $longitude, '1', '0', $status, $timestart, $id_accident);
                if($reponse === FALSE)
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_FAIL);
                //push thong bao tai nan giao thong
                $result = $reportaccidentlogic->pushNotify($licenseplate, $arrUser);
                if($result)
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REPORT_ACCIDENT_SUCCESSFULLY );
                return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->PUSH_NOTIFICATION_FAIL);
            }
            //tai nan giao thong chua duoc report
            $reponse = $reportaccidentlogic->insertReportAccident($username, $latitude, $longitude, $timestart, $status,
                $description, $image, $licenseplate, $level);
            if ($reponse === FALSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_FAIL);
            } else {
                //push thong bao tai nan giao thong
                $result = $reportaccidentlogic->pushNotify($licenseplate, $arrUser);
                if($result)
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REPORT_ACCIDENT_SUCCESSFULLY );

                return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->PUSH_NOTIFICATION_FAIL);
            }
        }
    }

    // api gui xac nhan tai nan giao thong
    public function comfirmAccidentAction() {

        $common = new Common();
        $registerResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $token = $array['token'];
        $latitude = $array['latitude'];
        $longitude = $array['longitude'];
        $agree = $array['agree'];
        $disagree = $array['disagree'];
        $status = $array['status'];
        $time = $array['time'];
        //test validate
        if(!$valid->validationIdToken($token) || !$valid->validationLatitude($latitude) ||
            !$valid->validationLongitude($longitude) || !$valid->validationAgree($agree) ||
            !$valid->validationDisagree($disagree) || !$valid->validationStatus($status) ||
            !$valid->validationTime($time))
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_ACCIDENT_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if($username === FALSE) 
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_ACCIDENT_FAIL);
        else if($username === NULL) 
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_ACCIDENT_ERROR_NOT_FOUND);
        else {
            $reportaccidentlogic = new ReportAccidentLogic($this->get('aws.dynamodb'));
            $accidentlogic = new AccidentLogic($this->get('aws.dynamodb'));
            $accident = $accidentlogic->getAccident($latitude, $longitude, $status);
            if($accident === FALSE)
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_ACCIDENT_FAIL);
            if(!$accident->get("Items")) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_ACCIDENT_HANDLED);
            }
            $id_accident = $accident->get("Items")[0]["id"]["S"];
            $reponse = $reportaccidentlogic->comfirmAccident($username, $latitude, $longitude, $agree, $disagree, $status, $time, $id_accident);
            if ($reponse === FALSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_ACCIDENT_FAIL);
            }
            return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->COMFIRM_ACCIDENT_SUCCESSULLY);
        }

    }

    //api thông báo tai nạn giao thông đã được xử lý
    public function reportAccidentHandledAction()
    {

        $common = new Common();
        $registerResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $token = $array['token'];
        $latitude = $array['latitude'];
        $longitude = $array['longitude'];
        $status = $array['status'];
        $timeend = $array['time'];
        //test validate
        if (!$valid->validationIdToken($token) || !$valid->validationLatitude($latitude) ||
            !$valid->validationLongitude($longitude) || !$valid->validationStatus($status) ||
            !$valid->validationTime($timeend)
        )
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_HANDLED_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if ($username === FALSE)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_HANDLED_FAIL);
        else if ($username === NULL)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_HANDLED_ERROR_NOT_FOUND);
        else { // tai khoan ton tai
            $reportaccidentlogic = new ReportAccidentLogic($this->get('aws.dynamodb'));
            $accidentlogic = new AccidentLogic($this->get('aws.dynamodb'));
            $accident = $accidentlogic->getAccident($latitude, $longitude, "no handle");
            if ($accident === FALSE) // error
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_HANDLED_FAIL);
            if (!$accident->get("Items")) {  
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_HANDLED);
            }
            $id_accident = $accident->get("Items")[0]["id"]["S"];
            $response = $reportaccidentlogic->reportAccidentHandled($username, $latitude, $longitude, $status, $timeend, $id_accident);
            if ($response === FALSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_ACCIDENT_HANDLED_FAIL);
            }

            return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REPORT_ACCIDENT_HANDLED_SUCCESSFULLY);
        }
    }


    public function testAction()
    {

        $ref = new Reference();
       return $ref->getDistanceBetweenPointsNew(10.7627, 106.682, 10.7594, 106.6744);
    }

}

