#!/bin/bash

# ================================================================================
# Advanced PHP/HAProxy Test Suite - Setup Script
# ================================================================================

set -e  # Exit on error

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║   Advanced PHP/HAProxy Test Suite - Installation Script       ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
INSTALL_DIR="${1:-server-test}"
WEB_ROOT="/var/www/html"
PHP_VERSION=$(php -v 2>/dev/null | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)

# Functions
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   print_warning "This script should not be run as root (for security). Running anyway..."
fi

# Check PHP installation
echo ""
print_info "Checking prerequisites..."

if ! command -v php &> /dev/null; then
    print_error "PHP is not installed. Please install PHP 7.4 or higher."
    exit 1
fi

print_success "PHP $PHP_VERSION detected"

# Check required PHP extensions
REQUIRED_EXTENSIONS=("json" "session" "mbstring")
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -q "^$ext$"; then
        print_success "PHP extension '$ext' found"
    else
        print_warning "PHP extension '$ext' not found (may cause issues)"
    fi
done

# Create directory structure
echo ""
print_info "Creating directory structure..."

mkdir -p "$INSTALL_DIR"/{api,sections,assets/{js,css},includes,logs}
print_success "Directory structure created"

# Create .htaccess for security
echo ""
print_info "Creating security files..."

# Root .htaccess
cat > "$INSTALL_DIR/.htaccess" << 'EOF'
# Enable rewrite engine
RewriteEngine On

# Increase upload limits
php_value upload_max_filesize 100M
php_value post_max_size 100M
php_value max_execution_time 300
php_value memory_limit 512M

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"

# Disable directory listing
Options -Indexes
EOF

# Includes .htaccess (deny direct access)
cat > "$INSTALL_DIR/includes/.htaccess" << 'EOF'
# Deny all direct access to include files
Order Allow,Deny
Deny from all
EOF

# Logs .htaccess
cat > "$INSTALL_DIR/logs/.htaccess" << 'EOF'
# Deny all access to log files
Order Allow,Deny
Deny from all
EOF

print_success "Security files created"

# Create empty log file
touch "$INSTALL_DIR/logs/app.log"
print_success "Log file initialized"

# Create placeholder files
echo ""
print_info "Creating placeholder files..."

cat > "$INSTALL_DIR/index.php" << 'EOF'
<?php
// TODO: Copy content from artifact "Enhanced Server Test Page - index.php"
echo "Please copy the content from the artifacts into this file.";
?>
EOF

cat > "$INSTALL_DIR/sections/server-info-sections.php" << 'EOF'
<?php
// TODO: Copy content from artifact "Server Info Sections"
?>
EOF

cat > "$INSTALL_DIR/api/download.php" << 'EOF'
<?php
// TODO: Copy content from artifact "Download API"
?>
EOF

cat > "$INSTALL_DIR/api/upload.php" << 'EOF'
<?php
// TODO: Copy content from artifact "Upload API"
?>
EOF

cat > "$INSTALL_DIR/api/stress-test.php" << 'EOF'
<?php
// TODO: Copy content from artifact "Stress Test API"
?>
EOF

cat > "$INSTALL_DIR/api/user-agent-test.php" << 'EOF'
<?php
// TODO: Copy content from artifact "User-Agent Test API"
?>
EOF

cat > "$INSTALL_DIR/assets/js/main.js" << 'EOF'
// TODO: Copy content from artifact "Main JavaScript"
EOF

print_success "Placeholder files created"

# Set permissions
echo ""
print_info "Setting permissions..."

chmod -R 755 "$INSTALL_DIR"
chmod -R 777 "$INSTALL_DIR/logs"
print_success "Permissions set"

# Detect web server
echo ""
print_info "Detecting web server..."

if systemctl is-active --quiet apache2 || systemctl is-active --quiet httpd; then
    WEB_SERVER="apache"
    print_success "Apache detected"
elif systemctl is-active --quiet nginx; then
    WEB_SERVER="nginx"
    print_success "Nginx detected"
else
    WEB_SERVER="unknown"
    print_warning "Web server not detected"
fi

# Summary
echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                    Installation Complete!                      ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""
print_info "Installation directory: $(pwd)/$INSTALL_DIR"
echo ""

# Next steps
echo "📋 NEXT STEPS:"
echo ""
echo "1. Copy artifact contents into the placeholder files:"
echo "   - index.php (from artifact 'Enhanced Server Test Page')"
echo "   - sections/server-info-sections.php (from artifact 'Server Info Sections')"
echo "   - assets/js/main.js (from artifact 'Main JavaScript')"
echo "   - api/download.php (from artifact 'Download API')"
echo "   - api/upload.php (from artifact 'Upload API')"
echo "   - api/stress-test.php (from artifact 'Stress Test API')"
echo "   - api/user-agent-test.php (from artifact 'User-Agent Test API')"
echo "   - includes/config.php (from artifact 'Configuration File')"
echo "   - includes/helpers.php (from artifact 'Helper Functions')"
echo ""
echo "2. Verify PHP configuration in php.ini:"
echo "   - upload_max_filesize = 100M"
echo "   - post_max_size = 100M"
echo "   - max_execution_time = 300"
echo "   - memory_limit = 512M"
echo ""

if [[ "$WEB_SERVER" == "apache" ]]; then
    echo "3. Restart Apache:"
    echo "   sudo systemctl restart apache2"
    echo ""
    echo "4. Access the application:"
    echo "   http://localhost/$INSTALL_DIR/"
elif [[ "$WEB_SERVER" == "nginx" ]]; then
    echo "3. Restart Nginx and PHP-FPM:"
    echo "   sudo systemctl restart nginx"
    echo "   sudo systemctl restart php${PHP_VERSION}-fpm"
    echo ""
    echo "4. Access the application:"
    echo "   http://localhost/$INSTALL_DIR/"
else
    echo "3. Start your web server and access:"
    echo "   http://localhost/$INSTALL_DIR/"
fi

echo ""
print_success "Setup script completed!"
echo ""

# Optional: Create a quick file copier helper
cat > "$INSTALL_DIR/COPY_FILES_HERE.txt" << 'EOF'
================================================================================
FILE COPY GUIDE
================================================================================

You have 11 artifacts to copy. Here's the mapping:

Artifact #1: "Enhanced Server Test Page" 
   → Copy to: index.php

Artifact #2: "Server Info Sections"
   → Copy to: sections/server-info-sections.php

Artifact #3: "Main JavaScript"
   → Copy to: assets/js/main.js

Artifact #4: "Download API"
   → Copy to: api/download.php

Artifact #5: "Upload API"
   → Copy to: api/upload.php

Artifact #6: "Stress Test API"
   → Copy to: api/stress-test.php

Artifact #7: "User-Agent Test API"
   → Copy to: api/user-agent-test.php

Artifact #8: "README"
   → Copy to: README.md

Artifact #9: "Configuration File"
   → Copy to: includes/config.php

Artifact #10: "Helper Functions"
   → Copy to: includes/helpers.php

After copying all files, delete this file and refresh your browser!
================================================================================
EOF

print_info "Created COPY_FILES_HERE.txt with instructions"