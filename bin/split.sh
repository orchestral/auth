#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="5.x"

function split()
{
    SHA1=`splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function remote()
{
    git remote add $1 $2 || true
}

git pull origin $CURRENT_BRANCH

remote authorization git@github.com:orchestral/authorization.git

split 'src/Authorization' authorization
