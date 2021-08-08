<?php

class SimpleJsonRequest
{
    public const BEARER_TOKEN = 'BQCrvX3yUimh3CQDxIx5K_DzggB6HeD3bzWfb08cPVZrh9A0DtkHr5m2G8mjlcWkBQXHEvm5WBFDBUj3EziftSnO7MezZC5g3Asofm1SC_M5tsmvthBMjdHaRGE21_-J6_G94FQzzUwauPlttdCw-ayPNAXCulZ-uALeaZgjqGOWip1cildfgRSVMnUSL8-F0U0rF9VHEiMY8BAQjeLyycfSZrbMC5_cIRK0Q1BozGDNQr5s5CesxrBdxPxT4MUG0cXgiPBi17RLu4p6mrKKk2NrAw';

    private static function makeRequest(string $method, string $url, array $parameters = null, array $data = null)
    {
        $opts = [
            'http' => [
                'method'  => $method,
                'header'  => [
                    'Content-type: application/json',
                    'Authorization: Bearer '.self::BEARER_TOKEN
            ],
                'content' => $data ? json_encode($data) : null
            ]
        ];

        $url .= ($parameters ? '?' . http_build_query($parameters) : '');
        return file_get_contents($url, false, stream_context_create($opts));
    }

    public static function get(string $url, array $parameters = null)
    {
        return json_decode(self::makeRequest('GET', $url, $parameters));
    }

    public static function post(string $url, array $parameters = null, array $data)
    {
        return json_decode(self::makeRequest('POST', $url, $parameters, $data));
    }

    public static function put(string $url, array $parameters = null, array $data)
    {
        return json_decode(self::makeRequest('PUT', $url, $parameters, $data));
    }   

    public static function patch(string $url, array $parameters = null, array $data)
    {
        return json_decode(self::makeRequest('PATCH', $url, $parameters, $data));
    }

    public static function delete(string $url, array $parameters = null, array $data = null)
    {
        return json_decode(self::makeRequest('DELETE', $url, $parameters, $data));
    }

    /**
     * Checking the cached data availablity
     * @param string $cachedName
     * @param object $redis
     */
    public static function readCache(string $cachedName, object $redis){

        $cachedEntry  = $redis->get($cachedName);

        return $cachedEntry;
    }

    /**
     * Getting the API result
     * @param string $cachedName
     * @param object $redis
     * @param string $url
     */
    public static function getData(string $cachedName, object $redis, string $url){

        $cachedEntry  = self::readCache($cachedName, $redis);

        if(!empty($cachedEntry)){
            echo "<strong>Displaying from cache </strong> <hr />";
            $dataAr = json_decode($cachedEntry, TRUE);

        }else{
            echo "<strong>Displaying from API </strong> <hr />";
            $dataAr = (array) self::createCache($cachedName, $url, $redis);
        }

        return $dataAr;
    }

    /**
     * Create radis cache
     * @param string $cachedName
     * @param string $url
     * @param object $redis
     */
    public static function createCache(string $cachedName, string $url, object $redis){

        switch ($cachedName) {
            case 'artist':
                $result = self::get($url, null);
                
                //Setting the redis cache
                $redis->set($cachedName, json_encode($result));

                break;
            
            default:
                # code...
                break;
        }

        return $result;
    }

    /**
     * Expiring radis cache
     * @param string $cachedName
     * @param object $redis
     * @param int $time
     */
    public static function expiringCache(string $cachedName, object $redis, int $time){

        $cachedEntry  = self::readCache($cachedName, $redis);

        if($cachedEntry){
            $redis->expire($cachedName, $time);
            return true;
        }else{
            return false;
        }
        
    }
}

