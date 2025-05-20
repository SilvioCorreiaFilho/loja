<?php
/**
 * Product Image Updater for WooCommerce
 * Updates product images with original images maintaining white backgrounds
 */

// Check if this is being run in WordPress context
if (!defined('ABSPATH')) {
    if (php_sapi_name() !== 'cli') {
        die('This script must be run from WordPress context or CLI');
    }
}

class ProductImageUpdater {
    private $image_directory;
    private $processed_count = 0;
    private $errors = [];
    
    public function __construct($image_directory = null) {
        $this->image_directory = $image_directory;
    }
    
    /**
     * Main process to update product images
     */
    public function process_images() {
        echo "Starting product image update process...\n";
        
        // Verify image directory
        if (!$this->verify_image_directory()) {
            return false;
        }
        
        // Get all image files
        $image_files = $this->get_image_files();
        if (empty($image_files)) {
            echo "No image files found in the specified directory.\n";
            return false;
        }
        
        // Process each image
        foreach ($image_files as $image_file) {
            $this->process_single_image($image_file);
        }
        
        // Output results
        $this->output_results();
        
        return true;
    }
    
    /**
     * Verify the image directory exists and is readable
     */
    private function verify_image_directory() {
        if (!$this->image_directory) {
            echo "Error: No image directory specified.\n";
            return false;
        }
        
        if (!is_dir($this->image_directory)) {
            echo "Error: Directory not found: {$this->image_directory}\n";
            return false;
        }
        
        if (!is_readable($this->image_directory)) {
            echo "Error: Directory not readable: {$this->image_directory}\n";
            return false;
        }
        
        return true;
    }
    
    /**
     * Get all valid image files from the directory
     */
    private function get_image_files() {
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $image_files = [];
        
        $files = new DirectoryIterator($this->image_directory);
        foreach ($files as $file) {
            if ($file->isDot() || !$file->isFile()) {
                continue;
            }
            
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $valid_extensions)) {
                $image_files[] = $file->getPathname();
            }
        }
        
        return $image_files;
    }
    
    /**
     * Process a single image file
     */
    private function process_single_image($image_path) {
        echo "Processing: " . basename($image_path) . "\n";
        
        // Verify image has white background
        if (!$this->verify_white_background($image_path)) {
            $this->errors[] = "Image does not have a white background: " . basename($image_path);
            return false;
        }
        
        // Optimize image
        $optimized_path = $this->optimize_image($image_path);
        if (!$optimized_path) {
            $this->errors[] = "Failed to optimize image: " . basename($image_path);
            return false;
        }
        
        // Upload to WordPress media library
        $attachment_id = $this->upload_to_media_library($optimized_path);
        if (!$attachment_id) {
            $this->errors[] = "Failed to upload to media library: " . basename($image_path);
            return false;
        }
        
        // Try to match with product
        if ($this->match_with_product($attachment_id, $image_path)) {
            $this->processed_count++;
            return true;
        }
        
        return false;
    }
    
    /**
     * Verify image has a white background
     */
    private function verify_white_background($image_path) {
        if (!function_exists('imagecreatefromstring')) {
            echo "Warning: GD library not available. Skipping white background check.\n";
            return true;
        }
        
        $image = imagecreatefromstring(file_get_contents($image_path));
        if (!$image) {
            return false;
        }
        
        // Check corners for white color
        $corners = [
            [0, 0],
            [0, imagesy($image) - 1],
            [imagesx($image) - 1, 0],
            [imagesx($image) - 1, imagesy($image) - 1]
        ];
        
        foreach ($corners as $corner) {
            $rgb = imagecolorat($image, $corner[0], $corner[1]);
            $colors = imagecolorsforindex($image, $rgb);
            
            // Allow slight variation in white (250+ for each channel)
            if ($colors['red'] < 250 || $colors['green'] < 250 || $colors['blue'] < 250) {
                imagedestroy($image);
                return false;
            }
        }
        
        imagedestroy($image);
        return true;
    }
    
    /**
     * Optimize image for web use
     */
    private function optimize_image($image_path) {
        if (!function_exists('imagecreatefromstring')) {
            echo "Warning: GD library not available. Skipping optimization.\n";
            return $image_path;
        }
        
        // Load image
        $image = imagecreatefromstring(file_get_contents($image_path));
        if (!$image) {
            return false;
        }
        
        // Get original dimensions
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Maximum dimensions for product images
        $max_width = 1024;
        $max_height = 1024;
        
        // Calculate new dimensions
        if ($width > $max_width || $height > $max_height) {
            $ratio = min($max_width / $width, $max_height / $height);
            $new_width = round($width * $ratio);
            $new_height = round($height * $ratio);
            
            // Create resized image
            $resized = imagecreatetruecolor($new_width, $new_height);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            
            // Save optimized image
            $optimized_path = $this->image_directory . '/optimized_' . basename($image_path);
            imagepng($resized, $optimized_path, 9); // Maximum PNG compression
            
            imagedestroy($resized);
            imagedestroy($image);
            
            return $optimized_path;
        }
        
        imagedestroy($image);
        return $image_path;
    }
    
    /**
     * Upload image to WordPress media library
     */
    private function upload_to_media_library($image_path) {
        if (!function_exists('wp_upload_bits')) {
            echo "Error: WordPress functions not available.\n";
            return false;
        }
        
        $wp_upload_dir = wp_upload_dir();
        $filename = basename($image_path);
        
        // Prepare file array
        $file = [
            'name' => $filename,
            'type' => mime_content_type($image_path),
            'tmp_name' => $image_path,
            'error' => 0,
            'size' => filesize($image_path)
        ];
        
        // Include necessary WordPress files
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        // Upload and get attachment ID
        $attachment_id = media_handle_sideload($file, 0);
        
        if (is_wp_error($attachment_id)) {
            echo "Error uploading file: " . $attachment_id->get_error_message() . "\n";
            return false;
        }
        
        return $attachment_id;
    }
    
    /**
     * Match image with WooCommerce product
     */
    private function match_with_product($attachment_id, $image_path) {
        if (!class_exists('WC_Product_Query')) {
            echo "Error: WooCommerce not active.\n";
            return false;
        }
        
        // Try to match by filename
        $filename = pathinfo($image_path, PATHINFO_FILENAME);
        
        // Clean filename for matching
        $clean_filename = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($filename));
        
        // Query products
        $query = new WC_Product_Query([
            'limit' => 1,
            'status' => 'publish'
        ]);
        
        $products = $query->get_products();
        
        foreach ($products as $product) {
            $product_name = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($product->get_name()));
            $sku = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($product->get_sku()));
            
            if ($clean_filename == $product_name || $clean_filename == $sku) {
                // Set product image
                $product->set_image_id($attachment_id);
                $product->save();
                
                echo "Matched and updated image for product: " . $product->get_name() . "\n";
                return true;
            }
        }
        
        echo "No matching product found for: " . basename($image_path) . "\n";
        return false;
    }
    
    /**
     * Output processing results
     */
    private function output_results() {
        echo "\nProcessing completed!\n";
        echo "Successfully processed: {$this->processed_count} images\n";
        
        if (!empty($this->errors)) {
            echo "\nErrors encountered:\n";
            foreach ($this->errors as $error) {
                echo "- {$error}\n";
            }
        }
    }
}

// If running from CLI
if (php_sapi_name() === 'cli') {
    // Check if directory parameter is provided
    if ($argc < 2) {
        echo "Usage: php product_image_updater.php <image_directory>\n";
        exit(1);
    }
    
    $updater = new ProductImageUpdater($argv[1]);
    $updater->process_images();
}

// WP-CLI command registration
if (defined('WP_CLI') && WP_CLI) {
    /**
     * Manages product images for WooCommerce.
     */
    class Product_Image_Command {
        /**
         * Updates product images from a directory.
         *
         * ## OPTIONS
         *
         * <directory>
         * : The directory containing product images.
         *
         * ## EXAMPLES
         *
         *     wp product-image update /path/to/images
         */
        public function update($args) {
            $updater = new ProductImageUpdater($args[0]);
            $result = $updater->process_images();
            
            if ($result) {
                WP_CLI::success('Product images updated successfully.');
            } else {
                WP_CLI::error('Failed to update product images.');
            }
        }
    }
    
    WP_CLI::add_command('product-image', 'Product_Image_Command');
}
?>
