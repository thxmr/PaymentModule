<?php 
require '../vendor/autoload.php';

use GuzzleHttp\Client;

$clientPayment = new Client([
    // Base URI, (url de l'API)
    'base_uri' => 'localhost:3002',
    'timeout' => 2.0,
]);
$clientClient = new Client([
    'base_uri' => 'localhost:3000',
    'timeout' => 2.0
]);

$res = $clientPayment->request('GET', 'invoice/' . $_GET['transaction_id']);
$body = get_object_vars(json_decode($res->getBody()));
print_r($body);
?>
