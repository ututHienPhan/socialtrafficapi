<?php
/**
 * Created by PhpStorm.
 * User: Luu Nhu
 * Date: 12/11/2016
 * Time: 4:06 PM
 */
namespace API1Bundle\Logic;

use API1Bundle\Repository\FireRepository;
use API1Bundle\Repository\ReportFireRepository;
use API1Bundle\Reference\Reference;
use Symfony\Component\Config\Definition\Exception\Exception;


class ReportFireLogic
{

    private $reportFireRepository;
    private $fireRepository;

    function __construct($dynamodb)
    {
        $this->reportFireRepository = new ReportFireRepository($dynamodb);
        $this->fireRepository = new FireRepository($dynamodb);
    }

    //get hoa hoan theo id
    public function getReportFireById($id)
    {
        $reponse = $this->reportFireRepository->getReportFireById($id);
        return $reponse;
    }

    // get hoa hoan theo toa do va trang thai
    public function getReportFireByCoordinate($status, $latitude, $longitude)
    {
        $reponse = $this->reportFireRepository->getReportFireByCoordinate($status, $latitude, $longitude);
        return $reponse->get('Items')[0];
    }

    // them thong tin hoa hoan
    public function insertReportFire($username, $latitude, $longitude, $timestart, $status,
                                         $description, $image, $address, $level)
    {
        //Lay ma id
        $id = uniqid();
        //Them report vao table report_fire
        $reponse = $this->reportFireRepository->insertReportFire($id, $username, $latitude, $longitude, $timestart, $status,
            $description, $image, $address, $level);

        //neu qua trinh thuc hien insert bi loi thi tra ve
        if ($reponse === FALSE) {
            return $reponse;
        } else {  // thưc hien them thong tin vao table fire
            $id_fire = uniqid();
            $reponse = $this->fireRepository->insertFire($id_fire, $latitude, $longitude, $timestart, $status,
                $description, $image, $address, $level);
            if ($reponse === FALSE) {
                return $reponse;
            } else { // update them thuoc tinh id_fire cho bang report_fire
                $reponse = $this->reportFireRepository->updateReportFire($id, $id_fire);
                return $reponse;
            }
        }
    }

    //comfirm fire
    public function comfirmFire($username, $latitude, $longitude, $agree, $disagree, $status, $time, $id_fire)
    {
        $id = uniqid();
        $resultInsert = $this->reportFireRepository->comfirmFire($username, $latitude, $longitude, $agree, $disagree, $status, $time, $id_fire, $id);
        if ($resultInsert === FALSE)
            return FALSE;
        $resultAcc = $this->fireRepository->getFireById($id_fire);
        $agree = $agree + $resultAcc->get('Item')['agree']['N'];
        $disagree = $disagree + $resultAcc->get('Item')['disagree']['N'];
        var_dump($agree);
        var_dump($disagree);
        $reponse = $this->fireRepository->updateFireByComfirm($id_fire, $agree, $disagree);
        var_dump(123);die;
        return $reponse;
    
    }

    //report fire handled
    public function reportFireHandled($username, $latitude, $longitude, $status, $time, $id_fire)
    {
        $id = uniqid();
        $resultInsert = $this->reportFireRepository->insertReportFireHandled($id, $username, $latitude, $longitude,
            $time, $status, $id_fire);
        if ($resultInsert === FALSE)
            return FALSE;
        $response = $this->fireRepository->updateFireHandled($id_fire, $status);
        if ($response === FALSE) {
            $this->reportFireRepository->delete($id);
            return FALSE;
        }
        return $response;
    }

    //send notify
    public function sendNotificationGCM($data, $id)
    {
        $response = $this->reportFireRepository->sendPushNotification($data, $id);
        return $response;
    }

     //ham push thong bao hoa hoan
    public function pushNotify($address, $latitude, $longitude, $arruser) {


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