<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 05/12/2016
 * Time: 10:08 SA
 */

/**
 * Token
 *
 * @ExclusionPolicy("all")
 */
class Token
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
    private $token;

    /**
     * @var string
     *
     * @Expose
     */
    private $time;

    function __construct($arrayData)
    {
        $this->username = $arrayData['username']['S'];
        $this->token = $arrayData['token']['S'];
        $this->time = $arrayData['time']['S'];

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

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    public function getTime()
    {
        return $this->time;
    }

}