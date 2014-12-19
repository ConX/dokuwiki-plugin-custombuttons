<?php
header("Content-type: image/png");
header("Expires: Mon, 01 Jul 2003 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

/*image generation code*/
if(isset($_GET['text'])) {
    $width = strlen($_GET['text']) * 11;
} else {
    $width = 66;
}
$bg = imagecreatetruecolor($width, 15);

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
imagettftext($bg, $fontsize, 0, 10, 12, $black, $font, $text);

//Create image
imagepng($bg);

//destroy image
ImageDestroy($bg);
