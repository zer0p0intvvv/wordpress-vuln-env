# Docker Local Cache

This directory holds host-side artifacts that Docker environments must consume
from local disk instead of downloading during `docker build` or container
startup.

## Layout

- `tools/wp-cli.phar` - cached WP-CLI Phar
- `tools/wp` - stable wrapper that executes the cached WP-CLI Phar
- `plugins/*.zip` - cached WordPress plugin archives named as
  `<plugin-slug>.<plugin-version>.zip`
- `lib/cache.sh` - runtime helper sourced by container entrypoints

## Populate Cache

Run:

```bash
bash scripts/prepare-docker-cache.sh
```

The script scans compose files and Docker entrypoints for required plugin
versions, downloads missing artifacts into this directory, and leaves existing
files untouched.

## Contract

- Docker images and entrypoints in this repo must not fetch WP-CLI or WordPress
  plugin zips from the network at build/runtime.
- If a required cache artifact is missing, startup should fail loudly instead
  of silently falling back to online download.
