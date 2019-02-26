#!/bin/bash
mkdir release
git archive -o ./release/getresponse.zip --prefix=getresponse/ HEAD
(cd ./release && unzip getresponse.zip)
rm ./release/getresponse.zip
rm -rf ./release/getresponse/tests
rm -rf ./release/getresponse/config.xml
rm -rf ./release/getresponse/release.sh
(cd ./release/getresponse && composer install --no-dev)

(cd ./release && git clone git@github.com:dg/php54-arrays.git && cd php54-arrays && php convert.php --reverse ../getresponse)
(cd ./release && git clone git@github.com:jmcollin/autoindex.git && cd autoindex && php index.php ../getresponse)

(cd ./release && zip -r ../getresponse.zip getresponse)
rm -rf release
