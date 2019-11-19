<?php
require 'filetypes.php';
// Reid Mayfield
echo ("<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><title>Photo Viewer</title><link href=\"bootstrap.min.css\" rel=\"stylesheet\"><link rel=\"stylesheet\" type=\"text/css\" href=\"viewer.css\"></head><body>");

if (!isset($_GET["dir"]) || !isset($_GET["photo"])) {
  exit("Params not specified");
}
$photo_dir = $_GET["dir"];
$photo = $_GET["photo"];

// Output the photo itselt
//echo ("<img src=\"$photo_dir$photo\">");
echo ("<div class=\"container\"><img src=\"$photo_dir$photo\"></div>");

// Search for file in Dir
opendir($photo_dir);
$fs = array();
$acc = 0;
while ($f = readdir()) {
  if ($f == $photo) {
    $i = $acc;
  }
  foreach($file_extensions as $e) {
    if (stripos($f, $e)) {
      $acc++;
      array_push($fs, $f);
    }
  }
}
// Output the forward and back buttons
echo ("<div class=\"btn-group col-md-offset-5 col-sm-offset-4 col-xs-offset-2\">");
if ($i != 0) {
  echo ("<a href=\"viewer.php?dir=$photo_dir&amp;photo=".$fs[$i - 1]."\" class=\"btn btn-lrg\">");
  echo ("<span class=\"glyphicon glyphicon-arrow-left\"></span> Prev");
  echo ("</a>");
}
echo ("<a href=\"displayDir.php?dir=$photo_dir\" class=\"btn btn-lrg\">");
  echo ("<span class=\"glyphicon glyphicon-th\"></span> Gallery");
echo ("</a>");
if ($i != count($fs) - 1) {
  echo ("<a href=\"viewer.php?dir=$photo_dir&amp;photo=".$fs[$i + 1]."\" class=\"btn btn-lrg\">");
  echo ("Next <span class=\"glyphicon glyphicon-arrow-right\"></span>");
  echo ("</a>");
}
echo ("</div>");
echo ("</body></html>");
?>
