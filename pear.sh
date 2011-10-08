#!/bin/sh
rm -f StreamWrapper*.tgz
mkdir -p TheSeer/StreamWrapper
cp -r src/* TheSeer/StreamWrapper
phpab -o TheSeer/StreamWrapper/autoload.php -b src src
pear package
rm -rf TheSeer
