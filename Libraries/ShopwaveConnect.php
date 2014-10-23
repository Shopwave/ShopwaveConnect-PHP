<?php

/**
 * Library to connect shopwave APIs.
 *
 * @author Karthik Vasudevan
 * @copyright (c) 2014, Shopwave Ltd.
 * @name $ShopwaveConnect
 * @global true
 */

namespace ShopwaveConnect;

require_once 'Token.php';

use Token\Token;

final class ShopwaveConnectManager
{
    
    private static $oAuthBaseUrl    = "http://secure.merchantstack.com/";
    private static $apiBaseUrl      = "http://api.merchantstack.com/";
    private static $authUri         = "oauth/authorize";
    private static $tokenUri        = "oauth/token";
    private static $logoutUri       = "logout";
    
    private $clientIdentifier;
    private $clientSecret;
    private $redirectUrl;
    private $scope;
    private $accessType;
    private $responseType;
    
    public $authCode;
    
    /**
     * 
     * @param type $clientIdentifier
     * @param type $clientSecret
     * @param type $redirectUrl
     * @param array $scopes
     * 
     */
    public function __construct($clientIdentifier, $clientSecret, $redirectUrl, array $scopes) 
    {
        $this->clientIdentifier = $clientIdentifier;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        $this->scope = $scopes;
        
        $this->accessType = "online";
        $this->responseType = "code";
    }
    
    /**
     * @return string authoriseApplicationUri
     * 
     * The auth uri for the user login screen
     */
    final public function getAuthoriseApplicationUri()
    {
        return self::$oAuthBaseUrl . 
               self::$authUri . 
               "?access_type=" . $this->accessType . 
               "&redirect_uri=" . $this->redirectUrl . 
               "&client_id=" . $this->clientIdentifier . 
               "&scope=" . join(",",$this->scope) .
               "&response_type=" . $this->responseType;
    }
    
    /**
     * @return string logoutUri
     * 
     * The auth uri to logout the user and present the login screen
     */
    final public function getLogoutUri()
    {
        return self::$oAuthBaseUrl . 
               self::$logoutUri . 
               "?access_type=" . $this->accessType . 
               "&redirect_uri=" . $this->redirectUrl . 
               "&client_id=" . $this->clientIdentifier . 
               "&scope=" . join(",",$this->scope) .
               "&response_type=" . $this->responseType;
    }
    
    /**
     * 
     * @throws Exception
     */
    final public function makeTokenCall()
    {
        $postData = "access_type=" . $this->accessType .
                    "&redirect_uri=" . $this->redirectUrl .
                    "&client_id=" . $this->clientIdentifier . 
                    "&scope=" . join(",",$this->scope) .
                    "&client_secret=" . $this->clientSecret .
                    "&code=" . $this->authCode .
                    "&grant_type=authorization_code";
        
        try 
        {
            $token = new Token();
            
            $response = $this->makeWebRequest($this->getTokenUri(), 'POST', null, null, null, $postData);
            $token->setAccess_token($response["body"]->access_token);
            $token->setExpires_in($response["body"]->expires_in);
            $token->setRefresh_token($response["body"]->refresh_token);
            $token->setToken_type($response["body"]->token_type);
            
            return $token;
        } 
        catch(Exception $e) 
        {
            throw new Exception("Something went wrong" . $e);
        }
    }
    
    /**
     * 
     * @throws Exception
     */
    final public function refreshTokenCall(Token $token)
    {
        $postData = "access_type=" . $this->accessType .
                    "&redirect_uri=" . $this->redirectUrl .
                    "&client_id=" . $this->clientIdentifier . 
                    "&scope=" . join(",",$this->scope) .
                    "&client_secret=" . $this->clientSecret .
                    "&refresh_token=" . $token->getRefresh_token() .
                    "&grant_type=refresh_token";
        
        try 
        {
            $response = $this->makeWebRequest($this->getTokenUri(), 'POST', null, null, null, $postData);
        
            $token->setAccess_token($response["body"]->access_token);
            $token->setExpires_in($response["body"]->expires_in);
            
            return $token;
        } 
        catch(Exception $e) 
        {
            throw new Exception("Something went wrong" . $e);
        }
        
    }
    
    final public function makeShopwaveApiCall($endpoint, Token $token, $method='GET', $headers=null, $postBody=null)
    {
        try 
        {
            $response = $this->makeWebRequest($this->getApiEndpoint($endpoint), $method, $token->getToken_type(), $token->getAccess_token(), $headers, "postBody=" . $postBody);
        
            return $response;
            
        } 
        catch(Exception $e) 
        {
            throw new Exception("Something went wrong" . $e);
        }
        
    }
    
    /**
     * @return string tokenUrl
     */
    private function getTokenUri()
    {
        return self::$oAuthBaseUrl . self::$tokenUri;
    }
    
    /**
     * @return  string api endpoing url
     */
    private function getApiEndpoint($endpoint)
    {
        return self::$apiBaseUrl . $endpoint;
    }
    
    /**
     * 
     * @param type $requestUrl
     * @param type $method
     * @param type $tokenType
     * @param type $accessToken
     * @param type $headers
     * @param type $postData
     * @return object decoded_Json_response
     * @throws Exception
     * 
     */
    private function makeWebRequest($requestUrl, $method = 'GET', $tokenType = null, $accessToken = null, $headers = null, $postData = null)
    {
        if (!function_exists('curl_init'))
        {
            throw new Exception('cURL is not installed!');
        }
        
        /* Set Common Headers */
        $reqHeaders = array("Accept-Tenant: uk", "Accept-Language: en-GB", "Accept-Charset: utf-8");
        
        /* Set Authorization Header */
        if($tokenType != null && $accessToken != null)
        {
            array_push($reqHeaders, "Authorization:".$tokenType." ".$accessToken);
        }
        
        if($headers != null)
        {
            foreach ($reqHeaders as $key => $value)
            {
                array_push($reqHeaders, $key.":".$value);
            }
        }

        /* Initialise cURL Resource Handle */
        $ch = curl_init();

        /* Set URL Options */
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $reqHeaders);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        
        if($method == "POST")
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            array_push($reqHeaders, "Content-Type:application/x-www-form-urlencoded");
        }
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($output, 0, $headerSize);
        $body = substr($output, $headerSize);
        
        /* Close The cURL resource */
        curl_close($ch);
        
        try
        {
            $decodedJson = json_decode($body);
        } 
        catch(Exception $e)
        {
            throw new Exception($e);
        }
        
        $response = array ("headers" =>  $header, "body"  => $decodedJson);
        
        return $response;
    }
}

?>
