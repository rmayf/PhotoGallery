<?php
// Reid Mayfield
// Wed Jul 12 22:49:21 PDT 2023

require_once 'emailerPrivateInfo.php';

// Check to make sure album's 'key' is specified as a GET arg
if (!isset($_GET["key"])) {
  exit("Key not specified");
}
$album_key = $_GET["key"];

// Look up 'key' in the database
$manager = new MongoDB\Driver\Manager("mongodb://mongo:27017");
$query = new MongoDB\Driver\Query( array( 'key' => $album_key ), array() );
$cursor = $manager->executeQuery( "main.albums", $query );
$albums = $cursor->toArray();
if( empty( $albums ) ) {
   die( 'album key not found in db' );
}
if( count( $albums ) > 1 ) {
   die( 'multiple entries returned for album key' );
}

// Prepare GET arguments for displayDir
unset($_GET["key"]);
$_GET["dir"] = $albums[ 0 ]->path;
$skip_breadcrumbs = true;

require 'displayDir.php';
