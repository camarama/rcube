#!/usr/bin/env bash
rsync -av ./ nb8tr_plasticrcube@plasticrcube.fr:~/plasticrcube --include=public/bundles --include=public/.htaccess --include=vendor --exclude-from=.gitignore --exclude=".*"