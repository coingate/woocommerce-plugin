#!/bin/sh
#  Run script and pass version: ./woocommerce-release.sh 2.0.1

set -e

# Environment variables
tmp_dir=tmp
release_name=coingate-for-woocommerce
release_dir_name=woocommerce-coingate
version=$1

# Validate version argument
if [ -z "$version" ]; then
    echo "Error: Version number is required"
    echo "Usage: ./woocommerce-release.sh <version>"
    exit 1
fi

# Validate version format
if ! echo "$version" | grep -qE '^[0-9]+\.[0-9]+\.[0-9]+$'; then
    echo "Error: Invalid version format. Use semantic versioning (e.g., 2.0.1)"
    exit 1
fi

echo "Starting release process for version $version..."

# Clean up existing temporary directory
if [ -d "${tmp_dir}" ]; then
    rm -rf "${tmp_dir}"
fi

# Clean up existing release files
rm -rf ${release_dir_name}-*.zip

# Create temporary directory
mkdir -p $tmp_dir
rsync -a . ${tmp_dir}/${release_name}

echo "Removing unnecessary files..."

# Remove development and unnecessary files
cd ${tmp_dir}/${release_name}
rm -rf .git .github .gitignore .idea vendor .phpcs.xml tmp woocommerce-release.sh
rm -rf node_modules package-lock.json yarn.lock webpack.config.js
rm -rf tests phpunit.xml.dist
rm -rf .editorconfig .eslintrc .prettierrc
rm -rf src/ # Remove source files after build
rm -rf build/ # Remove build files as they will be regenerated

# Install production dependencies
echo "Installing production dependencies..."
composer install --no-dev --optimize-autoloader

# Build assets
echo "Building assets..."
npm install
npm run build

# Clean up build dependencies
rm -rf node_modules package-lock.json

cd ../../

echo "Compressing release folder..."

# Create release zip
cd $tmp_dir && zip -r "${release_dir_name}-${version}.zip" ${release_name} && cd ..
mv "${tmp_dir}/${release_dir_name}-${version}.zip" .

# Clean up
rm -rf $tmp_dir

echo ""
echo "Release package ${release_dir_name}-${version}.zip is ready for submission."
echo "Please verify the package contents before uploading to WordPress marketplace."
echo ""