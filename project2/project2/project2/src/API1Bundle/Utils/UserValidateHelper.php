<?php
/**
 * Created by PhpStorm.
 * User: 19872406
 * Date: 26/11/2016
 * Time: 11:38 SA
 */
namespace API1Bundle\Utils;

use API1Bundle\Common\Common;

class UserValidateHelper
{
    /*public function validateUpdateData($data){
        $common = new Common();
        if($data["username"] === "")
            return $common->UPDATE_INFO_USER_NULL;
        return $common->UPDATE_VALIDATE_TRUE;
    }*/

    //test id token
    public function validationIdToken($id) {
    	if($id === NULL)
    		return false;
    	return true;
    }

    //test latitude
    public function validationLatitude($latitude) {

    	if(($latitude === NULL) || (!is_double($latitude))) {
    		return false;
    	}
    	return true;
    }
    //test latitude
    public function validationLongitude($longitude) {

    	if(($longitude === NULL) || (!is_double($longitude))) {
    		return false;
    	}
    	return true;
    }

    // test agree
    public function validationAgree($agree) {

    	if(($agree === NULL) || (!is_string($agree)))
    		return false;
    	return true;
    }

    // test disagree
    public function validationDisagree($disagree) {

    	if(($disagree === NULL) || (!is_string($disagree)))
    		return false;
    	return true;
    }

    // test status 
    public function validationStatus($status) {

    	if(($status === NULL) || (!is_string($status)))
    		return false;
    	return true;
    }

    // test format time
    public  function validationTime($time) {

        if(($time === NULL) || (!is_string($time)))
            return false;
        return true;
    }

    // test image
    public  function validationImage($image) {

        if(($image === NULL) || (!is_string($image)))
            return false;
        return true;
    }

    //test description
    public  function validationDescription($description) {

        if(($description === NULL) || (!is_string($description)))
            return false;
        return true;
    }

    //test level
    public  function validationLevel($level) {

        if(($level === NULL) || (!is_string($level)))
            return false;
        return true;
    }

    //test licenseplate
    public  function validationLicenseplate($licenseplate) {

        if(($licenseplate === NULL) || (!is_string($licenseplate)))
            return false;
        return true;
    }

    //test ownername
    public  function validationOwnername($ownername) {

        if(($ownername === NULL) || (!is_string($ownername)))
            return false;
        return true;
    }

    //test username
    public  function validationUsername($username) {

        if(($username === NULL) || (!is_string($username)))
            return false;
        return true;
    }

    //test email
    public  function validationEmail($email) {

        if(($email === NULL) || (!is_string($email)) || !strpos($email, '@'))
            return false;
        return true;
    }

    //test password
    public  function validationPassword($password) {

        if(($password === NULL) || (!is_string($password)))
            return false;
        return true;
    }
    
    //test address
    public function validationAddress($address){
        if(($address === NULL) || (!is_string($address)))
            return false;
        return true;
    }

    //test fullname
    public function validationFullname($fullname){
        if(($fullname === NULL) || (!is_string($fullname)) || preg_match('/[0-9]/', $fullname))
            return false;
        return true;
    }

    //test gender
    public function validationGender($gender){
        if(($gender === NULL) || (!is_string($gender)) || preg_match('/[0-9]/', $gender))
            return false;
        return true;
    }
    //test phone
    public function validationPhone($phone){
        if(($phone === NULL) || (!is_string($phone)) || preg_match('/[A-Za-z]/', $phone))
            return false;
        return true;
    }

    //token device
    public function validationTokenDevice($tokendevice){
        if(($tokendevice === NULL) || (!is_string($tokendevice)))
            return false;
        return true;
    }
}