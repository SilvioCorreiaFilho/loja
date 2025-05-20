#!/bin/zsh

echo "📦 Copying files to VPS..."
sshpass -p "Malasf1980M2w" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no preprocess_images.php root@167.86.115.59:/tmp/

echo "📦 Copying images to VPS..."
sshpass -p "Malasf1980M2w" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@167.86.115.59 "rm -rf /tmp/test_images && mkdir -p /tmp/test_images"
sshpass -p "Malasf1980M2w" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no test_images/* root@167.86.115.59:/tmp/test_images/

echo "📦 Processing images in container..."
sshpass -p "Malasf1980M2w" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@167.86.115.59 "docker cp /tmp/preprocess_images.php wordpress-tc48ws4ccowwwc8s0sockooo:/tmp/ && docker cp /tmp/test_images wordpress-tc48ws4ccowwwc8s0sockooo:/tmp/ && docker exec wordpress-tc48ws4ccowwwc8s0sockooo bash -c 'cd /tmp && php preprocess_images.php'"

echo "📦 Copying processed images back..."
rm -rf test_images_processed
mkdir test_images_processed
sshpass -p "Malasf1980M2w" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no "root@167.86.115.59:/tmp/test_images_processed/*" test_images_processed/

echo "✅ Images processed successfully!"
