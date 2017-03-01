<?php
/**
 * Created by PhpStorm.
 * User: Luu Nhu
 * Date: 03/01/2017
 * Time: 4:47 PM
 */
namespace API1Bundle\Controller;

use API1Bundle\Common\Common;
use API1Bundle\Logic\FireLogic;
use API1Bundle\Entity\Fire;
use API1Bundle\Entity\Accident;
use API1Bundle\Logic\AccidentLogic;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use API1Bundle\FormatResponse\FormatResponse;
use API1Bundle\Utils\UserValidateHelper;

class WebRestController extends Controller
{
    //thong ke hoa hoan, tai nan theo ngay
    public function StatisticalByDateAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $date = date('Y/m/d', time());
        if(!$valid->validationDate($date)){
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_DATE_ERROR_REQUEST);
        }
        $fireResponse = $fireLogic->FireStatistical($date);
        $accidentResponse = $accidentLogic->AccidentStatistical($date);
        $result_array = array();
        $result_array['Time'] = $date;
        $result_array['Fire'] = $fireResponse;
        $result_array['Accident'] = $accidentResponse;
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_DATE_SUCCESSFULLY, $result_array))
            ->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }

    //thong ke hoa hoan, tai nan theo thang
    public function StatisticalByMonthAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $date = date('Y/m', time());
        if(!$valid->validationMonth($date)){
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_MONTH_ERROR_REQUEST);
        }
        $fireResponse = $fireLogic->FireStatistical($date);
        $accidentResponse = $accidentLogic->AccidentStatistical($date);
        $result_array = array();
        $result_array['Time'] = $date;
        $result_array['Fire'] = $fireResponse;
        $result_array['Accident'] = $accidentResponse;
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_MONTH_SUCCESSFULLY, $result_array))
            ->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }

    //thong ke hoa hoan, tai nan theo nam
    public function StatisticalByYearAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $date = date('Y', time());
        if(!$valid->validationYear($date)){
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_YEAR_ERROR_REQUEST);
        }
        $fireResponse = $fireLogic->FireStatistical($date);
        $accidentResponse = $accidentLogic->AccidentStatistical($date);
        $result_array = array();
        $result_array['Time'] = $date;
        $result_array['Fire'] = $fireResponse;
        $result_array['Accident'] = $accidentResponse;
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_YEAR_SUCCESSFULLY, $result_array))
            ->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }

    //thong ke hoa hoan, tai nan theo tuan
    public function StatisticalByWeekAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $date = date('Y/m/d', time());

        if(!$valid->validationDate($date)){
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_WEEK_ERROR_REQUEST);
        }
        $fireResponse = $fireLogic->FireStatisticalbyWeek($date);
        $accidentResponse = $accidentLogic->AccidentStatisticalbyWeek($date);
        $result_array = array();
        for($i = 0; $i < count($fireResponse); $i++){
            $result_array[$i]['Time'] = $fireResponse[$i]['date'];
            $result_array[$i]['Fire'] = $fireResponse[$i]['count'];
            $result_array[$i]['Accident'] = $accidentResponse[$i]['count'];
        }
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_WEEK_SUCCESSFULLY, $result_array))
            ->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }

    //thong ke hoa hoan, tai nan 4 tuan
    public function StatisticalBy4WeekAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $week = date('Y/m/d', time());
        if(!$valid->validationDate($week)){
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_WEEK_ERROR_REQUEST);
        }
        $fireResponse = $fireLogic->FireStatistical4Week($week);
        $accidentResponse = $accidentLogic->AccidentStatistical4Week($week);
        $result_array = array();
        for($i = 0; $i < count($fireResponse); $i++){
            $result_array[$i]['Time'] = $fireResponse[$i]['week'];
            $result_array[$i]['Fire'] = $fireResponse[$i]['count'];
            $result_array[$i]['Accident'] = $accidentResponse[$i]['count'];
        }
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_WEEK_SUCCESSFULLY, $result_array))
            ->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }

    //thong ke hoa hoan 6 thang
    public function StatisticalBy6MonthAction(){
        $fireLogic = new FireLogic($this->get('aws.dynamodb'));
        $accidentLogic = new AccidentLogic($this->get('aws.dynamodb'));
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $date = date('Y/m/d', time());
        if(!$valid->validationDate($date)){
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->STATISTICAL_BY_MONTH_ERROR_REQUEST);
        }
        $fireResponse = $fireLogic->FireStatistical6Month($date);
        $accidentResponse = $accidentLogic->AccidentStatistical6Month($date);
        $result_array = array();
        for($i = 0; $i < count($fireResponse); $i++){
            $result_array[$i]['Time'] = $fireResponse[$i]['month'];
            $result_array[$i]['Fire'] = $fireResponse[$i]['count'];
            $result_array[$i]['Accident'] = $accidentResponse[$i]['count'];
        }
        $view = View::create();
        $view->setData($formatResponse->getResultStatistical($common->RESULT_CODE_SUCCESS, $common->STATISTICAL_BY_MONTH_SUCCESSFULLY, $result_array))
            ->setStatusCode(200)->setHeader('Access-Control-Allow-Origin','*');
        return $view;
    }
}