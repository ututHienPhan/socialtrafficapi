<?php
/**
 * Created by PhpStorm.
 * User: Luu Nhu
 * Date: 12/11/2016
 * Time: 4:24 PM
 */
namespace API1Bundle\Logic;

use API1Bundle\Common\Common;
use API1Bundle\Entity\Fire;
use API1Bundle\Repository\FireRepository;
use Symfony\Component\Validator\Constraints\Count;


class FireLogic
{

    private $fireRepository;

    function __construct($dynamodb)
    {
        $this->fireRepository = new FireRepository($dynamodb);
    }

    // get thong tin hoa hoan
    public function getFire($latitude, $longitude, $status)
    {
        $reponse = $this->fireRepository->getFire($latitude, $longitude, $status);
        return $reponse;
    }

    //get cac dia diem xay ra hoa hoan
    public function getFireLocal($latitude, $longitude)
    {
        $status = 'no handle';
        $fire = $this->fireRepository->getFireByStatus($status);
        if ($fire === FALSE) {
            return FALSE;
        } else if ($fire->get('Count') <= 0) {
            return null;
        }
        $numberAcc = $fire->get('Count');
        $result = array();
        for ($i = 0; $i < $numberAcc; $i++) {
            $latitudeA = $fire->get('Items')[$i]['latitude']['N'];
            $longitudeA = $fire->get('Items')[$i]['longitude']['N'];
            $distance = $this->getDistanceBetweenPointsNew($latitude, $longitude, $latitudeA, $longitudeA);
            if ($distance <= 100)
                $arr = new Fire($fire->get('Items')[$i]);
                array_push($result, $arr);
        }
        if (count($result) == 0)
            return null;
        return $result;
    }

    // get result of evaluation of accurate fire - do tin cay cua thong bao
    public function getEvaluateAccurateFire($latitude, $longitude)
    {
        $common = new Common();
        $fire = $this->fireRepository->getFire($latitude, $longitude, 'no handle');
        if ($fire === FALSE) {
            return FALSE;
        }
        if ($fire->get('Count') <= 0)
            return null;
        $agree = $fire->get('Items')[0]['agree']['N'];
        $disagree = $fire->get('Items')[0]['disagree']['N'];
        if ($agree == 0 && $disagree == 0) { //chi moi co 1 nguoi report
            $evaluate = (1 / $common->NUMBER) * 100;
            $evaluate = (int)$evaluate;
            return $evaluate;
        }
        $evaluate = ($agree + 1) / ($common->NUMBER + $disagree) * 100;
        $evaluate = (int)$evaluate;
        if ($evaluate > 100)
            return 100;
        return $evaluate;
    }

    //ham tinh khoang cach 2 toa do
    private function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1))
                * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        return $kilometers;
    }

        // get hoa hoan theo toa do va trang thai
    public function getFireByCoordinate($status, $latitude, $longitude)
    {
        $reponse = $this->fireRepository->getFire($latitude, $longitude, $status);
        return $reponse->get('Items');
    }

}