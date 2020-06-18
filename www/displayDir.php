<?php
// Reid Mayfield
require 'filetypes.php';

// Check to make sure 'dir' is specified as a GET argument
if (!isset($_GET["dir"])) {
  exit("Directory not specified");
}

$photo_dir .= $_GET["dir"];
if (!($pos = strrpos($photo_dir, "/")) || ($pos != strlen($photo_dir) -1 )) {
  $photo_dir .= "/";
}

// Check to make sure 'dir' is a valid directory
if (!is_dir($photo_dir)) {
  exit("Photo Directory: $photo_dir does not exist");
}

// Open the directory
if (!($dir = opendir($photo_dir))) {
  exit("Could not open Photo Directory");
}

print_html_base();
print_nav_tab($photo_dir);

// Reads the specified directory and sorts images into 'photos' and
// nested directories into 'links'
$links = array();
$photos = array();
echo("<div class=\"container-fluid\"><div class=\"row\">");
while ($file = readdir()) {
  if (strncmp($file, '.', 1)) {
    if (!is_dir($photo_dir . $file)) {
      foreach($file_extensions as $e) {
        if (stripos($file, $e)) {
          array_push($photos, $file);
        }
      }
      if (stripos($file, '.mp4')) {
        array_push($photos, $file);
      }
    }
    else {
      array_push($links, $file);
    }
  }
}

// sort by file name
sort($photos);

// If any nested directories exist, produces the sidebar 
if (count($links) > 0) {
  echo("<div class=\"col-md-2 col-sm-2 col-xs-4\"><ul class=\"list-unstyled\">");
  foreach($links as $l) {
    echo("<li><a href=\"displayDir.php?dir=$photo_dir$l\">$l</a></li>"); 
  }
  echo("</ul></div>");
}

// If there are any pictures, produces the image div
if (count($photos) > 0) {
  echo("<div class=\"col-md-10 col-sm-10 col-xs-8\">");
	echo( "<div class=\"my-gallery\">" );
}
$photoIdx = 0;
foreach($photos as $p) {
  link_photo($photo_dir, $p, $photoIdx);
	$photoIdx++;
}
echo("</div>");
echo("</div>");
echo("</div></div>");

close_html();

function print_html_base() {
  echo("<!DOCTYPE html><html lang=\"en\"><head><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\"><title>Mayfield Photo Gallery</title>" );
  echo("<link href=\"https://stackpath.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-Ej0hUpn6wbrOTJtRExp8jvboBagaz+Or6E9zzWT+gHCQuuZQQVZUcbmhXQzSG17s\" crossorigin=\"anonymous\">");
  echo("<script src=\"http://code.jquery.com/jquery.js\"></script>");
  echo("<script src=\"https://stackpath.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js\" integrity=\"sha384-VI5+XuguQ/l3kUhh4knz7Hxptx47wpQbVRDnp8v7Vvuhzwn1PEYb/uvtH6KLxv6d\" crossorigin=\"anonymous\"></script>");
  echo("<script language=\"javascript\" type=\"text/javascript\" src=\"sub.js\"></script>");
  echo("<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.0.7/photoswipe.min.css\">");
  echo("<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.0.7/default-skin/default-skin.css\">");
  echo("<link rel=\"stylesheet\" href=\"displayDir.css\">");
  echo("<script src=\"https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.0.7/photoswipe.min.js\"></script>");
  echo("<script src=\"https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.0.7/photoswipe-ui-default.min.js\"></script>");
  echo("<script src=\"galleryInit.js\"></script>");
  echo("</head><body>");
}

function close_html() {
  // create Photoswipe gallery DOM
  echo( "<!-- Root element of PhotoSwipe. Must have class pswp. -->
<div class=\"pswp\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">

    <!-- Background of PhotoSwipe. 
         It's a separate element as animating opacity is faster than rgba(). -->
    <div class=\"pswp__bg\"></div>

    <!-- Slides wrapper with overflow:hidden. -->
    <div class=\"pswp__scroll-wrap\">

        <!-- Container that holds slides. 
            PhotoSwipe keeps only 3 of them in the DOM to save memory.
            Don't modify these 3 pswp__item elements, data is added later on. -->
        <div class=\"pswp__container\">
            <div class=\"pswp__item\"></div>
            <div class=\"pswp__item\"></div>
            <div class=\"pswp__item\"></div>
        </div>

        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
        <div class=\"pswp__ui pswp__ui--hidden\">

            <div class=\"pswp__top-bar\">

                <!--  Controls are self-explanatory. Order can be changed. -->

                <div class=\"pswp__counter\"></div>

                <button class=\"pswp__button pswp__button--close\" title=\"Close (Esc)\"></button>

                <button class=\"pswp__button pswp__button--share\" title=\"Share\"></button>

                <button class=\"pswp__button pswp__button--fs\" title=\"Toggle fullscreen\"></button>

                <button class=\"pswp__button pswp__button--zoom\" title=\"Zoom in/out\"></button>

                <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
                <div class=\"pswp__preloader\">
                    <div class=\"pswp__preloader__icn\">
                      <div class=\"pswp__preloader__cut\">
                        <div class=\"pswp__preloader__donut\"></div>
                      </div>
                    </div>
                </div>
            </div>

            <div class=\"pswp__share-modal pswp__share-modal--hidden pswp__single-tap\">
                <div class=\"pswp__share-tooltip\"></div> 
            </div>

            <button class=\"pswp__button pswp__button--arrow--left\" title=\"Previous (arrow left)\">
            </button>

            <button class=\"pswp__button pswp__button--arrow--right\" title=\"Next (arrow right)\">
            </button>

            <div class=\"pswp__caption\">
                <div class=\"pswp__caption__center\"></div>
            </div>

        </div>

    </div>

</div>" );
  echo ("</body></html>");
}

function link_photo($p_dir, $file, $photoIdx) {
  $escape_chars = " &\'()";
  $t_dir = 'thumbs/' . $p_dir;
  $thumb = convert_to_jpg($file);
  // Check to see if thumbnail directory exists
  if (!is_dir($t_dir)) {
    if(!mkdir($t_dir, 0777, true)) {
      exit("mkdir failed on: $t_dir");
    }
  }
	$photoPath = addcslashes($p_dir . $file, $escape_chars);
  if (!file_exists($t_dir . $thumb)) {
    if (stripos($file, '.mp4')) {
      // TODO
      exec( "convert " . $photoPath . "[0] " . addcslashes($t_dir . $thumb, $escape_chars), $out );
    } else {
      exec("convert $photoPath -resize 100x100 " . addcslashes($t_dir . $thumb, $escape_chars));
    }
  }
	
  if (stripos($file, '.mp4')) {
    echo("<a href=\"$p_dir$file\"><img src=\"$t_dir$thumb\" class=\"video-thumbnail\"></a>");
  } else {
    // Find dimensions of the photo
    $identifyCmdOutput = [];
    exec( "identify $photoPath", $identifyCmdOutput );
    if( count( $identifyCmdOutput ) != 1 ) {
    	exit( "identify shell command failed" );
    }
    $dimString = explode( " ", $identifyCmdOutput[ 0 ] )[ 2 ];
    $dim = explode( "x", $dimString );
    echo("<img onclick=\"onThumbClick( $photoIdx )\" src=\"$t_dir$thumb\" class=\"img-thumbnail\" x=" . $dim[ 0 ] . " y=" . $dim[ 1 ] . " idx=$photoIdx " . "photo=$photoPath>");
		$photoIdx++;
  }
}

function convert_to_jpg($f) {
  $exp = explode('.', $f);
  $exp[count($exp) - 1] = 'jpg';
  return implode('.', $exp);
}

function print_nav_tab($d_) {
  echo("<ol class=\"breadcrumb\">");
  $exp = explode('/', $d_);
  array_pop($exp);
  $len = count($exp);
  $link = '';
  for ($i = 0; $i < $len - 1; $i++) {
    $link .= $exp[$i];
    echo("<li><a href=\"displayDir.php?dir=$link\">".$exp[$i]."</a></li>");
    $link .= '/';
  }
  echo("<li class=\"active\">".$exp[$len - 1]."</li>");
  echo( "<button id=\"subBtn\" class=\"btn-small btn-primary pull-right\">Subscribe</button>" );
  echo("</ol>");

  echo( "<div id=\"registerSuccess\" class=\"alert alert-success collapse\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>Email registered successfully</div>" );
  echo( "<div class=\"hide\" id=\"subBody\">" );
  echo( "<input type=\"text\" id=\"email\" placeholder=\"email\">" );
  echo( "<button class=\"btn-small\" onclick=\"subscribe();\">Submit</button>" );
  echo( "</div>" );
}
?>
