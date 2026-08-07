#!/usr/bin/env python3
"""Updates html/images/*.png from the PBIcons repository (source of images/icons).

Each entry is resized per its "resize" geometry (ImageMagick -resize argument,
e.g. "128x128>" to fit within a 128x128 box, "128x>" for a 128px width only),
or copied as-is when "resize" is None.

The SHA-256 hash of each downloaded source is kept in
html/images/<file>.sha256 (version-controlled): if the hash hasn't changed
since the last run, the destination file is left untouched.
"""

import hashlib
import os
import shutil
import subprocess
import sys
import tempfile
import urllib.request
from pathlib import Path

REPO = "ppoilbarbe/PBIcons"
BRANCH = os.environ.get("PBICONS_BRANCH", "main")
# The repository stores its images via Git LFS: raw.githubusercontent.com only
# returns the LFS pointer, media.githubusercontent.com must be used instead.
RAW_BASE = f"https://media.githubusercontent.com/media/{REPO}/{BRANCH}"

SCRIPT_DIR = Path(__file__).resolve().parent
DEST_DIR = SCRIPT_DIR.parent / "html" / "images"

# Destination filename -> source path in PBIcons + resize geometry (None = as-is).
ICONS = {
    "btn-cocktails.png":           {"src": "cardolan/btn-cocktails.png",          "resize": "128x128>"},
    "btn-links.png":               {"src": "cardolan/btn-links.png",              "resize": "128x128>"},
    "btn-mahjong.png":             {"src": "cardolan/btn-mahjong.png",            "resize": "128x128>"},
    "btn-programs.png":            {"src": "cardolan/btn-programs.png",           "resize": "128x128>"},
    "btn-recipes.png":             {"src": "cardolan/btn-recipes.png",            "resize": "128x128>"},
    "home-hobbit.png":             {"src": "cardolan/hobbit-home-button-big.png", "resize": "128x>"},
    "pbcardolan-full.png":         {"src": "cardolan/cardolan.png",               "resize": None},
    "pbcardolan.png":              {"src": "cardolan/cardolan.png",               "resize": "128x128>"},
    "pbicons.png":                 {"src": "programs/pbicons.png",                "resize": "128x128>"},
    "pbpicat.png":                 {"src": "programs/pbpicat-full.png",           "resize": "128x128>"},
    "pbprompt.png":                {"src": "programs/pbprompt-full.png",          "resize": "128x128>"},
    "pbrecipe.png":                {"src": "programs/pbrecipe-128x128.png",       "resize": "128x128>"},
    "pbregisteractivity.png":      {"src": "programs/pbregisteractivity.png",     "resize": "128x128>"},
    "pbrenamer.png":               {"src": "programs/pbrenamer.png",              "resize": "128x128>"},
    "pi.png":                      {"src": "cardolan/pi-small.png",               "resize": None},
    "praetorians-background.jpg":  {"src": "cardolan/praetorians-background.jpg", "resize": None},
    "praetorians-watcher.png":     {"src": "programs/pbregisteractivity.png",     "resize": "512x512>"},
    "Sindarin.jpg":                {"src": "cardolan/Sindarin-21x9.jpg",          "resize": None},
    "the-one-disk.png":            {"src": "cardolan/the-one-disk.png",           "resize": None},
}


def check_dependencies():
    if shutil.which("convert") is None:
        print("Error: 'convert' (ImageMagick) is required but was not found in PATH.", file=sys.stderr)
        sys.exit(1)


def sha256sum(path):
    digest = hashlib.sha256()
    with open(path, "rb") as f:
        for chunk in iter(lambda: f.read(65536), b""):
            digest.update(chunk)
    return digest.hexdigest()


def main():
    check_dependencies()

    updated = 0

    with tempfile.TemporaryDirectory() as tmp:
        for dest_name, icon in ICONS.items():
            src = icon["src"]
            resize = icon["resize"]
            url = f"{RAW_BASE}/{src}"
            tmp_file = Path(tmp) / Path(src).name
            dest_file = DEST_DIR / dest_name
            hash_file = dest_file.with_name(dest_file.name + ".sha256")

            print(f"Downloading {src}…")
            urllib.request.urlretrieve(url, tmp_file)
            new_hash = sha256sum(tmp_file)

            if hash_file.exists() and hash_file.read_text().strip() == new_hash:
                print(f"  = {dest_file} (unchanged)")
                continue

            if resize:
                subprocess.run(["convert", str(tmp_file), "-resize", resize, str(dest_file)], check=True)
            else:
                shutil.copyfile(tmp_file, dest_file)
            hash_file.write_text(new_hash + "\n")
            print(f"  -> {dest_file}")
            updated += 1

    print(f"\n\n{updated} icons updated.")


if __name__ == "__main__":
    main()
