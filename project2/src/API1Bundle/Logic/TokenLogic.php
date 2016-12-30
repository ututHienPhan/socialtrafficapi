<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 05/12/2016
 * Time: 10:33 SA
 */

namespace API1Bundle\Logic;

use API1Bundle\Repository\TokenRepository;


class TokenLogic
{
    private $tokenRepository;

    function __construct($dynamodb)
    {
        $this->tokenRepository = new TokenRepository($dynamodb);
    }

    public function getUsername($token) {

        $result = $this->tokenRepository->getUsernameByToken($token);
        if($result === FALSE) {
            return FALSE;
        }
        if ($result ->get('Items'))
            return $result->get('Items')[0]["username"]["S"];
        return NULL;
    }
    public function insertNewToken($username, $token, $time)
    {
        $response = $this->tokenRepository->newToken($username, $token, $time);
        return $response;
    }

    public  function deleteToken($username) {

        $response = $this->tokenRepository->deleteToken($username);
        return $response;
    }


}