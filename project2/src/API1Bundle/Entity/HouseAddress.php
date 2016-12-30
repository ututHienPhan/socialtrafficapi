<?php
/**
 * Created by PhpStorm.
 * User: 19872406
 * Date: 24/11/2016
 * Time: 8:17 CH
 */
namespace   API1Bundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;


/**
 * HouseAddress
 *
 * @ExclusionPolicy("all")
 */
class HouseAddress
{
    /**
     * @var string
     *
     * @Expose
     */
    private $id;

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
    private $latitude;

    /**
     * @var string
     *
     * @Expose
     */
    private $longtitude;

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
    private $ownername;


    function __construct($arrayData)
    {
        $this->username = $arrayData['username']['S'];
        $this->latitude = $arrayData['latitude']['S'];
        $this->longtitude = $arrayData['longtitude'];
        $this->address = $arrayData['address']['S'];
        $this->ownername = $arrayData['ownername']['S'];
        $this->id = $arrayData['id']['S'];
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

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
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

    public function setLongtitude($longtitude)
    {
        $this->longtitude = $longtitude;
        return $this;
    }
    public function getLongtitude()
    {
        return $this->longtitude;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setOwnerName($ownername)
    {
        $this->ownername = $ownername;

        return $this;
    }
    public function getOwnerName()
    {
        return $this->ownername;
    }
}
