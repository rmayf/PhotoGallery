<?php
require_once 'emailerPrivateInfo.php';

$mongo = new MongoClient();
$main = $mongo->main;
$albums = $main->albums;
$users = $main->users;
$path = (string)$_GET[ 'path' ];
$key = (string)$_GET[ 'key' ];
$album = $albums->findOne( array( 'path' => $path, 'key' => $key ) );
if( is_null( $album ) ) {
   die( 'path/key pair is wrong' );
}
if( $album[ 'notified' ] ) {
   die( 'notification already sent' );
}
$albums->update( array( 'path' => $path ),
                 array( 'path' => $path, 'key' => $key, 'notified' => true ) );
$it = $users->find();

require_once 'swiftmailer/lib/swift_required.php';

$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
  ->setUsername( $emailUsername )
  ->setPassword( $emailPassword );

$mailer = Swift_Mailer::newInstance($transport);

$template = file_get_contents( 'notification.template' );
$template = str_replace( '{path}', $path, $template );
$template = str_replace( '{album}', end(explode( '/', $path ) ), $template );
$message = Swift_Message::newInstance('New Mayfield Photo Album')
  ->setFrom(array( $emailUsername => 'Mayfield Photo Gallery'));

foreach( $it as $user ) {
   $msg = $template;
   $msg = str_replace( '{user}', $user[ 'email' ], $msg );
   $msg = str_replace( '{key}', $user[ 'key' ], $msg );
   $message->setTo( array( $user[ 'email' ] ) ) 
  	   ->setBody( $msg, 'text/html' );
   $result = $mailer->send($message);
}
echo( "the deed is done!\n" );
