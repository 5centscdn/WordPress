<?php
use GuzzleHttp\Client;

class FivecentsCDNApi
{
  public function __construct() 
  {
	  $this->client = new Client();
	  $this->api_uri="https://api.5centscdn.com/v1/";
  }

  public function listPullZones( $api_key )
  {
    $res = $this->client->request('GET', $this->api_uri.'zones/http/pull', [
      'headers' => [
        'Accept'        => 'application/json',
        'x-api-key'     =>  $api_key
      ]
    ]);
    return json_decode($res->getBody(), true);
  }

  public function getPullZones( $id , $api_key )
  {
    $res = $this->client->request('GET', $this->api_uri.'zones/http/pull/'.$id, [
      'headers' => [
        'Accept'        => 'application/json',
        'x-api-key'     =>  $api_key
      ]
    ]);
    return json_decode($res->getBody(), true);
  }

  public function purgePullZone($id , $api_key)
  {
  	$res = $this->client->request('POST', $this->api_uri.'zones/http/pull/'.$id.'/purge', [
      'headers' => [
        'Accept'        => 'application/json',
        'x-api-key'     =>  $api_key
      ]
    ]);
    return json_decode($res->getBody(), true);
  }
  
  public function httpPullZone($id , $api_key , $http)
  {
  	$res = $this->client->request('POST', $this->api_uri.'zones/http/pull/'.$id.'/ssl', [
      'form_params' => [
        'http2' => $http,
      ],'headers' => [
        'Accept'        => 'application/json',
        'x-api-key'     =>  $api_key
      ]
    ]);
    return json_decode($res->getBody(), true);
  }
}

