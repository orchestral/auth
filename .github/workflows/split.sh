#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="4.x"

function split()
{
    SHA1=`./.github/workflows/splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

git pull origin $CURRENT_BRANCH

split 'src/Authorization' authorization
