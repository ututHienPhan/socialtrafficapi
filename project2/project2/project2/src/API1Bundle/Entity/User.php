<?php

namespace   API1Bundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;


/**
 * User
 *
 * @ExclusionPolicy("all")
 */
class User
{
     /**
     * @var string
     *
	 * @Expose
     */
    private $username;
   
    /**
     * @var string
     *
     */
    private $password;
 
    /**
     * @var string
     *
	 * @Expose
     */
    private $fullname;

    /**
     * @var string
     *
     * @Expose
     */
    private $phone;

    /**
     * @var string
     *
     * @Expose
     */
    private $email;

    /**
     * @var string
     *
     * @Expose
     */
    private $address;

    /**
     * @var string
     *
     * @Expose
     */
    private $gender;

	function __construct($arrayData) 
    { 
        $this->username = $arrayData['username']['S'];
        $this->fullname = $arrayData['fullname']['S'];
        $this->phone = $arrayData['phone']['S'];
        $this->email = $arrayData['email']['S'];
        $this->address = $arrayData['address']['S'];
        $this->gender = $arrayData['gender']['S'];
    } 
	

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setAdress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    public function getGender()
    {
        return $this->gender;
    }

}
