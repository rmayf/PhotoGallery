<?php

$email = (string) $_GET[ 'email' ];

$mongo = new MongoClient();
$users = $mongo->main->users;

$users->update( array( 'email' => $email ),
                array( 'email' => $email, 'key' => sha1(microtime(true).mt_rand(10000,90000)) ),
                array( 'upsert' => true ) );
echo( 'Thank you for subscribing' );
