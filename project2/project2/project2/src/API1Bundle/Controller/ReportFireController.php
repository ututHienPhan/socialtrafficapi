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
        else {
            $reportfirelogic = new ReportFireLogic($this->get('aws.dynamodb'));
            $response = $reportfirelogic->insertReportFire($username, $latitude, $longitude, $timestart, $status,
                $description, $image, $address, $level);
            if ($response === FALSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REPORT_FIRE_FAIL);
            } else {
                //find address in database
                $houselogic = new HouseLogic($this->get('aws.dynamodb'));
                $firelogic = new FireLogic($this->get('aws.dynamodb'));
                $house = $houselogic->getHouseInfo($latitude, $longitude);
                $fire = $firelogic->getEvaluateAccurateFire($latitude, $longitude);
                if($house && $house->get('Count') > 0 && $fire === 100)
                {
                    //notify if found address in database & evaluate = 100
                    $numberReceiver = $house->get('Count');//so tai khoan dang ki dia chi nay
                    //$receiverArray = array();//danh sach username da dang ki dia chi nay
                    for($i = 0; $i < $numberReceiver; $i++)
                    {
                        $item = $house->get('Items')[$i]['username']['S'];
                        //send notify to receiver
                        $sendNotify = $reportfirelogic->sendNotificationGCM("Message", "d-tBRQgYL2M:APA91bGYLtYucBX70v4x6YqAYUU5BZpxmu8WnVnd6SV13WSBDOfM-dI7WEl1Lp4gEyzVNrNumYgRI3LajOLg67zbECjUtC-vgjzXo_QmGDupqer5AO8FGr928oJu5bXsjqF6f0zCHNvD");
                        //array_push($receiverArray, $item);
                    }
                }
                return $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REPORT_FIRE_SUCCESSFULLY);
            }
        }
    }
}
