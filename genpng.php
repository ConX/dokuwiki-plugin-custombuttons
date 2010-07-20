<?php
header("Content-type: image/png"); //Picture Format
header("Expires: Mon, 01 Jul 2003 00:00:00 GMT"); // Past date
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Consitnuously modified
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // NO CACHE

/*image generation code*/
//create Image of size 350px x 75px
$bg = imagecreatetruecolor(isset($_GET['text']) ? strlen($_GET['text'])*11 : 66, 10);

//This will make it transparent
imagesavealpha($bg, true);

$trans_colour = imagecolorallocatealpha($bg, 0, 0, 0, 127);
imagefill($bg, 0, 0, $trans_colour);

//Text to be written
$text = isset($_GET['text']) ? $_GET['text'] : "No Name";

// Black Text
$black = imagecolorallocate($bg, 0,0,0);

$font = './DejaVuSans.ttf'; //path to font you want to use
$fontsize = 10; //size of font

//Writes text to the image using fonts using FreeType 2
imagettftext($bg, $fontsize, 0, 10, 10, $black, $font, $text);

//Create image
imagepng($bg);

//destroy image
ImageDestroy($bg);
?>
