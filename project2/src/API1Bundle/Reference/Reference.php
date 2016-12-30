<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 23/12/2016
 * Time: 12:08 SA

 */

namespace API1Bundle\Reference;

class Reference {

    //ham tinh khoang cach 2 toa do
    public function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {

        $theta = $longitude1 - $longitude2;

        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1))
                * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));

        $miles = acos($miles);

        $miles = rad2deg($miles);

        $miles = $miles * 60 * 1.1515;

        $kilometers = $miles * 1.609344;

        return $kilometers;
    }
}