#!/bin/sh

if [ -z "$1" ]; then
    echo "No argument supplied";
    exit 1;
fi

if [ -d .subsplit ]; then
    git subsplit update
else
    git subsplit init git@github.com:orchestral/auth.git
fi

git subsplit publish --heads=$1 --no-tags src/Authorization:git@github.com:orchestral/authorization.git
