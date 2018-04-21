#!/usr/bin/env bash
TARGET_DIR=${PWD%/*}/mobile
if [ -d "$TARGET_DIR" ]; then
  echo "$TARGET_DIR exists. First remove it and then run this script."
  exit 1
fi
mv $PWD $TARGET_DIR
cd $TARGET_DIR

HOOK_NAMES="applypatch-msg pre-applypatch post-applypatch pre-commit prepare-commit-msg commit-msg post-commit pre-rebase post-checkout post-merge pre-receive update post-receive post-update pre-auto-gc pre-push"
# find gits native hooks folder
HOOKS_DIR=$(git rev-parse --show-toplevel)/.git/hooks
cp -f hooks/* $HOOKS_DIR
for hook in $HOOK_NAMES; do
    # If the hook already exists, is a file, and is not a symlink
    if [ ! -h $HOOKS_DIR/$hook ] && [ -f $HOOKS_DIR/$hook ]; then
        chmod +x $HOOKS_DIR/$hook
  	fi
done
composer up

echo "Directory renamed. cd to $TARGET_DIR before working on it. And exit any other programs in the previous cd."
