<?php

$user = (string)$_GET[ 'user' ];
$key = (string)$_GET[ 'key' ];

$manager = new MongoDB\Driver\Manager("mongodb://mongo:27017");
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->delete( [ 'email' => $user, 'key' => $key ] );
$manager->executeBulkWrite( 'main.users', $bulk );
echo( 'You have successfully unsubscribed' );
