<?php

// Please change the path to your autoload
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/Articles.php';
include __DIR__.'/People.php';

use JSONAPI\Resource\Fieldset;
use JSONAPI\Resource\Includeset;
use JSONAPI\Resource\Serializer;

// Prepare our mock data, in real example it is fetched using ORM
$pavel = new People(17, 'Pavel Z', 31);
$dan = new People(18, 'Dan', 43);
$article1 = new Articles(1, 'PHP JSON:API Resource', $pavel);
$article2 = new Articles(2, 'How to create JSON:API', $dan);

// Collection of all available articles
$collection = [$article1, $article2];

// fields[people]=age
$fieldset = new Fieldset(['people' => 'age']);

// include=author
$includeset = Includeset::fromString('author');

$serializer = new Serializer(
    fieldset: $fieldset,
    includeset: $includeset,
);

// Our main data
$data = $serializer->serialize($collection);

// It is how we fetch compound data or included data
$included = $serializer->compoundData();

echo json_encode(['data' => $data, 'included' => $included], JSON_PRETTY_PRINT);
