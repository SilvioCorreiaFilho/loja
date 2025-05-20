#!/bin/zsh
# Script to update WooCommerce product images

# Set error handling
set -e
trap 'echo "❌ Error occurred. Cleaning up..." && cleanup' ERR

# Enable verbose output
set -x

# Load environment variables
if [ ! -f .env ]; then
    echo "❌ ERROR: .env file not found!"
    exit 1
fi

source .env

# Function to display progress
show_progress() {
    echo "📦 $1"
}

# Function to cleanup remote temporary files
cleanup() {
    if [ -n "$SERVER_IP" ] && [ -n "$SERVER_USER" ]; then
        show_progress "Cleaning up temporary files..."
        sshpass -p "$SERVER_PASSWORD" ssh -o PreferredAuthentications=password -o ConnectTimeout=10 -o StrictHostKeyChecking=no $SERVER_USER@$SERVER_IP "rm -rf /tmp/product_images /tmp/product_image_updater.php" || true
    fi
}

# Print header
echo "WooCommerce Product Image Updater"
echo "================================"

# Test SSH connection first
echo "Testing SSH connection..."
if ! sshpass -p "$SERVER_PASSWORD" ssh -o PreferredAuthentications=password -o ConnectTimeout=10 -o StrictHostKeyChecking=no $SERVER_USER@$SERVER_IP "echo 'Connection successful'" 2>/dev/null; then
    echo "❌ Error: Failed to connect to server $SERVER_IP"
    echo "Testing connection with netcat..."
    nc -zv $SERVER_IP 22 || echo "Port 22 is not reachable"
    exit 1
fi

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
for ext in jpg jpeg png gif; do
    count=$(find "$IMAGE_DIR" -maxdepth 1 -type f -name "*.${ext}" 2>/dev/null | wc -l)
    image_count=$((image_count + count))
done

if [ $image_count -eq 0 ]; then
    echo "❌ Error: No image files found in directory: $IMAGE_DIR"
    echo "Supported formats: jpg, jpeg, png, gif"
    exit 1
fi

# Create temporary directory on server
show_progress "Creating temporary directory..."
if ! sshpass -p "$SERVER_PASSWORD" ssh -o PreferredAuthentications=password -o ConnectTimeout=10 -o StrictHostKeyChecking=no $SERVER_USER@$SERVER_IP "mkdir -p /tmp/product_images"; then
    echo "❌ Error: Failed to create temporary directory on server"
    exit 1
fi

# Upload the PHP script
show_progress "Uploading processor script..."
if ! sshpass -p "$SERVER_PASSWORD" scp -o PreferredAuthentications=password -o ConnectTimeout=10 -o StrictHostKeyChecking=no product_image_updater.php $SERVER_USER@$SERVER_IP:/tmp/; then
    echo "❌ Error: Failed to upload processor script"
    cleanup
    exit 1
fi

# Upload images with progress indicator
show_progress "Uploading product images..."
if ! sshpass -p "$SERVER_PASSWORD" scp -o PreferredAuthentications=password -o ConnectTimeout=10 -o StrictHostKeyChecking=no -r "$IMAGE_DIR"/* $SERVER_USER@$SERVER_IP:/tmp/product_images/ 2>/dev/null; then
    echo "❌ Error: Failed to upload images"
    cleanup
    exit 1
fi

# Execute the script using WP-CLI
show_progress "Processing images..."
if ! sshpass -p "$SERVER_PASSWORD" ssh -o PreferredAuthentications=password -o ConnectTimeout=10 -o StrictHostKeyChecking=no $SERVER_USER@$SERVER_IP "cd $WP_DIR && wp product-image update /tmp/product_images"; then
    echo "❌ Error: Failed to process images"
    cleanup
    exit 1
fi

# Clean up
cleanup

echo "✅ Image update process completed successfully!"
