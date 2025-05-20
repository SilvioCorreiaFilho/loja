#!/bin/zsh
# Script to update WooCommerce product images

# Load environment variables from .env file if it exists
if [ -f .env ]; then
    source .env
else
    echo "❌ Error: .env file not found"
    echo "Please create a .env file with VPS_PASSWORD and VPS_HOST variables"
    exit 1
fi

# Set error handling
set -e
trap 'echo "❌ Error occurred. Cleaning up..." && cleanup' ERR

# Function to display progress
show_progress() {
    echo "📦 $1"
}

# Function to cleanup remote temporary files
cleanup() {
    show_progress "Cleaning up temporary files..."
    sshpass -p "$VPS_PASSWORD" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@$VPS_HOST "
        docker exec wordpress-tc48ws4ccowwwc8s0sockooo rm -rf /tmp/product_images /tmp/product_image_updater.php
        rm -rf /tmp/images
    " || true
}

# Function to backup existing images
backup_images() {
    local timestamp=$(date +%Y%m%d_%H%M%S)
    show_progress "Creating backup of existing images..."
    sshpass -p "$VPS_PASSWORD" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@$VPS_HOST "
        docker exec wordpress-tc48ws4ccowwwc8s0sockooo bash -c '
            cd /var/www/html
            if [ ! -d wp-content/uploads/wc-backup ]; then
                mkdir -p wp-content/uploads/wc-backup
            fi
            tar -czf wp-content/uploads/wc-backup/products_${timestamp}.tar.gz wp-content/uploads/[0-9]*
        '
    "
}

# Print header
echo "WooCommerce Product Image Updater"
echo "================================"

# Check if image directory is provided
if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <image_directory>"
    exit 1
fi

IMAGE_DIR="$1"

# Verify image directory exists
if [ ! -d "$IMAGE_DIR" ]; then
    echo "❌ Error: Directory not found: $IMAGE_DIR"
    exit 1
fi

# Check if directory contains any image files
image_count=0
for ext in jpg jpeg png; do
    count=$(find "$IMAGE_DIR" -maxdepth 1 -type f -iname "*.${ext}" 2>/dev/null | wc -l)
    image_count=$((image_count + count))
done

if [ $image_count -eq 0 ]; then
    echo "❌ Error: No image files found in directory: $IMAGE_DIR"
    echo "Supported formats: jpg, jpeg, png"
    exit 1
fi

# Process images locally first
show_progress "Processing images locally..."
php preprocess_images.php "$IMAGE_DIR"

if [ ! -d "processed_images" ]; then
    echo "❌ Error: Image processing failed"
    exit 1
fi

# Create temporary directories in container and VPS
show_progress "Creating temporary directories..."
sshpass -p "$VPS_PASSWORD" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@$VPS_HOST "
    mkdir -p /tmp/images
    docker exec wordpress-tc48ws4ccowwwc8s0sockooo mkdir -p /tmp/product_images
"

# Backup existing images
backup_images

# Copy files to VPS
show_progress "Copying files to VPS..."
SCRIPT_DIR=$(pwd)
sshpass -p "$VPS_PASSWORD" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no "$SCRIPT_DIR/product_image_updater.php" root@$VPS_HOST:/tmp/

show_progress "Copying processed images to VPS..."
sshpass -p "$VPS_PASSWORD" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no processed_images/* root@$VPS_HOST:/tmp/images/

# Upload files to container
show_progress "Uploading files to container..."
sshpass -p "$VPS_PASSWORD" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@$VPS_HOST "
    docker cp /tmp/product_image_updater.php wordpress-tc48ws4ccowwwc8s0sockooo:/tmp/
    docker cp /tmp/images/. wordpress-tc48ws4ccowwwc8s0sockooo:/tmp/product_images/
"

# Process images using WP-CLI
show_progress "Importing images..."
sshpass -p "$VPS_PASSWORD" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@$VPS_HOST "
    docker exec wordpress-tc48ws4ccowwwc8s0sockooo bash -c '
        cd /var/www/html
        for img in /tmp/product_images/*; do
            if [ -f \"\$img\" ]; then
                echo \"Importing \$(basename \"\$img\")...\"
                wp media import \"\$img\" --allow-root || echo \"Failed to import \$(basename \"\$img\")\"
            fi
        done
    '
"

# Associate images with products
show_progress "Associating images with products..."
sshpass -p "$VPS_PASSWORD" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@$VPS_HOST "
    docker exec wordpress-tc48ws4ccowwwc8s0sockooo bash -c '
        cd /var/www/html
        php /tmp/product_image_updater.php /tmp/product_images
    '
"

# Clean up
cleanup

# Clean up local processed images
rm -rf processed_images

echo "✅ Image update process completed successfully!"
echo "A backup of the original images was created on the server."
