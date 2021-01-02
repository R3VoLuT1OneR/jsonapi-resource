<?php

// Please change the path to your autoload
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/People.php';

use JSONAPI\Resource\Serializer;

$people = new People(1, 'Pavel Z', 31);
$serializer = new Serializer();
$data = $serializer->serialize($people);

echo json_encode(['data' => $data], JSON_PRETTY_PRINT);
