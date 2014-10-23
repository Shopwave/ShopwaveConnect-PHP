<?php

/**
 * Description of Token
 *
 * @author Karthik
 * @copyright (c) 2014, Shopwave Ltd.
 */
 
namespace Token;

class Token {
    
    private $refresh_token;
    private $token_type;
    private $expires_in;
    private $access_token;
    
    public function getRefresh_token() 
    {
        return $this->refresh_token;
    }

    public function setRefresh_token($refresh_token) 
    {
        $this->refresh_token = $refresh_token;
    }

    public function getToken_type() 
    {
        return $this->token_type;
    }

    public function setToken_type($token_type) 
    {
        $this->token_type = $token_type;
    }

    public function getExpires_in() 
    {
        return $this->expires_in;
    }

    public function setExpires_in($expires_in) 
    {
        $this->expires_in = $expires_in;
    }

    public function getAccess_token() 
    {
        return $this->access_token;
    }

    public function setAccess_token($access_token) 
    {
        $this->access_token = $access_token;
    }
}

?>
