#!/bin/bash
mkdir release
git archive -o ./release/getresponse.zip --prefix=getresponse/ HEAD
(cd ./release && unzip getresponse.zip)
rm ./release/getresponse.zip
(cd ./release/getresponse && composer install --no-dev)
(cd ./release && zip -r ../getresponse.zip getresponse)
rm -rf release