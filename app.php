#!/usr/bin/env php
<?php

use Kir\WawaSport\ScrapData;
use Symfony\Component\Console\Application;

require_once __DIR__.'/vendor/autoload.php';

$app = new Application();
$app->add(new ScrapData());
$app->run();

