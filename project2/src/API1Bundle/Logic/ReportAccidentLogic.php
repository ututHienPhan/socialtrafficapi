<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 26/11/2016
 * Time: 10:44 SA
 */

namespace API1Bundle\Logic;


use API1Bundle\FirebaseCloudMessage\Push;
use API1Bundle\Reference\Reference;
use API1Bundle\Repository\AccidentRepository;
use API1Bundle\Repository\DeviceTokenRepository;
use API1Bundle\Repository\ReportAccidentRepository;
use API1Bundle\Logic\AccidentLogic;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Constraints\Count;


class ReportAccidentLogic {

    private $reportAccidentRepository;
    private $accidentRepository;
    private $deviceTokenRepository;

    function __construct($dynamodb)
    {
        $this->reportAccidentRepository = new ReportAccidentRepository($dynamodb);
        $this->accidentRepository = new AccidentRepository($dynamodb);
        $this->deviceTokenRepository = new DeviceTokenRepository($dynamodb);
    }

    //get tai nan giao thong theo id
    public function getReportAccidentById($id) {

        $reponse = $this->reportAccidentRepository->getReportAccidentById($id);
        return $reponse;

    }

    // get tai nan giao thong theo toa do va trang thai
    public function getReportAccidentByCoordinate( $status, $latitude, $longitude) {
        $reponse = $this->reportAccidentRepository->getReportAccidentByCoordinate($status, $latitude, $longitude);
        return $reponse->get('Items')[0];
    }

    // them thong tin tai nan giao thong
    public function insertReportAccident($username, $latitude, $longitude, $timestart, $status,
                                         $description, $image, $licenseplate, $level) {

        //Lay ma id
        $id = uniqid();
        //Them report vao table report_accident
        $reponse = $this->reportAccidentRepository->insertReportAccident($id, $username, $latitude, $longitude, $timestart, $status,
                                                                        $description, $image, $licenseplate, $level);

        //neu qua trinh thuc hien insert bi loi thi tra ket ve                                                                    $description, $image, $licenseplate, $level);
        if($reponse === FALSE) {
            return $reponse;
        }
        else {  // thưc hien them thong tin vao table accident
            $id_accident = uniqid();
            $reponse = $this->accidentRepository->insertAccident($id_accident, $latitude, $longitude, $timestart, $status,
                $description, $image, $licenseplate, $level, $username);
            if($reponse === FALSE) {
                return $reponse;
            }
            else { // update them thuoc tinh id_accident cho bang report_accident
                $reponse = $this->reportAccidentRepository->updateReportAccident($id, $id_accident);

                return $reponse;
            }

        }
    }

    //comfirm accident by user
    public function comfirmAccident($username, $latitude, $longitude, $agree, $disagree, $status, $time, $id_accident) {

        $id = uniqid();
        $resultInsert = $this->reportAccidentRepository->comfirmAccident($username, $latitude, $longitude, $agree, $disagree,
                                                                         $status, $time, $id_accident, $id);
        if($resultInsert === FALSE)
            return FALSE;
        $resultAcc = $this->accidentRepository->getAccidentById($id_accident);
        try {
            $agree = $agree + $resultAcc->get('Item')['agree']['N'];
            $disagree = $disagree + $resultAcc->get('Item')['disagree']['N'];
            $reponse = $this->accidentRepository->updateAccidentByComfirm($id_accident, $agree, $disagree);
            return $reponse;
        }
        catch (Exception $e){
            $this->reportAccidentRepository->delete($id);
            return FALSE;
        }

    }

    //report accident handled
    public function reportAccidentHandled($username, $latitude, $longitude, $status, $time, $id_accident) {

        $id = uniqid();
        $resultInsert = $this->reportAccidentRepository->insertReportAccidentHandled($id, $username, $latitude, $longitude,
                                                                                        $time, $status, $id_accident);
        if($resultInsert === FALSE)
            return FALSE;

        $response = $this->accidentRepository->updateAccidentHandled($id_accident, $status);
        if($response === FALSE) {
            $this->reportAccidentRepository->delete($id);
            return FALSE;
        }
        return $response;
    }

    // ham xet tai nan da dươc report trươc do chua
    public function comfirmAccidentByCoordinate($latitude, $longitude) {

        $accidents = $this->accidentRepository->getAccidentByStatus('no handle');
        if($accidents === FALSE){
            return FALSE;
        }
        else if($accidents->get('Count') <= 0) {
            return null;
        }
        $numberAcc = $accidents->get('Count');
        $result = array();
        for($i = 0; $i < $numberAcc; $i++) {
            $ref = new Reference();
            $latitudeA = $accidents->get('Items')[$i]['latitude']['N'];
            $longitudeA = $accidents->get('Items')[$i]['longitude']['N'];
            $distance =  $ref->getDistanceBetweenPointsNew($latitude, $longitude, $latitudeA, $longitudeA);
            if($distance*1000 <= 30) {
                return $accidents->get('Items')[$i]; //tai nan da duoc report truoc do
            }
        }
        return null; // tai nan chua duoc report

    }

    //ham push thong bao tai nan giao thong
    public function pushNotify($licensplate, $latitude, $longitude, $arruser) {


        $number = Count($arruser);
        $push = new Push();
        for($i = 0; $i < $number; $i++) {
            $username = $arruser[$i]['username']['S'];
            $result = $this->deviceTokenRepository->findByUsername($username);
            if($result === FALSE)
                return $result;
            $numberdevice = Count($result);
            for($j=0; $j<$numberdevice; $j++) {
                $push->sendPushNotification($result[$j]['token']['S'], $latitude, $longitude,
                                            $arruser[$i]['ownername']['S'], $licensplate, 'no handle');
            }

        }
        return true;
    }


}