#!/usr/bin/php
<?php
require_once 'emailerPrivateInfo.php';
// This should only be ran from cron
// don't allow it to be run from web
if (isset($_SERVER['REMOTE_ADDR'])) die('Permission denied.');

$seperator = "/";
$pathComponents = explode( $seperator, __FILE__ );
$workDir = implode( $seperator, array_slice( $pathComponents, 0, count( $pathComponents ) - 1 ) );
chdir( $workDir );

$options = getopt( "", array( "interval::" ) );
if( isset( $options[ 'interval' ] ) && empty( $options[ 'interval' ] ) ) {
   die( "--interval requires an argument in minutes (e.g. --interval=45)\n" );
}

$punchcard = '.punchcard';

function handleDir( $dirString ) {
   global $prevTime, $newAlbums;
   if( !( $dir = opendir( $dirString ) ) ) {
     exit( "Could not open $dirString\n" );
   }
   $leaf = true;
   while( $f = readdir( $dir ) ) {
      if( is_dir( $dirString . $f ) && strncmp( $f, '.', 1 ) &&
          strcmp( $f, 'Photo Stream' ) != 0 ) {
         $isLeaf = handleDir( $dirString . $f . '/' );
         $ctime = filemtime( $dirString . $f );
         if( $isLeaf && $ctime > $prevTime ) {
            array_push( $newAlbums, $dirString . $f );
            $leaf = false;
         }
      }
   }
   return $leaf;
}

do {
   // check last runtime by getting mtime of punchcard file
   $prevTime = filemtime( $punchcard );
   // update mtime by touching punchcard
   touch( $punchcard );
   echo( "Previous run " . date( "F d Y H:i:s.", $prevTime ) ."\n" );
   $newAlbums = array();
   handleDir( 'Home/' );
   if( !empty( $newAlbums ) ) {
      echo( "Found new albums!\n" );
      print_r( $newAlbums );
      $manager = new MongoDB\Driver\Manager();

      require_once 'swiftmailer/lib/swift_required.php';
      $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
        ->setUsername( $emailUsername )
        ->setPassword( $emailPassword );

      $mailer = Swift_Mailer::newInstance($transport);
      $prefix = 'http://beanpictures.homeftp.net/sendNotification.php?path=';
      $msg = file_get_contents( 'newAlbum.template' );
      foreach( $newAlbums as $album ) {
         $ar = explode( '/', $album );
         $title = end( $ar );
         $key = sha1(microtime(true).mt_rand(10000,90000));
         $msg .= "<a href=\"$prefix$album&key=$key\">$title</a><br>";

         $command = new MongoDB\Driver\Command( array( "update" => "albums",
                          "updates" => [ array( "q" => array( "path" => $album ),
                                                "u" => array( "path" => $album,
                                                              "key" => $key,
                                                              "notified" => false ),
                                                "upsert" => true ) ] ) );
         $manager->executeCommand( 'main', $command );
      }
      $msg .= "</body></html>";
      $message = Swift_Message::newInstance('Album Notification Confirmation')
        ->setFrom(array( $emailUsername => 'Album Crawler'))
        ->setTo(array('brockband1@gmail.com'))
        ->setCC(array('rmayf3@gmail.com' ) )
        ->setBody( $msg, 'text/html' );
      //A4NOMERGE
      //$result = $mailer->send($message);
   }
} while( isset( $options[ 'interval' ] ) && sleep( $options[ 'interval' ] * 60 ) == 0 );
