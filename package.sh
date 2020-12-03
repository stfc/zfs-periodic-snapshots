#!/bin/bash


version="$(git rev-parse --short HEAD)"

if [[ $# -eq 1 ]]; then
    version="$1"
fi

fpm \
    --input-type dir \
    --output-type rpm \
    --name zfs-periodic-snapshots \
    --version "$version" \
    --epoch "$(date +%s)" \
    --architecture noarch \
    --vendor 'Science and Technology Facilties Council' \
    --url 'https://github.com/stfc/zfs-periodic-snapshots' \
    --description '' \
    ./www/=/var/www/html \
    ./sbin/=/usr/sbin
