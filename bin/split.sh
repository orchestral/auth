#!/usr/bin/env bash

set -e
set -x

. bin/remote.conf;

function split()
{
    SHA1=`splitsh-lite --prefix=$1`
    git push $2 "$SHA1:refs/heads/$CURRENT_BRANCH" -f
}

CURRENT_BRANCH="4.x"
git pull origin $CURRENT_BRANCH

register_remotes

split 'src/Authorization' authorization

reset_remotes
