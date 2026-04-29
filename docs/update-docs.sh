#!/bin/bash

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_DIR="$SCRIPT_DIR/.."
VERSIONS_FILE="$SCRIPT_DIR/versions/config.json"
VERSIONS_DIR="$SCRIPT_DIR/versions"
WORKTREE_DIR="$(mktemp -d)"

cleanup() {
    git -C "$REPO_DIR" worktree remove --force "$WORKTREE_DIR" 2>/dev/null || true
    rm -rf "$WORKTREE_DIR"
}
trap cleanup EXIT

echo "Reading versions from $VERSIONS_FILE..."

jq -c '.[]' "$VERSIONS_FILE" | while read -r version; do
    branch=$(echo "$version" | jq -r '.branch // empty')
    slug=$(echo "$version" | jq -r '.slug // empty')
    name=$(echo "$version" | jq -r '.name')

    if [ -z "$branch" ]; then
        echo "Skipping version $name (no branch property)..."
        continue
    fi

    echo "Processing version $name (branch: $branch, slug: $slug)..."

    target_dir="$VERSIONS_DIR/$slug"
    rm -rf "$target_dir"
    mkdir -p "$target_dir"

    git -C "$REPO_DIR" fetch origin "$branch"

    rm -rf "$WORKTREE_DIR"
    git -C "$REPO_DIR" worktree add --detach "$WORKTREE_DIR" "origin/$branch"

    cp -r "$WORKTREE_DIR/docs/." "$target_dir/"

    git -C "$REPO_DIR" worktree remove --force "$WORKTREE_DIR"

    echo "  -> Written to $target_dir"
done

echo "Done."
