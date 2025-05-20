#!/bin/zsh

echo "Setting up directories..."
# Create directories on VPS
sshpass -p "Malasf1980M2w" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@167.86.115.59 "mkdir -p /tmp/test_images"

echo "Copying PHP script..."
# Copy PHP script to container
sshpass -p "Malasf1980M2w" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no create_test_image.php root@167.86.115.59:/tmp/

echo "Creating test images in container..."
# Execute PHP script in container
sshpass -p "Malasf1980M2w" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@167.86.115.59 "docker cp /tmp/create_test_image.php wordpress-tc48ws4ccowwwc8s0sockooo:/tmp/ && docker exec wordpress-tc48ws4ccowwwc8s0sockooo bash -c 'mkdir -p /tmp/test_images && php /tmp/create_test_image.php' && docker cp wordpress-tc48ws4ccowwwc8s0sockooo:/tmp/test_images/. /tmp/test_images/"

echo "Copying images back to local machine..."
# Copy generated images back to local machine
rm -rf test_images
mkdir -p test_images
sshpass -p "Malasf1980M2w" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no "root@167.86.115.59:/tmp/test_images/*" test_images/

echo "Test images created and copied successfully!"
ls -l test_images/
