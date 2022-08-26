#!/bin/sh
#  Run script and pass version: ./woocommerce-release.sh 2.0.1

set -e

#enviroment variables
tmp_dir=tmp
release_name=coingate-for-woocommerce
release_dir_name=woocommerce-coingate

echo "Starting process..."

if [ -d  "${tmp_dir}" ]; then
  rm -rf "${tmp_dir}"
fi

rm -rf ${release_dir_name}-*.zip

mkdir $tmp_dir
rsync -a . ${tmp_dir}/${release_name}

echo "Removing unnecessary files..."

cd ${tmp_dir}/${release_name}
rm -rf .git .github .gitignore .idea vendor .phpcs.xml tmp woocommerce-release.sh
composer install --no-dev
cd ../../

echo "Compressing release folder..."

cd $tmp_dir && zip -r "${release_dir_name}-$1.zip" ${release_name} && cd ..
mv "${tmp_dir}/${release_dir_name}-$1.zip" .
rm -rf $tmp_dir

echo ""
echo "Release folder is completed."
echo ""