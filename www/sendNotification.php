<?php
require_once 'emailerPrivateInfo.php';

$manager = new MongoDB\Driver\Manager("mongodb://mongo:27017");
$path = (string)$_GET[ 'path' ];
$key = (string)$_GET[ 'key' ];
$query = new MongoDB\Driver\Query(
		array( 'path' => $path, 'key' => $key ),
		array() );
$cursor = $manager->executeQuery( "main.albums", $query );
$albums = $cursor->toArray();
if( empty( $albums ) ) {
   die( 'path/key pair is wrong' );
}
if( count( $albums ) > 1 ) {
   die( 'multiple entries returned for path/key pair' );
}
if( $albums[ 0 ]->notified ) {
   die( 'notification already sent' );
}
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->update( [ 'path' => $path ],
               [ '$set' => [ 'notified' => true ] ] );
$result = $manager->executeBulkWrite( 'main.albums', $bulk );
$query = new MongoDB\Driver\Query( array(), array() );
$it = $manager->executeQuery( "main.users",  $query );

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
   $msg = str_replace( '{user}', $user->email, $msg );
   $msg = str_replace( '{key}', $user->key, $msg );
   $message->setTo( array( $user->email ) )
  	   ->setBody( $msg, 'text/html' );
   $result = $mailer->send($message);
}
echo( "the deed is done!\n" );
