#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="master"

function split()
{
    SHA1=`splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

function add_remote()
{
    git remote add $1 $2 || true
}

function remove_remote()
{
    git remote remove $1 || true
}

git pull origin $CURRENT_BRANCH

add_remote authorization git@github.com:orchestral/authorization.git

split 'src/Authorization' authorization

remove_remote authorization
