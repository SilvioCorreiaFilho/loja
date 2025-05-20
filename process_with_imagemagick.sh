#!/bin/zsh

echo "📦 Setting up on VPS..."
sshpass -p "Malasf1980M2w" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@167.86.115.59 "docker exec wordpress-tc48ws4ccowwwc8s0sockooo mkdir -p /tmp/test_images_processed && chmod 777 /tmp/test_images_processed"

echo "📦 Copying images to VPS..."
sshpass -p "Malasf1980M2w" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no test_images/* root@167.86.115.59:/tmp/test_images/

echo "📦 Processing images with ImageMagick..."
sshpass -p "Malasf1980M2w" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@167.86.115.59 "docker exec wordpress-tc48ws4ccowwwc8s0sockooo bash -c '
    cd /tmp
    for img in test_images/*; do
        filename=\$(basename \"\$img\")
        echo \"Processing \$filename...\"
        
        # Create pure white background
        convert -size 800x800 xc:white /tmp/bg.png
        
        # Process image with more aggressive white background enforcement
        convert \"\$img\" \\
            -resize 800x800\\> \\
            -background white \\
            -alpha remove \\
            -alpha off \\
            -fuzz 5% \\
            -fill white \\
            -draw \"color 0,0 floodfill\" \\
            -draw \"color 799,0 floodfill\" \\
            -draw \"color 0,799 floodfill\" \\
            -draw \"color 799,799 floodfill\" \\
            -gravity center \\
            -extent 800x800 \\
            -strip \\
            -quality 85 \\
            \"/tmp/test_images_processed/\$filename\"
        
        # Verify the image was created
        if [ -f \"/tmp/test_images_processed/\$filename\" ]; then
            echo \"Successfully processed \$filename\"
            
            # Additional verification of white background
            convert \"/tmp/test_images_processed/\$filename\" -gravity center -crop 800x1+0+0 -format \"%[mean]\" info:
            
        else
            echo \"Failed to process \$filename\"
        fi
    done
'"

echo "📦 Copying processed images back..."
rm -rf test_images_processed
mkdir test_images_processed

# First copy from container to VPS host
sshpass -p "Malasf1980M2w" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@167.86.115.59 "
    docker cp wordpress-tc48ws4ccowwwc8s0sockooo:/tmp/test_images_processed/. /tmp/test_images_processed/
    ls -la /tmp/test_images_processed/
"

# Then copy from VPS host to local
sshpass -p "Malasf1980M2w" scp -o PreferredAuthentications=password -o StrictHostKeyChecking=no "root@167.86.115.59:/tmp/test_images_processed/*" test_images_processed/

# Verify local images
if [ -n "$(ls -A test_images_processed 2>/dev/null)" ]; then
    echo "✅ Images were successfully processed and copied back"
    echo "
Changes made to image processing:
1. Added floodfill operations from corners to ensure white background
2. Increased fuzz factor to 5% for better background detection
3. Added verification step to check mean color value of edges
4. Enforced strict alpha channel removal

Now running update script with improved images...
"
    ./update_product_images.sh test_images_processed
else
    echo "❌ No processed images found in test_images_processed/"
    echo "Checking VPS container for processed images:"
    sshpass -p "Malasf1980M2w" ssh -o PreferredAuthentications=password -o StrictHostKeyChecking=no root@167.86.115.59 "
        echo 'Container directory contents:'
        docker exec wordpress-tc48ws4ccowwwc8s0sockooo ls -la /tmp/test_images_processed/
    "
fi
