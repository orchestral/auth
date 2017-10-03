#!/bin/sh

if [ -d .subsplit ]; then
    git subsplit update
else
    git subsplit init git@github.com:orchestral/auth.git
fi

git subsplit publish --heads="master 3.5 3.4" --no-tags src/Authorization:git@github.com:orchestral/authorization.git
