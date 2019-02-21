#!/bin/sh

if [ -d .subsplit ]; then
    git subsplit update
else
    git subsplit init git@github.com:orchestral/auth.git
fi

git subsplit publish --heads="3.8" src/Authorization:git@github.com:orchestral/authorization.git
