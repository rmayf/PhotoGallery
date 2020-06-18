<?php
$email = (string)$_GET[ 'email' ];
if( empty( $email ) ) {
   die( 'no email specified in GET options' );
}
$key = sha1( microtime( true ).mt_rand( 10000, 90000 ) );

$manager = new MongoDB\Driver\Manager("mongodb://mongo:27017");
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->update( [ 'email' => $email ],
	       [ '$set' => [ 'key' => $key ] ],
	       [ 'upsert' => true ] );
$manager->executeBulkWrite( 'main.users', $bulk );
echo( 'Thank you for subscribing' );
