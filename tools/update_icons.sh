#!/usr/bin/env bash
# Updates html/images/*.png from the PBIcons repository (source of images/icons).
# Each file is resized to 128x128 if it exceeds that size (aspect ratio kept).
#
# The SHA-256 hash of each downloaded source is kept in
# html/images/<file>.sha256 (version-controlled): if the hash hasn't changed
# since the last run, the destination file is left untouched.

set -euo pipefail

REPO="ppoilbarbe/PBIcons"
BRANCH="${PBICONS_BRANCH:-main}"
# The repository stores its images via Git LFS: raw.githubusercontent.com only
# returns the LFS pointer, media.githubusercontent.com must be used instead.
RAW_BASE="https://media.githubusercontent.com/media/$REPO/$BRANCH"
MAX_SIZE="128x128>"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DEST_DIR="$SCRIPT_DIR/../html/images"

declare -A ICONS=(
    ["cardolan/cardolan.png"]="pbcardolan.png"
    ["programs/pbicons.png"]="pbicons.png"
    ["programs/pbpicat-full.png"]="pbpicat.png"
    ["programs/pbprompt-full.png"]="pbprompt.png"
    ["programs/pbrecipe-128x128.png"]="pbrecipe.png"
    ["programs/pbregisteractivity.png"]="pbregisteractivity.png"
    ["programs/pbrenamer.png"]="pbrenamer.png"
)

for cmd in curl convert sha256sum; do
    command -v "$cmd" >/dev/null 2>&1 || {
        echo "Error: '$cmd' is required but was not found in PATH." >&2
        exit 1
    }
done

tmp="$(mktemp -d)"
trap 'rm -rf "$tmp"' EXIT

for src in "${!ICONS[@]}"; do
    dest="${ICONS[$src]}"
    url="$RAW_BASE/$src"
    tmp_file="$tmp/$(basename "$src")"
    dest_file="$DEST_DIR/$dest"
    hash_file="$dest_file.sha256"

    echo "Downloading $src…"
    curl -fsSL "$url" -o "$tmp_file"
    new_hash="$(sha256sum "$tmp_file" | cut -d' ' -f1)"

    if [ -f "$hash_file" ] && [ "$(cat "$hash_file")" = "$new_hash" ]; then
        echo "  = $dest_file (unchanged)"
        continue
    fi

    convert "$tmp_file" -resize "$MAX_SIZE" "$dest_file"
    echo "$new_hash" >"$hash_file"
    echo "  -> $dest_file"
done

echo "Icons updated."
