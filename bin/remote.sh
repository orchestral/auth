#!/usr/bin/env bash

set -e
set -x

CURRENT_BRANCH="4.x"

function remote()
{
    git remote add $1 $2 || true
}

remote authorization git@github.com:orchestral/authorization.git
