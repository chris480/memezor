<?php
//get our quote
$quote = "";
if(isset($_GET["quote"])) $quote = $_GET["quote"];
						  $quote2 = $_GET["quote2"];
if(isset($_GET["meme"])) $meme = $_GET["meme"]; else $meme = "meme.jpg";

// Path to our font file
$font = 'IMPACT.TTF';
$fontsize = 18;
$y = 25;
$x = 5;

// Change fontsize based on number of characters
 if( ((strlen($quote) > 20 && str_word_count($quote) >3 ) )|| ((strlen($quote2) > 20 && str_word_count($quote2) >3 ))){
	$fontsize = 14;
}

// get the quote and word wrap it
$quote = wordwrap($quote,30);
$quote2 = wordwrap($quote2,30);
// Create image from input source
$image = imagecreatefromjpeg($meme);
$attr = getimagesize($meme);

imageTextWrapped($image, $x, $y, $attr[0], $font, $font_color, $quote, $fontsize);
imageTextWrapped($image, $x, $attr[1]*.8, $attr[0], $font, $font_color, $quote2, $fontsize);

/*

*/
// tell the browser that the content is an image
header('Content-type:image/jpg');
// output image to the browser
imagepng($image);
// delete the image resource 
imagedestroy($image);


//A function for pixel precise text Wrapping
function imageTextWrapped(&$img, $x, $y, $width, $font, $color, $text, $textSize) {
	// pick color for the text
	$stroke_color = imagecolorallocate($img, 0, 0, 0);
	$font_color = imagecolorallocate($img, 255, 255, 255);
    //Recalculate X and Y to have the proper top/left coordinates instead of TTF base-point
    $y += $textSize;
    $dimensions = imagettfbbox($textSize, 0, $font, " "); //use a custom string to get a fixed height.
    $x -= $dimensions[4]-$dimensions[0];

    $text = str_replace ("\r", '', $text); //Remove windows line-breaks
    $srcLines = split ("\n", $text); //Split text into "lines"
    $dstLines = Array(); // The destination lines array.
    foreach ($srcLines as $currentL) {
        $line = '';
        $words = split (" ", $currentL); //Split line into words.
        foreach ($words as $word) {
            $dimensions = imagettfbbox($textSize, 0, $font, $line.$word);
            $lineWidth = $dimensions[4] - $dimensions[0]; // get the length of this line, if the word is to be included
            if ($lineWidth > $width && !empty($line) ) { // check if it is too big if the word was added, if so, then move on.
                $dstLines[] = ' '.trim($line); //Add the line like it was without spaces.
                $line = '';
            }
            $line .= $word.' ';
        }
        $dstLines[] =  ' '.trim($line); //Add the line when the line ends.
    }
    //Calculate lineheight by common characters.
    $dimensions = imagettfbbox($textSize, 0, $font, "ABCDEfghij123345"); //use a custom string to get a fixed height.
    $lineHeight = $dimensions[1] - $dimensions[5]; // get the heightof this line

    foreach ($dstLines as $nr => $line) {
        $dimensions = imagettfbbox($textSize, 0, $font, $line);
        $lineWidth = $dimensions[4] - $dimensions[0]; // get the length of this line
        $locX = $x + ($width/2) - ($lineWidth/2);
        $locY = $y + ($nr * $lineHeight);
        //Print the line.
		imagettfstroketext($img, $textSize, 0, $locX, $locY, $font_color, $stroke_color, $font, $line, 1,$dimensions );

    }        
}
function imagettfstroketext(&$image, $size, $angle, $x, $y, &$textcolor, &$strokecolor, $fontfile, $text, $px,$dims ) {
    for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
        for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
            $bg = imagettftext($image, $size, $angle, $c1, $c2, $strokecolor, $fontfile, $text);

   return imagettftext($image, $size, $angle, $x, $y, $textcolor, $fontfile, $text);
}
?>