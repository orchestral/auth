#!/usr/bin/env bash

set -e

CURRENT_BRANCH="5.x"
COMPONENTS=("authorization")

. bin/remote.conf;

if (( "$#" != 1 ))
then
    echo "Tag has to be provided"

    exit 1
fi

VERSION=$1

# Always prepend with "v"
if [[ $VERSION != v*  ]]
then
    VERSION="v$VERSION"
fi

# Tag Component
git tag $VERSION
git push origin --tags

register_remotes()

# Tag Components
for REMOTE in "${COMPONENTS[@]}"
do
    echo ""
    echo ""
    echo "Releasing $REMOTE";

    TMP_DIR="/tmp/orchestra-auth-split"
    REMOTE_URL="git@github.com:orchestral/$REMOTE.git"

    rm -rf $TMP_DIR;
    mkdir $TMP_DIR;

    (
        cd $TMP_DIR;

        git clone $REMOTE_URL .
        git checkout "$CURRENT_BRANCH";

        git tag $VERSION
        git push origin --tags
    )
done

reset_remotes
