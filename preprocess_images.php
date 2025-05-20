<?php
// Configuration
define('OUTPUT_DIR', 'processed_images');
define('QUALITY', 90);
define('MAX_WIDTH', 1024);
define('THUMB_SIZE', 600);

// Create output directory
if (!file_exists(OUTPUT_DIR)) {
    mkdir(OUTPUT_DIR, 0755, true);
}

class ImageProcessor {
    private $stats = [
        'processed' => 0,
        'skipped' => 0,
        'errors' => []
    ];

    /**
     * Process all images in directory
     */
    public function processDirectory($inputDir) {
        if (!is_dir($inputDir)) {
            die("Error: Input directory not found: $inputDir\n");
        }

        $images = glob("$inputDir/*.[jJ][pP][gG]") + 
                 glob("$inputDir/*.[jJ][pP][eE][gG]") +
                 glob("$inputDir/*.[pP][nN][gG]");

        foreach ($images as $imagePath) {
            try {
                $this->processImage($imagePath);
            } catch (Exception $e) {
                $this->stats['errors'][] = basename($imagePath) . ": " . $e->getMessage();
                $this->stats['skipped']++;
            }
        }

        $this->outputStats();
    }

    /**
     * Process single image
     */
    private function processImage($imagePath) {
        echo "Processing: " . basename($imagePath) . "\n";
        
        // Load image
        $image = $this->loadImage($imagePath);
        if (!$image) {
            throw new Exception("Failed to load image");
        }

        // Get original dimensions
        $origWidth = imagesx($image);
        $origHeight = imagesy($image);

        // Resize if needed
        if ($origWidth > MAX_WIDTH) {
            $ratio = MAX_WIDTH / $origWidth;
            $newWidth = MAX_WIDTH;
            $newHeight = $origHeight * $ratio;
            
            $resized = imagecreatetruecolor($newWidth, $newHeight);
            $this->prepareBackground($resized);
            
            imagecopyresampled(
                $resized, $image,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $origWidth, $origHeight
            );
            
            imagedestroy($image);
            $image = $resized;
        }

        // Ensure white background
        $image = $this->ensureWhiteBackground($image);

        // Save main image
        $info = pathinfo($imagePath);
        $baseName = $info['filename'];
        $extension = $this->determineOutputFormat($imagePath);
        
        $mainPath = OUTPUT_DIR . "/$baseName.$extension";
        $this->saveImage($image, $mainPath, QUALITY);

        // Create thumbnail
        $thumbnail = $this->createThumbnail($image, THUMB_SIZE);
        $thumbPath = OUTPUT_DIR . "/$baseName-" . THUMB_SIZE . "x" . THUMB_SIZE . ".$extension";
        $this->saveImage($thumbnail, $thumbPath, QUALITY);

        imagedestroy($thumbnail);
        imagedestroy($image);

        $this->stats['processed']++;
    }

    /**
     * Load image from file
     */
    private function loadImage($path) {
        $type = exif_imagetype($path);
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            default:
                throw new Exception("Unsupported image type");
        }
    }

    /**
     * Prepare transparent background
     */
    private function prepareBackground($image) {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefilledrectangle($image, 0, 0, imagesx($image), imagesy($image), $transparent);
        imagealphablending($image, true);
    }

    /**
     * Ensure white background for images
     */
    private function ensureWhiteBackground($image) {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $newImage = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefill($newImage, 0, 0, $white);
        
        imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);
        
        return $newImage;
    }

    /**
     * Create square thumbnail
     */
    private function createThumbnail($image, $size) {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $thumbnail = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($thumbnail, 255, 255, 255);
        imagefill($thumbnail, 0, 0, $white);
        
        if ($width > $height) {
            $ratio = $size / $height;
            $newWidth = $width * $ratio;
            $x = ($newWidth - $size) / 2;
            imagecopyresampled($thumbnail, $image, -$x, 0, 0, 0, $newWidth, $size, $width, $height);
        } else {
            $ratio = $size / $width;
            $newHeight = $height * $ratio;
            $y = ($newHeight - $size) / 2;
            imagecopyresampled($thumbnail, $image, 0, -$y, 0, 0, $size, $newHeight, $width, $height);
        }
        
        return $thumbnail;
    }

    /**
     * Determine output format based on input
     */
    private function determineOutputFormat($path) {
        return (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'png') ? 'png' : 'jpg';
    }

    /**
     * Save image with proper format
     */
    private function saveImage($image, $path, $quality) {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if ($extension === 'png') {
            imagepng($image, $path, 9);
        } else {
            imagejpeg($image, $path, $quality);
        }
    }

    /**
     * Output processing statistics
     */
    private function outputStats() {
        echo "\nProcessing completed!\n";
        echo "Processed: {$this->stats['processed']} images\n";
        echo "Skipped: {$this->stats['skipped']} images\n";
        
        if (!empty($this->stats['errors'])) {
            echo "\nErrors encountered:\n";
            foreach ($this->stats['errors'] as $error) {
                echo "- $error\n";
            }
        }
    }
}

// Run processor if called directly
if (php_sapi_name() === 'cli') {
    if ($argc < 2) {
        die("Usage: php " . basename(__FILE__) . " <input_directory>\n");
    }
    
    $processor = new ImageProcessor();
    $processor->processDirectory($argv[1]);
}
?>
