<?php
use GuzzleHttp\Client;

class FivecentsCDNApi
{
  public function __construct() 
  {
	  $this->client = new Client([
      'http_errors' => false
    ]);
	  $this->api_uri="https://api.5centscdn.com/v2/";
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


   public function updatePullZoneSsl($zoneid,$api_key,$http2,$redirect,$mode,$enabled)
  {
    	$res = $this->client->request('POST', $this->api_uri.'zones/http/pull/'.$zoneid.'/ssl', [
      'form_params' => [
        'http2' => $http2,
        'enabled'=>$enabled,
        'mode'=>$mode,
        'redirect'=>$redirect
        
      ],'headers' => [
        'Accept'        => 'application/json',
        'x-api-key'     =>  $api_key
      ]
    ]);
    return json_decode($res->getBody(), true);
  
  }

    public function updatePullZoneCname($zoneid,$api_key,$orgin,$cnames)
  {
    	$res = $this->client->request('POST', $this->api_uri.'zones/http/pull/'.$zoneid, [
        'form_params' => [

          'origin' => $orgin,
          'optimize'=>'http',
          'cnames'=>$cnames,
       
      ],'headers' => [
        'Accept'        => 'application/json',
        'x-api-key'     =>  $api_key
      ]
    ]);
    return json_decode($res->getBody(), true);
  
  }

  public function purgePullZone($id,$api_key)
  {
  	$res = $this->client->request('POST', $this->api_uri.'zones/http/pull/'.$id.'/purge', [
      'form_params' => [
        '_METHOD' => 'DELETE'
      ], 
      'headers' => [
        'Accept'        => 'application/json',
        'x-api-key'     =>  $api_key
      ]
    ]);
    return json_decode($res->getBody(), true);
  }


  public function purgePullZoneFile($id,$api_key,$files)
  {
  	$res = $this->client->request('POST', $this->api_uri.'zones/http/pull/'.$id.'/purge', [
      'form_params' => [
        '_METHOD' => 'DELETE',
        'files'=>$files
      ], 
      'headers' => [
        'Accept'        => 'application/json',
        'x-api-key'     =>  $api_key
      ]
    ]);
    return json_decode($res->getBody(), true);
  }
  
  public function httpPullZone($id, $api_key,$http)
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