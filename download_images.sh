#!/bin/zsh

# Create test_images directory if it doesn't exist
rm -rf test_images
mkdir -p test_images

# Download selected product images with white backgrounds
curl -o "test_images/rice-probiotic.png" "https://ae-pic-a1.aliexpress-media.com/kf/S5da8489d560243f594b1616e5e39cc5c4.png"
curl -o "test_images/centella-serum.jpg" "https://ae-pic-a1.aliexpress-media.com/kf/Sd3bb3f826b174b33aaef4f33c464e759e.jpg"
curl -o "test_images/collagen-mask.jpg" "https://ae-pic-a1.aliexpress-media.com/kf/S85c3abf5876e47819a2709d1b65b9fcfW.jpg"

echo "Downloaded test images successfully!"
