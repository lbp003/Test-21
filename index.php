<?php
include_once './request.php';
require './vendor/autoload.php';

$redis = new Predis\Client();

// echo $redis->ping();

$objRequest = new SimpleJsonRequest();

//Check cache isAvailable
$cachedName = 'artist';
$expireTime = 5; // In seconds

$url = 'https://api.spotify.com/v1/artists/6eUKZXaKkcviH0Ku9w2n3V'; // Ed Sheeran's artist Id

$dataAr = $objRequest::getData($cachedName, $redis, $url);

// Expiring the created cache
$objRequest::expiringCache($cachedName, $redis, $expireTime);

print_r($dataAr);

?>