#!/bin/bash

#./vendor/bin/phpunit

#if [ $? -ne 0 ]; then
#    echo 'PHP Unit Failed'
#    exit
#else
#    echo 'PHP Unit Passed'
#fi

cb=$(git symbolic-ref --short HEAD)

git pull origin live

npm run prod

git add public/js/app.js
git add public/css/app.css
git add public/mix-manifest.json
git commit -m "auto commit public files"

git checkout live
git merge $cb
git push origin live
git checkout $cb
git status
