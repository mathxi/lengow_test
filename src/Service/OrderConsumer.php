<?php

namespace App\Service;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class OrderConsumer
 * @package App\Service
 */
class OrderConsumer
{
    /**
     * @param string $url
     */
    public function createFromUrl(string $url)
    {
        
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
        echo 'Erreur Curl : ' . curl_error($curl);
    
        $result = curl_exec($curl);
        var_dump($result);
        curl_close($curl);
    
        return $result;     
        
    }
}