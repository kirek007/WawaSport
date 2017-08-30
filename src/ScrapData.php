<?php
namespace Kir\WawaSport;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class ScrapData extends Command
{
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->scrapData();
    }

    protected function configure()
    {
        $this->setName('data:load')
            ->setDescription('Download data');
    }

    private function scrapData()
    {
        $client = new Client();


        $requests = function () {
            $uri = 'http://sportowa.warszawa.pl/adresy/baza-obiektow?page=';
            for ($i = 0; $i < 29; $i++) {
                yield new Request('GET', $uri . $i);
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
                $this->parseResponse($response->getBody());
            },
            'rejected' => function ($reason, $index) {
                echo "Failed because " . $reason . ". For job " . $index;
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();

        // Force the pool of requests to complete.
        $promise->wait();


    }

    private function parseResponse(String $response) {
        // $body = (string) $res->getBody();
        $crawler = new Crawler($response);

        $crawler->filter('.InstitutionsList')->each(function (Crawler $node, $i) {
            $name= $node->filter('address > em')->first()->text();
            $lat = $node->filter('.latitude')->first()->text();
            $lon = $node->filter('.longitude')->first()->text();
            var_dump($name.": ".$lat." ".$lon);
        });

    }
}