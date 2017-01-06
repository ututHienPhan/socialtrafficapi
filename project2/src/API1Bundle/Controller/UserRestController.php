<?php

namespace API1Bundle\Controller;

use API1Bundle\Common\Common;
use API1Bundle\Logic\TokenLogic;
use Aws\DynamoDb\DynamoDbClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use API1Bundle\Logic\UserLogic;
use API1Bundle\FormatResponse\FormatResponse;
use API1Bundle\Utils\UserValidateHelper;
use API1Bundle\Logic\DeviceTokenLogic;
use Symfony\Component\Config\Definition\Exception\Exception;

class UserRestController extends Controller
{
    public function getUserAction($username){
        try{
            $userLogic = new UserLogic($this->get('aws.dynamodb'));
        }catch (Exception $e) {
            echo $e->getMessage();
        }
echo 'die';die;
		$formatResponse = new FormatResponse();
        $common = new Common();
        $user = $userLogic->getUserInfo($username);

        if($user === FAlSE) {
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->GET_INFO_USER_FAIL, null);
        }
        if ($user->get('Item')) {
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_SUCCESS, $common->GET_INFO_USER_SUCCESSULLY, $user->get('Item'));

        }
        return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->GET_INFO_USER_ERROR_NOT_FOUND, null);


    }
    // api 1: create account
        public function postUsersAction() {
            $common = new Common();
            $registerResponse = new FormatResponse();
            $userLogic = new UserLogic($this->get('aws.dynamodb'));
            $valid = new UserValidateHelper();
            $datas = $this->get('request')->getContent();
            $array = json_decode($datas, true);
            $email = $array["email"];
            $password = $array["password"];
            $username = $array["username"];
            if(!$valid->validationEmail($email) || !$valid->validationPassword($password) || !$valid->validationUsername($username))
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTERED_ACCOUNT_ERROR_REQUEST);
            $user = $userLogic->getUserInfoByEmail($email);
            if($user === FAlSE) {
                return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTERED_ACCOUNT_FAIL);
            }
            if ($user->get('Count') == 0) {
                $user2 = $userLogic->getUserInfo($username);
               if($user2 === FALSE)
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTERED_ACCOUNT_FAIL);
                if($user2->get('Item'))
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTERED_ACCOUNT_USERNAME_EXISTED);
                $result = $userLogic->insertNewUser($email, $username, $password);
                if($result === FAlSE) {
                    return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTERED_ACCOUNT_FAIL);
                }
               return  $registerResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->REGISTERED_ACCOUNT_SUCCESSFULLY);
            }

             return $registerResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->REGISTERED_ACCOUNT_EMAIL_EXISTED);
    }

    //cap nhat thong tin tai khoan
    public function updateAction()
    {
        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $userLogic = new UserLogic($this->get('aws.dynamodb'));
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);
        $token = $decoded["token"];
        $password = $decoded["password"];
        $fullname = $decoded["fullname"];
        $email = $decoded["email"];
        $phone = $decoded["phone"];
        $address = $decoded["address"];
        $gender = $decoded["gender"];
        if(!$valid->validationIdToken($token))
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->UPDATE_INFO_TOKEN_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if($username === FALSE)
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->UPDATE_INFO_FAIL);
        else if($username === NULL)
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->UPDATE_INFO_TOKEN_ERROR_NOT_FOUND);
        else {
            if (!$valid->validationEmail($email) || !$valid->validationAddress($address) || !$valid->validationPassword($password) ||
                !$valid->validationFullname($fullname) || !$valid->validationGender($gender) || !$valid->validationPhone($phone)
            ) {
                return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->UPDATE_INFO_ERROR_INPUT);
            }
            $user = $userLogic->updateUserInfo($fullname, $password, $email, $phone, $address, $gender, $username);
            if($user === NULL)
                return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->UPDATE_INFO_FAIL);
        }
        return $formatResponse->updateInfoResponse($common->RESULT_CODE_SUCCESS, $common->UPDATE_INFO_SUCCESSULLY, $user);
    }

    //Dang nhap bang token
    public function signInByTokenAction() {

        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $token = $array["token"];
        if(!$valid->validationIdToken($token))
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->SIGN_IN_TOKEN_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if($username === FALSE)
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->SIGN_IN_TOKEN_FAIL);
        else if($username === NULL)
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->SIGN_IN_TOKEN_ERROR_NOT_FOUND);
        else {
            return $formatResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->SIGN_IN_TOKEN_SUCCESSULLY);
        }
    }

    public function userLoginAction(){
        $common = new Common();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $deviceTokenLogic = new DeviceTokenLogic($this->get('aws.dynamodb'));
        $formatResponse = new FormatResponse();
        $userLogic = new UserLogic($this->get('aws.dynamodb'));
        $data = $this->get('request')->getContent();
        $array = json_decode($data, true);
        $username = $array["username"];
        $password = $array["password"];
        $tokendevice = $array["tokendevice"];
        if(!$valid->validationUsername($username) || !$valid->validationPassword($password)
            || !$valid->validationTokenDevice($tokendevice))
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->LOGIN_ERROR_INPUT);
        $user = $userLogic->UserLogin($username, $password);
        if($user === FALSE){
            $response = $formatResponse->signInByUsernameResponse($common->RESULT_CODE_FAIL, $common->LOGIN_FAIL, null, null );
        }
        else {
            $token = $username.uniqid();
            $resultToken = $tokenLogic->insertNewToken($username, $token, date('Y-m-d H:i:s'));
            $resultDeviceToken = $deviceTokenLogic->insertDeviceToken($username, $tokendevice);
            if($resultToken === FALSE || $resultDeviceToken === FALSE) {
                return $formatResponse->signInByUsernameResponse($common->RESULT_CODE_FAIL, $common->LOGIN_FAIL, null, null);
            }
            $response = $formatResponse->signInByUsernameResponse($common->RESULT_CODE_SUCCESS, $common->LOGIN_SUCCESSFULLY, $user, $token);

        }
        return $response;
    }

    public function userLogoutAction() {

        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();
        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $deviceTokenLogic = new DeviceTokenLogic($this->get('aws.dynamodb'));
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $token = $array["token"];
        $tokendevice = $array["tokendevice"];
        if(!$valid->validationIdToken($token))
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->USER_LOGOUT_ERROR_REQUEST);
        $username = $tokenLogic->getUsername($token);
        if($username === FALSE)
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->USER_LOGOUT_FAIL);
        else if($username === NULL)
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->USER_LOGOUT_ERROR_NOT_FOUND);
        else {
            $responseToken = $tokenLogic->deleteToken($username);
            $responseDevice = $deviceTokenLogic->deleteDeviceToken($tokendevice);
            if($responseToken === FALSE || $responseDevice === FALSE)
                return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->USER_LOGOUT_FAIL);
            return $formatResponse->createResponseRegister($common->RESULT_CODE_SUCCESS, $common->USER_LOGOUT_SUCCESSULLY);
        }

    }
}
