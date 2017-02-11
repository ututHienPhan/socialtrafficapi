<?php

namespace API1Bundle\Logic;

use API1Bundle\Repository\UserRepository;

class UserLogic
{
	private $userRepository;
	
	function __construct($dynamodb) 
    { 
        $this->userRepository = new UserRepository($dynamodb);
    } 

	public function getUserInfo ($username){
        $user = $this->userRepository->findByUsername($username);
		return $user;
    }

    public  function  getUserInfoByEmail($email) {
        $user = $this->userRepository->findByEmail($email);
        return $user;
    }
    public function insertNewUser($email, $username, $password) {
        $response = $this->userRepository->newAccount($email, $username, $password);
        return $response;
    }

    public function insertNewUserFacebook($username, $fullname, $avatar) {
        $response = $this->userRepository->newAccountFacebook($username, $fullname, $avatar);
        return $response;
    }

    public function updateUserInfo($fullname, $password, $email, $phone, $address, $gender, $username, $avatar)
    {
        $result = $this->userRepository->updateUserInfo($fullname, $password, $email, $phone, $address, $gender, $username, $avatar);
        if($result===FALSE){
            return NULL;
        }
        else {
            $response = array("username" => $result->get('Attributes')["username"]["S"],
                "fullname" => $result->get('Attributes')["fullname"]["S"],
                "email" => $result->get('Attributes')["email"]["S"],
                "phone" => $result->get('Attributes')["phone"]["S"],
                "address" => $result->get('Attributes')["address"]["S"],
                "gender" => $result->get('Attributes')["gender"]["S"],
                "avatar" => $result->get('Attributes')["avatar"]["S"]);
            return $response;
        }
    }

    public function UserLogin($username, $password){
        $user = $this->userRepository->UserLogin($username, $password);
        return $user;
    }
}
