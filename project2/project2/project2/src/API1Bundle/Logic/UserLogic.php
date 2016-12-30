<?php

namespace API1Bundle\Logic;

use API1Bundle\Repository\UserRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Constraints\True;

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
    public function insertNewUser($email, $username, $password /*, $fullname, $phonenumber, $address, $gender*/) {
        $response = $this->userRepository->newAccount($email, $username, $password /*, $fullname, $phonenumber, $address, $gender*/);
        return $response;
    }

    public function updateUserInfo($fullname, $password, $email, $phone, $address, $gender, $username)
    {
        $result = $this->userRepository->updateUserInfo($fullname, $password, $email, $phone, $address, $gender, $username);
        if($result===FALSE){
            return NULL;
        }
        else {
            $response = array("username" => $result->get('Attributes')["username"]["S"],
                "fullname" => $result->get('Attributes')["fullname"]["S"],
                "email" => $result->get('Attributes')["email"]["S"],
                "phone" => $result->get('Attributes')["phone"]["S"],
                "address" => $result->get('Attributes')["address"]["S"],
                "gender" => $result->get('Attributes')["gender"]["S"]);
            return $response;
        }
    }

    public function UserLogin($username, $password){
        $user = $this->userRepository->UserLogin($username, $password);
        return $user;
    }
}
