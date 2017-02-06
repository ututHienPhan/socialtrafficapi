<?php
/**
 * Created by PhpStorm.
 * User: Luu Nhu
 * Date: 12/11/2016
 * Time: 4:50 PM
 */
namespace API1Bundle\Controller;

use API1Bundle\Common\Common;
use API1Bundle\Logic\ReportFireLogic;
use API1Bundle\Logic\TokenLogic;
use API1Bundle\Logic\FireLogic;
use API1Bundle\Logic\HouseLogic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use API1Bundle\FormatResponse\FormatResponse;
use API1Bundle\Utils\UserValidateHelper;

class ReportFireController extends Controller
{
    // api lay thong tin report fire
    public function getAction()
    {
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
        $status = $array["status"];
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        $reportfirelogic = new ReportFireLogic($this->get('aws.dynamodb'));
        $reponse = $reportfirelogic->getReportFireByCoordinate($status, $latitude, $longitude);
        return $reponse;
    }


    // api gui xac nhan hoa hoan
    public function comfirmFireAction()
    {
        $common = new Common();
        $registerResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
        $token = $array['token'];
        $latitude = $array['latitude'];
        $longitude = $array['longitude'];
        $agree = $array['agree'];
        $disagree = $array['disagree'];
        $status = $array['status'];
        $time = $array['time'];
        //test validate
        if (!$valid->validationIdToken($token) || !$valid->validationLatitude($latitude) ||
            !$valid->validationLongitude($longitude) || !$valid->validationAgree($agree) ||
            !$valid->validationDisagree($disagree) || !$valid->validationStatus($status) ||
            !$valid->validationTime($time)
        )
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_FIRE_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if ($username === FALSE)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_FIRE_FAIL);
        else if ($username === NULL)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_FIRE_ERROR_NOT_FOUND);
        else {
            $reportfirelogic = new ReportFireLogic($this->get('aws.dynamodb'));
            $firelogic = new FireLogic($this->get('aws.dynamodb'));
            $fire = $firelogic->getFire($latitude, $longitude, $status);
            if ($fire === FALSE)
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_FIRE_FAIL);
            if (!$fire->get("Items")) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_FIRE_HANDLED);
            }
            $id_fire = $fire->get("Items")[0]["id"]["S"];
            $reponse = $reportfirelogic->comfirmFire($username, $latitude, $longitude, $agree, $disagree, $status, $time, $id_fire);
            if ($reponse === FALSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->COMFIRM_FIRE_FAIL);
            }
            return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->COMFIRM_FIRE_SUCCESSULLY);
        }
    }

    //api thông báo hỏa hoạn đã được xử lý
    public function reportFireHandledAction()
    {
        $common = new Common();
        $registerResponse = new FormatResponse();
        $valid = new UserValidateHelper();

        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
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
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_HANDLED_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if ($username === FALSE)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_HANDLED_FAIL);
        else if ($username === NULL)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_HANDLED_ERROR_NOT_FOUND);
        else {
            $reportfirelogic = new ReportFireLogic($this->get('aws.dynamodb'));
            $firelogic = new FireLogic($this->get('aws.dynamodb'));
            $fire = $firelogic->getFire($latitude, $longitude, "no handle");
            if ($fire === FALSE)
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_HANDLED_FAIL);
            if (!$fire->get("Items")) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_HANDLED);
            }
            $id_fire = $fire->get("Items")[0]["id"]["S"];
            $response = $reportfirelogic->reportFireHandled($username, $latitude, $longitude, $status, $timeend, $id_fire);
            if ($response === FALSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_HANDLED_FAIL);
            }
            return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REPORT_FIRE_HANDLED_SUCCESSFULLY);
        }
    }

    // api thong bao hoa hoan
    public function postReportFireAction()
    {
        $common = new Common();
        $registerResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
        $token = $array["token"];
        $latitude = $array["latitude"];
        $longitude = $array["longitude"];
        $timestart = $array["timestart"];
        $description = $array["description"];
        $image = $array["image"];
        $address = $array['address'];
        $level = $array['level'];
        $status = $array["status"];

        if (!$valid->validationIdToken($token) || !$valid->validationLatitude($latitude) ||
            !$valid->validationLongitude($longitude) || !$valid->validationDescription($description) ||
            !$valid->validationImage($image) || !$valid->validationStatus($status) ||
            !$valid->validationTime($timestart) || !$valid->validationLevel($level) ||
            !$valid->validationAddress($address)
        )
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if ($username === FALSE)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_FAIL);
        else if ($username === NULL)
            return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_ERROR_NOT_FOUND);
        else { //tai khoan ton tai

            $reportfirelogic = new ReportFireLogic($this->get('aws.dynamodb'));
            $houseLogic = new HouseLogic($this->get('aws.dynamodb'));

             // xac nhan hoa hoan da duoc report chua
            $resultComf = $reportfirelogic->getReportFireByCoordinate($status, $latitude, $longitude);

            //arr username da dang ki nha gap hoa hoan
            $arrUser = $houseLogic->getUsernames($latitude, $longitude);
            if($resultComf) { // hoa hoan da duoc report roi
                $id_fire = $resultComf['id']['S'];
                $latitude = $resultComf['latitude']['N'];
                $longitude = $resultComf['longitude']['N'];
                $address = $resultComf['address']['S'];
                //xu ly vao phan xac nhan tai nan
                $reponse =  $reportfirelogic->comfirmFire($username, $latitude, $longitude, '1', '0', $status, $time, $id_fire);
                if($reponse === FALSE)
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_FAIL);
                //push thong bao tai nan giao thong
                var_dump('124');die;
                $result = $reportfirelogic->pushNotify($address, $latitude, $longitude, $arrUser);
                
                if($result)
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REPORT_FIRE_SUCCESSFULLY );
                return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->PUSH_NOTIFICATION_FAIL);
            }
            
            // hoa hoan chua duoc report
            $response = $reportfirelogic->insertReportFire($username, $latitude, $longitude, $timestart, $status,
                $description, $image, $address, $level);

            if ($response === FALSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_FAIL);
            } else {
                //push thong bao hoa hoan
                $result = $reportfirelogic->pushNotify($address, $latitude, $longitude, $arrUser);
                if($result)
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REPORT_FIRE_SUCCESSFULLY );

                return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->PUSH_NOTIFICATION_FAIL);
            }
        }
    }
}
