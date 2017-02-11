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
    // lay thong tin cua nguoi dung
    public function getUserAction($username){
        $userLogic = new UserLogic($this->get('aws.dynamodb'));
		$formatResponse = new FormatResponse();
        $common = new Common();
        $response = $userLogic->getUserInfo($username);

        if($response === FAlSE) {
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_FAIL, $common->GET_INFO_USER_FAIL, null);
        }
        if ($response->get('Item')) {
            $infoUser = $response->get('Item');
            //phan can chinh sua
            $username = $infoUser['username']['S'];
            $email = "";
            $password = "";
            $address = "";
            $fullname = "";
            $gender = "";
            $phone = "";
            $avatar = "";
            if(isset($infoUser['email'])){
                $email = $infoUser['email']['S'];
            }
            if(isset($infoUser['password'])){
                $password = $infoUser['password']['S'];
            }
            if(isset($infoUser['address'])){
                $address = $infoUser['address']['S'];
            }
            if(isset($infoUser['fullname'])){
                $fullname = $infoUser['fullname']['S'];
            }
            
            if(isset($infoUser['gender'])) {
                $gender = $infoUser['gender']['S'];
            }
            if(isset($infoUser['phone'])) {
                $phone = $infoUser['phone']['S'];
            }
            if(isset($infoUser['avatar'])) {
                $avatar = $infoUser['avatar']['S'];
            }
            $user = [
                        'username' => $username,
                        'password' => $password,
                        'email' => $email,
                        'fullname' => $fullname,
                        'address' => $address,
                        'gender' => $gender,
                        'phone' => $phone,
                        'avatar' => $avatar
                     ];
            // phan can chinh sua
            return $formatResponse->updateInfoResponse($common->RESULT_CODE_SUCCESS, $common->GET_INFO_USER_SUCCESSULLY, $user);

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
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $token = $array["token"];
        $password = $array["password"];
        $fullname = $array["fullname"];
        $email = $array["email"];
        $phone = $array["phone"];
        $address = $array["address"];
        $gender = $array["gender"];
        $avatar = $array["avatar"];
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
                || !$valid->validationAvatar($avatar)) {
                return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->UPDATE_INFO_ERROR_INPUT);
            }
            $user = $userLogic->updateUserInfo($fullname, $password, $email, $phone, $address, $gender, $username, $avatar);
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

    //api đăng xuất
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

    //api đăng nhập bằng facebook
    public function userLoginFacebookAction() {

        $common = new Common();
        $formatResponse = new FormatResponse();
        $valid = new UserValidateHelper();

        $tokenLogic = new TokenLogic($this->get('aws.dynamodb'));
        $deviceTokenLogic = new DeviceTokenLogic($this->get('aws.dynamodb'));
        $userLogic = new UserLogic($this->get('aws.dynamodb'));
        $datas = $this->get('request')->getContent();
        $array = json_decode($datas, true);
        $token = $array["id"];
        $username = $array["username"];
        $tokendevice = $array["tokendevice"];
        $fullname = $array["fullname"];
        $avatar = $array["avatar"];
        if(!$valid->validationIdToken($token) || !$valid->validationTokenDevice($tokendevice) || !$valid->validationUsername($username) || !$valid->validationFullname($fullname) || !$valid->validationAvatar($avatar))
            return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->LOGIN_FACEBOOK_ERROR_INPUT);
        $user = $userLogic->getUserInfo($username);

        if(!($user->get('Item')))
        {
            $result = $userLogic->insertNewUserFacebook($username, $fullname, $avatar);
            if($result === FALSE) {
                return $formatResponse->createResponseRegister($common->RESULT_CODE_FAIL, $common->LOGIN_FACEBOOK_FAIL);
            }
        }
        $resultToken = $tokenLogic->insertNewToken($username, $token, date('Y-m-d H:i:s'));
        $resultDeviceToken = $deviceTokenLogic->insertDeviceToken($username, $tokendevice);
        if($resultToken === FALSE || $resultDeviceToken === FALSE) {
            return $formatResponse->loginFacebookResponse($common->RESULT_CODE_FAIL, $common->LOGIN_FACEBOOK_FAIL, null, null);
        }
        return $formatResponse->loginFacebookResponse($common->RESULT_CODE_SUCCESS, $common->LOGIN_FACEBOOK_SUCCESSFULLY, $username, $token);
    }
}
