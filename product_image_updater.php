<?php
/**
 * Product Image Updater for WooCommerce
 * Updates product images with original images maintaining white backgrounds
 */

// Bootstrap WordPress when running from CLI
if (php_sapi_name() === 'cli' && !defined('ABSPATH')) {
    $wp_load_paths = [
        '/var/www/html/wp-load.php',
        dirname(__FILE__) . '/wp-load.php',
        dirname(dirname(__FILE__)) . '/wp-load.php'
    ];
    
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            break;
        }
    }
    
    if (!defined('ABSPATH')) {
        die('WordPress environment not found. Please run this script from the correct directory.');
    }
}

class ProductImageUpdater {
    private $image_directory;
    private $processed_count = 0;
    private $skipped_count = 0;
    private $errors = [];
    private $updated_products = [];
    private $duplicate_images = [];
    
    public function __construct($image_directory = null) {
        $this->image_directory = $image_directory;
        
        // Initialize WordPress environment
        if (!function_exists('wp_get_current_user')) {
            require_once(ABSPATH . 'wp-includes/pluggable.php');
        }
        
        // Load WooCommerce
        if (!class_exists('WC_Product')) {
            include_once(ABSPATH . 'wp-content/plugins/woocommerce/includes/class-wc-product.php');
        }
    }
    
    /**
     * Main process to update product images
     */
    public function process_images() {
        echo "Starting product image update process...\n";
        
        // Verify WordPress and WooCommerce are available
        if (!$this->verify_environment()) {
            return false;
        }
        
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
        
        // Group images by base name to detect duplicates
        $grouped_images = $this->group_images_by_name($image_files);
        
        // Process each group of images
        foreach ($grouped_images as $base_name => $images) {
            $this->process_image_group($base_name, $images);
        }
        
        // Output results
        $this->output_results();
        
        return true;
    }
    
    /**
     * Verify WordPress and WooCommerce environment
     */
    private function verify_environment() {
        if (!function_exists('wp_get_current_user')) {
            echo "Error: WordPress functions not available.\n";
            return false;
        }
        
        if (!class_exists('WC_Product')) {
            echo "Error: WooCommerce not available.\n";
            return false;
        }
        
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
        $valid_extensions = ['jpg', 'jpeg', 'png'];
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
     * Group images by base name to handle duplicates
     */
    private function group_images_by_name($image_files) {
        $grouped = [];
        foreach ($image_files as $file) {
            $info = pathinfo($file);
            $base_name = preg_replace('/-\d+x\d+$/', '', $info['filename']); // Remove size suffix
            $grouped[$base_name][] = $file;
        }
        return $grouped;
    }
    
    /**
     * Process a group of images with the same base name
     */
    private function process_image_group($base_name, $images) {
        if (count($images) > 1) {
            $this->duplicate_images[$base_name] = $images;
            echo "Found multiple images for '$base_name'. Using the highest quality version.\n";
            
            // Sort by file size (assuming larger = higher quality)
            usort($images, function($a, $b) {
                return filesize($b) - filesize($a);
            });
        }
        
        $this->process_single_image($images[0]);
    }
    
    /**
     * Process a single image file
     */
    private function process_single_image($image_path) {
        echo "Processing: " . basename($image_path) . "\n";
        
        // Get filename without extension and size suffix
        $filename = pathinfo($image_path, PATHINFO_FILENAME);
        $base_name = preg_replace('/-\d+x\d+$/', '', $filename);
        
        // Clean filename for matching
        $clean_filename = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($base_name));
        
        // Find matching product
        $product_id = $this->find_matching_product($clean_filename);
        if (!$product_id) {
            $this->errors[] = "No matching product found for: " . basename($image_path);
            $this->skipped_count++;
            return false;
        }
        
        // Check if product was already updated
        if (in_array($product_id, $this->updated_products)) {
            echo "Product already updated. Skipping duplicate image.\n";
            $this->skipped_count++;
            return false;
        }
        
        // Get attachment ID
        $attachment_id = $this->get_attachment_id($image_path);
        if (!$attachment_id) {
            $this->errors[] = "Failed to get attachment ID for: " . basename($image_path);
            $this->skipped_count++;
            return false;
        }

        if ($this->update_product_image($product_id, $attachment_id)) {
            $this->processed_count++;
            $this->updated_products[] = $product_id;
            return true;
        }
        
        return false;
    }
    
    /**
     * Find matching product by name or SKU
     */
    private function find_matching_product($clean_filename) {
        global $wpdb;
        
        // First try by product name
        $product_id = $wpdb->get_var($wpdb->prepare("
            SELECT ID 
            FROM {$wpdb->posts} 
            WHERE post_type = 'product' 
            AND post_status = 'publish'
            AND REPLACE(LOWER(post_title), ' ', '') LIKE %s
        ", '%' . $clean_filename . '%'));
        
        if ($product_id) {
            return $product_id;
        }
        
        // Then try by SKU
        return $wpdb->get_var($wpdb->prepare("
            SELECT post_id 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_sku' 
            AND REPLACE(LOWER(meta_value), ' ', '') LIKE %s
        ", '%' . $clean_filename . '%'));
    }
    
    /**
     * Get attachment ID for an image
     */
    private function get_attachment_id($image_path) {
        $attachment = get_posts([
            'post_type' => 'attachment',
            'posts_per_page' => 1,
            'post_status' => 'any',
            'orderby' => 'ID',
            'order' => 'DESC',
            'meta_query' => [
                [
                    'key' => '_wp_attached_file',
                    'value' => basename($image_path),
                    'compare' => 'LIKE'
                ]
            ]
        ]);

        return !empty($attachment) ? $attachment[0]->ID : false;
    }
    
    /**
     * Update product image
     */
    private function update_product_image($product_id, $attachment_id) {
        if (!function_exists('update_post_meta')) {
            echo "Error: WordPress functions not available for product update.\n";
            return false;
        }
        
        try {
            // Start transaction
            global $wpdb;
            $wpdb->query('START TRANSACTION');
            
            // Delete existing thumbnail
            delete_post_meta($product_id, '_thumbnail_id');
            
            // Update with new thumbnail
            $result = update_post_meta($product_id, '_thumbnail_id', $attachment_id);
            
            if ($result) {
                $wpdb->query('COMMIT');
                $product = wc_get_product($product_id);
                echo "Updated image for product: " . $product->get_name() . "\n";
                return true;
            } else {
                $wpdb->query('ROLLBACK');
                $this->errors[] = "Failed to update image for product ID: " . $product_id;
                return false;
            }
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            $this->errors[] = "Error updating product ID {$product_id}: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Output processing results
     */
    private function output_results() {
        echo "\nProcessing completed!\n";
        echo "Successfully processed: {$this->processed_count} images\n";
        echo "Skipped: {$this->skipped_count} images\n";
        
        if (!empty($this->duplicate_images)) {
            echo "\nDuplicate images found:\n";
            foreach ($this->duplicate_images as $base_name => $images) {
                echo "- {$base_name}: " . count($images) . " versions\n";
            }
        }
        
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
?>
