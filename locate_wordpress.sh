#!/bin/zsh
# Script to locate WordPress installation on the server
# Compatible with Mac zsh

echo "WordPress Installation Locator"
echo "===================================="

# Server details
SERVER="167.86.115.59"
USER="root"
PASSWORD="Malasf1980M2w"

echo "Connecting to server to locate WordPress installation..."

# Create a temporary script to run on the server
cat > temp_locate.sh << 'EOL'
#!/bin/bash
# Script to locate WordPress installation

echo "Checking common WordPress installation directories..."

# List of common WordPress installation directories
DIRS=(
  "/var/www/html"
  "/var/www"
  "/usr/share/nginx/html"
  "/usr/share/nginx/www"
  "/usr/share/apache2/default-site/htdocs"
  "/usr/share/apache2/htdocs"
  "/home/*/public_html"
  "/srv/www"
  "/srv/www/html"
  "/srv/www/htdocs"
)

# Function to check if a directory contains WordPress
check_wp_dir() {
  local dir=$1
  if [ -d "$dir" ]; then
    echo "Checking $dir..."
    if [ -f "$dir/wp-config.php" ]; then
      echo "✅ WordPress installation found in $dir"
      return 0
    elif [ -f "$dir/index.php" ] && grep -q "WordPress" "$dir/index.php" 2>/dev/null; then
      echo "✅ WordPress installation likely found in $dir"
      return 0
    fi
  fi
  return 1
}

# Check each directory
WP_FOUND=false
for dir in "${DIRS[@]}"; do
  # Handle directories with wildcards
  if [[ $dir == *"*"* ]]; then
    for expanded_dir in $(eval echo $dir); do
      if check_wp_dir "$expanded_dir"; then
        WP_FOUND=true
        echo "WordPress directory: $expanded_dir"
      fi
    done
  else
    if check_wp_dir "$dir"; then
      WP_FOUND=true
      echo "WordPress directory: $dir"
    fi
  fi
done

# If not found in common directories, search more broadly
if [ "$WP_FOUND" = false ]; then
  echo "WordPress not found in common directories. Searching more broadly..."
  echo "This may take some time..."
  
  # Find wp-config.php files
  echo "Searching for wp-config.php files..."
  find / -name wp-config.php -type f 2>/dev/null | while read -r config_file; do
    wp_dir=$(dirname "$config_file")
    echo "✅ WordPress installation found in $wp_dir"
    WP_FOUND=true
  done
fi

# Check if WordPress is installed via Docker
if [ "$WP_FOUND" = false ]; then
  echo "Checking for Docker installations..."
  if command -v docker &> /dev/null; then
    echo "Docker is installed. Checking for WordPress containers..."
    docker ps | grep -i wordpress
    if [ $? -eq 0 ]; then
      echo "WordPress Docker containers found. You may need to access WordPress through Docker."
      WP_FOUND=true
    fi
  fi
fi

# Check for running web servers
echo "Checking for running web servers..."
ps aux | grep -E 'apache|nginx|httpd' | grep -v grep

# Check for website configuration files
echo "Checking for website configuration files..."
if [ -d "/etc/apache2/sites-enabled" ]; then
  echo "Apache sites enabled:"
  ls -la /etc/apache2/sites-enabled/
  echo "Content of configuration files:"
  grep -r "DocumentRoot" /etc/apache2/sites-enabled/
fi

if [ -d "/etc/nginx/sites-enabled" ]; then
  echo "Nginx sites enabled:"
  ls -la /etc/nginx/sites-enabled/
  echo "Content of configuration files:"
  grep -r "root" /etc/nginx/sites-enabled/
fi

# If still not found
if [ "$WP_FOUND" = false ]; then
  echo "❌ WordPress installation not found."
  echo "Please check the server configuration manually."
fi
EOL

# Make the script executable
chmod +x temp_locate.sh

# Use sshpass to connect and run the script
# First check if sshpass is installed
if ! command -v sshpass &> /dev/null; then
    echo "❌ sshpass is not installed. Please install it with: brew install sshpass"
    echo "You may need to run: brew tap esolitos/ipa"
    echo "Then: brew install sshpass"
    exit 1
fi

# Upload and execute the script
sshpass -p "$PASSWORD" scp temp_locate.sh $USER@$SERVER:/tmp/
sshpass -p "$PASSWORD" ssh $USER@$SERVER "bash /tmp/temp_locate.sh"

# Clean up
rm temp_locate.sh

echo "✅ WordPress location search completed!"
