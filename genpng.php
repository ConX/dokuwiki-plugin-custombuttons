<?php
header("Content-type: image/png");
header("Expires: Mon, 01 Jul 2003 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

/* image generation code */

//Text to be written
$text = $_GET['text'] ?? "No Name";

//font
$font = './DejaVuSans.ttf'; //path to font you want to use
$fontsize = 10; //size of font

//calculate width from bounding box for the text
$tb = imagettfbbox($fontsize, 0, $font, $text);
$width = $tb[2] - $tb[0];

$bg = imagecreatetruecolor($width, 16);

//This will make it transparent
imagesavealpha($bg, true);
$trans_colour = imagecolorallocatealpha($bg, 0, 0, 0, 127);
imagefill($bg, 0, 0, $trans_colour);

// Black Text
$black = imagecolorallocate($bg, 0, 0, 0);

//Writes text to the image using fonts using a TrueType font
//no x margin, because button adds margin as well
imagettftext($bg, $fontsize, 0, 0, 12, $black, $font, $text);

//Create image
imagepng($bg);

//destroy image
ImageDestroy($bg);
