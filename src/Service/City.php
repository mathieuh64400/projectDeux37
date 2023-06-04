<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class City{
    private $client;
 
    public function __construct(HttpClientInterface $client)
    {
        $this->client=$client;
    }
    public function call($numero)
    {
        $api="https://geo.api.gouv.fr/departements/".$numero."/communes";
    
        
        $response= $this->client->request(
            'GET',
            $api
        );

        $statusCode=$response->getStatusCode();
        if ($statusCode===200) {
            
            $content=$response->getContent();
            
            $content=$response->toArray(); 
            // dd(gettype($content));
        } else {
            $content="erreur de type ".$statusCode;
        }
       

        return $content;


    }
        
}