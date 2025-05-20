<?php
// Create a 400x400 image with white background
$image = imagecreatetruecolor(400, 400);
$white = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $white);

// Add some sample product text
$black = imagecolorallocate($image, 0, 0, 0);
imagestring($image, 5, 150, 190, "Sample Product", $black);

// Save as PNG with white background
imagepng($image, "/tmp/test_images/sample-product.png");

// Save as JPG with white background
imagejpeg($image, "/tmp/test_images/sample-product.jpg", 90);

imagedestroy($image);

echo "Test images created successfully!\n";
?>
