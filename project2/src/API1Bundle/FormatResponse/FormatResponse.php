<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 18/11/2016
 * Time: 10:49 SA
 */

namespace  API1Bundle\FormatResponse;

class FormatResponse {

    public function createResponseRegister($resultCode, $resultMessage){

        $respone = [
            'resultCode' => $resultCode,
            'resultMessage' => $resultMessage
        ];
        return $respone;
    }

    public function newAddressResponse($resultCode, $resultMessage){
        $response = [
            'resultCode' => $resultCode,
            'resultMessage' => $resultMessage,
        ];
        return $response;
    }

    public function updateInfoResponse($resultCode, $resultMessage, $data){
        $respone = array(
            'resultCode' => $resultCode,
            'resultMessage' => $resultMessage,
            'Data' => $data
        );
        return $respone;
    }

    public function reportFireResponse($resultCode, $resultMessage, $data){
        $respone = array(
            'resultCode' => $resultCode,
            'resultMessage' => $resultMessage,
            'Data' => $data,
        );
        return $respone;
    }

    public function signInByUsernameResponse($resultCode, $resultMessage, $data, $token){
        $respone = array(
            'resultCode' => $resultCode,
            'resultMessage' => $resultMessage,
            'Data'=> $data,
            'token' => $token
        );
        return $respone;
    }

        public function loginFacebookResponse($resultCode, $resultMessage, $username, $token){
        $respone = array(
            'resultCode' => $resultCode,
            'resultMessage' => $resultMessage,
            'username'=> $username,
            'id' => $token
        );
        return $respone;
    }

    public function getResultEvaluateAccurateAccident($resultCode, $resultMessage, $data) {

        $respone = array(
            'resultCode' => $resultCode,
            'resultMessage' => $resultMessage,
            'Evaluate'=> $data
        );
        return $respone;
    }

    public function getNotification($resultCode, $resultMessage, $data) {
        $respone = array(
            'resultCode' => $resultCode,
            'resultMessage' => $resultMessage,
            'Data'=> $data
        );
        return $respone;
    }
}