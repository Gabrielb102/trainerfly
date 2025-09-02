#!/bin/bash

# Trainerfly WordPress Plugin Build Script
# This script builds the frontend assets and packages the plugin for WordPress deployment

set -e  # Exit on any error

#region Check for required dependencies

# PHP-side dependencies
if [ ! -d "vendor" ]; then
    print_error "vendor directory not found. Please run 'composer install' first."
    exit 1
fi

# Frontend Dependencies
if [ ! -d "frontend/node_modules" ]; then 
    print_error "frontend/node_modules directory not found. Please run 'npm install' first."
    exit 1
fi

#endregion

echo "Building Trainerfly WordPress Plugin..."

#region Output

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions to print colored output
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}
#endregion

# Clean up any previous build
if [ -d "build" ]; then
    print_status "Cleaning previous build..."
    rm -rf build/
fi

VERSION=$(node -p "require('./frontend/package.json').version")
ZIP_NAME="trainerfly-plugin-v${VERSION}.zip"

# Create build directory
print_status "Creating build directory..."
mkdir -p build/trainerfly/frontend

# Build frontend assets
print_status "Building frontend assets..."
cd frontend
if ! npm run build; then
    print_error "Frontend build failed!"
    exit 1
fi
cd ..

# Copy plugin files to build directory
print_status "Copying plugin files..."
cp -r includes/ build/trainerfly/includes/
cp -r src/ build/trainerfly/src/
cp -r vendor/ build/trainerfly/vendor/
cp -r frontend/dist/ build/trainerfly/frontend/dist/
cp trainerfly.php build/trainerfly/

# Create zip file
print_status "Creating plugin zip file..."
cd build
zip -r "$ZIP_NAME" trainerfly/ > /dev/null
cd ..

# Get file size
ZIP_SIZE=$(du -h trainerfly-plugin.zip | cut -f1)

print_status "Plugin built successfully!"
echo ""
echo "📦 Plugin package: trainerfly-plugin.zip"
echo "📏 Size: $ZIP_SIZE"
echo ""
echo "You can now upload trainerfly-plugin.zip to your WordPress site."
