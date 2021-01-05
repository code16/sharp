#!/usr/bin/env sh

printf "\e[1mSharp release\e[0m\n"

#MODIFIED_FILES=$(git diff --cached --numstat | wc -l | xargs)
#
#if [ "$MODIFIED_FILES" != "0" ]; then
#  printf '\e[33mSome files are not committed 👇\e[0m\n\n----------\n\n'
#  git status
#  exit
#fi

node $(dirname $0)/publish.js
