<?php

$user = (string)$_GET[ 'user' ];
$key = (string)$_GET[ 'key' ];

$mongo = new MongoClient();
$users = $mongo->main->users;

$users->remove( array( 'email' => $user, 'key' => $key ) );
echo( 'You have successfully unsubscribed' );
