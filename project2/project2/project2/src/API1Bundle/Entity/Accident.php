<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 24/11/2016
 * Time: 10:50 CH
 */
 namespace API1Bundle\Entity;
 use JMS\Serializer\Annotation\ExclusionPolicy;
 use JMS\Serializer\Annotation\Expose;
 use JMS\Serializer\Annotation\Groups;
 /**
  * Accident
  *
  * @ExclusionPolicy("all")
  */
 class Accident {

     /**
      * @var string
      *
      * @Expose
      */
     private $id;

     /**
      * @var number
      *
      * @Expose
      */
     private $latitude;

     /**
      * @var number
      *
      * @Expose
      */
     private $longitude;

     /**
      * @var string
      *
      * @Expose
      */
     private $username;

     /**
      * @var string
      *
      * @Expose
      */
     private $timestart;

     /**
      * @var string
      *
      * @Expose
      */
     private $description;

     /**
      * @var string
      *
      * @Expose
      */
     private $status;

     /**
      * @var string
      *
      * @Expose
      */
     private $image;

     /**
      * @var int
      *
      * @Expose
      */
     private $agree;

     /**
      * @var int
      *
      * @Expose
      */
     private $disagree;

     /**
      * @var int
      *
      * @Expose
      */
     private $level;

     function __construct($arrayData)
     {
         $this->latitude = $arrayData['latitude']['N'];
         $this->longitude = $arrayData['longitude']['N'];
         $this->id = $arrayData['id']['S'];
         $this->description = $arrayData['desciption']['S'];
         $this->licenseplate = $arrayData['licenseplate']['S'];
         $this->status = $arrayData['statusA']['S'];
         $this->image = $arrayData['image']['S'];
         $this->agree = $arrayData['agree']['N'];
         $this->disagree = $arrayData['disagree']['N'];
         $this->level = $arrayData['levelA']['S'];
         $this->timestart = $arrayData['timestart']['S'];

     }

     public function setId($id)
     {
         $this->id = $id;

         return $this;
     }

     public function getId()
     {
         return $this->id;
     }

     public function setLatitude($latitude)
     {
         $this->latitude = $latitude;

         return $this;
     }

     public function getLatitude()
     {
         return $this->latitude;
     }

     public function setLongitude($longitude)
     {
         $this->longitude = $longitude;

         return $this;
     }

     public function getLongitude()
     {
         return $this->longitude;
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

     public function setTimeStart($time)
     {
         $this->timestart = $time;

         return $this;
     }

     public function getTime()
     {
         return $this->timestart;
     }



     public function setStatus($status)
     {
         $this->status = $status;

         return $this;
     }

     public function getStatus()
     {
         return $this->status;
     }






 }