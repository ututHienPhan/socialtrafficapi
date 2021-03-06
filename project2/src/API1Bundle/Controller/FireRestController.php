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
        $distance = $array["distance"];
        if (!$valid->validationLatitude($latitude) || !$valid->validationLongitude($longitude) || !$valid->validationDistance($distance)) {
            $view = View::create();
            $view->setData($formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->GET_FIRE_LOCAL_ERROR_REQUEST, null))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
            return $view;
        }
        $respone = $fireLogic->getFireLocal($latitude, $longitude, $distance);
        if ($respone === FALSE) {
            $view = View::create();
            $view->setData($formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->GET_FIRE_LOCAL_FAIL, null))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
            return $view;
        }
        if ($respone === null){
            $view = View::create();
            $view->setData($formatResponse->reportFireResponse($common->RESULT_CODE_FAIL, $common->GET_FIRE_LOCAL_ERROR_NOT_FOUND, null))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
            return $view;
        }
        $view = View::create();
        $view->setData($formatResponse->reportFireResponse($common->RESULT_CODE_SUCCESS, $common->GET_FIRE_LOCAL_SUCCESSULLY, $respone))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
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

    //thong ke hoa hoan theo ngay
    public function fireStatisticalByDateAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $date = date('Y/m/d');
        if(!$valid->validationDate($date)){
            $view = View::create();
            $view->setData($formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_DATE_ERROR_REQUEST))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
            return $view;
        }
        $response = $fireLogic->FireStatistical($date);
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_DATE_SUCCESSFULLY, $response))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }

    //thong ke hoa hoan theo thang
    public function fireStatisticalByMonthAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $month = date('Y/m');
        if(!$valid->validationMonth($month)){
            $view = View::create();
            $view->setData($formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_MONTH_ERROR_REQUEST))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
            return $view;
        }
        $response = $fireLogic->FireStatistical($month);
         $view = View::create();
            $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_MONTH_SUCCESSFULLY, $response))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
            return $view;
    }

    //thong ke hoa hoan theo nam
    public function fireStatisticalByYearAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $year = date('Y');
        if(!$valid->validationYear($year)){
            $view = View::create();
            $view->setData($formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_YEAR_ERROR_REQUEST))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
            return $view;
        }
        $response = $fireLogic->FireStatistical($year);
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_YEAR_SUCCESSFULLY, $response))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }

    //thong ke hoa hoan theo tuan
    public function FireStatisticalByWeekAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $date = date('Y/m/d', time());
        if(!$valid->validationDate($date)){
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_WEEK_ERROR_REQUEST);
        }
        $response = $fireLogic->FireStatisticalbyWeek($date);
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_WEEK_SUCCESSFULLY, $response))->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }
}