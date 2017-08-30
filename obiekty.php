<?php

require 'vendor/autoload.php';

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

$client = new Client();


$requests = function () {
    $uri = 'http://sportowa.warszawa.pl/adresy/baza-obiektow?page=';
    for ($i = 0; $i < 29; $i++) {
        yield new Request('GET', $uri.$i);
    }
};

$pool = new Pool($client, $requests(), [
    'concurrency' => 5,
    // 'options' => [
    // 	'timeout' => 30,
    // 	'headers' => [
    // 		'user-agent', "Mozilla/5.0 (Windows NT 5.1)AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.101 Safari/537.36"
    // 	]
    // 	],
    'fulfilled' => function ($response, $index) {
    	// var_dump("Done ".$index);
        parseResponse($response->getBody());
    },
    'rejected' => function ($reason, $index) {
        echo "Failed because ".$reason.". For job ".$index;
    },
]);

// Initiate the transfers and create a promise
$promise = $pool->promise();

// Force the pool of requests to complete.
$promise->wait();


	
function parseResponse(String $response) {
	// $body = (string) $res->getBody();
	$crawler = new Crawler($response);

	$crawler->filter('.InstitutionsList')->each(function (Crawler $node, $i) {
		$name= $node->filter('address > em')->first()->text();
		$lat = $node->filter('.latitude')->first()->text();
		$lon = $node->filter('.longitude')->first()->text();
		var_dump($name.": ".$lat." ".$lon);
	});

}

?>	

