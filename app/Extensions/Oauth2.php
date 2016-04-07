<?php

namespace App\Extensions;

use App;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use OAuth2\HttpFoundationBridge\Response as BridgeResponce;
use OAuth2\HttpFoundationBridge\Request as BridgeRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Oauth2 extends Controller
{
    protected $server;
    protected $bridgedResponce;
    protected $bridgedRequest;
    protected $laravelRequest;
    protected $symfonyRequest;

    public function __construct(
        BridgeRequest $request, 
        BridgeResponce $responce, 
        Request $laravelRequest,
        SymfonyRequest $symfonyRequest )
    {
        $this->server = App::make('oauth2');
        $this->bridgedRequest = $request;
        $this->bridgedResponce = $responce;
        $this->laravelRequest = $laravelRequest;
        $this->symfontRequest = $symfonyRequest;
    }

    /**
     * Creates a access token from the request
     * needs query string pramiter "grant_type"
     *
     * @return json string containing the access token, scope and exspiary time
     */
    public function getToken()
    {
        $req = BridgeRequest::createFromRequest($this->laravelRequest);
        return $this->server->handleTokenRequest($req, $this->bridgedResponce);

    }

    public function authorizeClient()
    {
        return 'here';
    }

    /**
     * Gets the token data for the token
     * given in the "access_token" pramiter
     */
    //public function getTokenData()
    //{ 
        //$sRequest = SymfonyRequest::createFromGlobals();
        //$bridgedRequest = BridgeRequest::createFromRequest($sRequest);
        //return $this->server->getAccessTokenData($bridgedRequest);
    //}

    public static function getTokenData()
    {
        $sRequest = SymfonyRequest::createFromGlobals();
        $bridgedRequest = BridgeRequest::createFromRequest($sRequest);
        return App::make('oauth2')->getAccessTokenData($bridgedRequest);

    }

    /**
     * authenticets the request has the currecct scope
     *
     * @param string $scope The scope needed for the request
     * 
     * @return  json error messaege if the token dosenot have the correct scope
     */
    public static function verifyResourceRequest($scope = null)
    {
        $server = App::make('oauth2');
        $req = SymfonyRequest::createFromGlobals();
        $bridgedRequest  = BridgeRequest::createFromRequest($req);
       
        if (!$server->verifyResourceRequest($bridgedRequest, null, $scope)) {
            $server->getResponse()->send();
            die;
        } 
    }

    /**
     * cheeks if the token has the correct scope
     *
     * @param string $scope The scope to cheak against
     *
     * @return bool true if the token has the spesified scope
     */
    public static function tokenHasScope($scope)
    {
        $thisScope = Self::getTokenData()['scope'];
        if (strpos($thisScope, $scope) === false) {
            return false;
        } else {
            return true;
        }
    }
}
