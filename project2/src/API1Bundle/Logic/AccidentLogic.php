<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 08/12/2016
 * Time: 11:26 CH
 */

namespace API1Bundle\Logic;

use API1Bundle\Common\Common;
use API1Bundle\Entity\Accident;
use API1Bundle\Repository\AccidentRepository;
use API1Bundle\Reference\Reference;


class AccidentLogic
{

    private $accidentRepository;

    function __construct($dynamodb)
    {
        $this->accidentRepository = new AccidentRepository($dynamodb);
    }


    // get thong tin tai nan gia thong
    public function getAccident($latitude, $longitude, $status) {
        $reponse = $this->accidentRepository->getAccident($latitude, $longitude, $status);
        return $reponse;
    }

    //get cac dia diem xay ra tai nan giao thong
    public function getAccidentsLocal($latitude, $longitude, $distance) {

        $status = 'no handle';
        $accidents = $this->accidentRepository->getAccidentByStatus($status);
        if($accidents === FALSE){
            return FALSE;
        }
        else if($accidents->get('Count') <= 0) {
            return null;
        }
        $numberAcc = $accidents->get('Count');
        $result = array();
        for($i = 0; $i < $numberAcc; $i++) {
            $latitudeA = $accidents->get('Items')[$i]['latitude']['N'];
            $longitudeA = $accidents->get('Items')[$i]['longitude']['N'];
            $ref = new Reference();
            $distanceReal =  $ref->getDistanceBetweenPointsNew($latitude, $longitude, $latitudeA, $longitudeA);
            if($distanceReal <= $distance)
            {
                $acci = new Accident($accidents->get('Items')[$i]);
                array_push($result,$acci); //moi them
            }
        }
        if(count($result) == 0)
            return null;
        return $result;
    }

    // get result of evaluation of accurate accident
    public function getEvaluateAccurateAccident($latitude, $longitude) {

        $common = new Common();
        $accident = $this->accidentRepository->getAccident($latitude, $longitude, 'no handle');
        if($accident === FALSE) {
            return FALSE;
        }
        if($accident->get('Count') <= 0)
            return null;
        $agree = $accident->get('Items')[0]['agree']['N'];
        $disagree = $accident->get('Items')[0]['disagree']['N'];
        if($agree == 0 && $disagree == 0) { //chi moi co 1 nguoi report
            $evaluate = (1 / $common->NUMBER) * 100;
            $evaluate = (int)$evaluate;
            return $evaluate;
        }
        $evaluate = ($agree+1)/($common->NUMBER+$disagree)*100;
        $evaluate = (int)$evaluate;
        if($evaluate >= 100)
            return 99;
        return $evaluate;
    }


    //lay cac licenseplate accidnet
    public  function getLicenseplateAccident($licenseplate) {

        $accident = $this->accidentRepository->getLicenseAccident('no handle', $licenseplate);

        if($accident === FALSE) {
            return FALSE;
        }
        if($accident->get('Count') <= 0)
            return null;
        return $accident->get('Items')[0];

    }

    //thong ke tai nan
    public function AccidentStatistical($date){
        $response = $this->accidentRepository->AccidentStatistical($date);
        return $response;
    }

    //thong ke tai nan 7 ngay
    public function AccidentStatisticalbyWeek($date){
        $result = $this->accidentRepository->AccidentStatisticalinWeek($date);
        return $result;
    }

    //thong ke tai nan 4 tuan
    public function AccidentStatistical4Week($date){
        $result = $this->accidentRepository->AccidentStatisticalin4Week($date);
        return $result;
    }

    //thong ke tai nan trong 6 thang
    public function AccidentStatistical6Month($date){
        $result = $this->accidentRepository->AccidentStatisticalinYear($date);
        return $result;
    }
}