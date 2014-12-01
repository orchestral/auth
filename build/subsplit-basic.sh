git subsplit init git@github.com:orchestral/auth.git
git subsplit publish --heads="master" --no-tags src/Authorization:git@github.com:orchestral/authorization.git
rm -rf .subsplit/
