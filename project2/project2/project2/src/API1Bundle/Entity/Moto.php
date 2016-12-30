<?php

namespace   API1Bundle\Entity;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;


/**
 * Moto
 *
 * @ExclusionPolicy("all")
 */
class Moto
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
    private $licenseplate;

    /**
     * @var string
     *
     * @Expose
     */
    private $ownername;


    function __construct($arrayData)
    {
        $this->username = $arrayData['username']['S'];
        $this->licenseplate = $arrayData['licenseplate']['S'];
        $this->ownername = $arrayData['ownername']['S'];

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

    public function setLicensePlate($licneseplate)
    {
        $this->licenseplate = $licneseplate;

        return $this;
    }
    public function getLicensePlate()
    {
        return $this->licenseplate;
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
