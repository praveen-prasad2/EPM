# EPM WordPress presentation updates

Custom EPM must-use plugins, styles, scripts and imagery for the existing WordPress website. This repository is a deployment overlay, not a complete WordPress installation.

## Contents

- `app/public/wp-content/mu-plugins/epm-*`: custom presentation code and image maps.
- `app/public/wp-content/mu-plugins/epm-light-assets/`: supporting images.
- Selected files in `app/public/wp-content/uploads/`: images directly required by the custom presentation layer.

The sitewide update applies white content surfaces, navy typography, responsive section spacing and distinct photographic card backgrounds to 126 published pages. Invixium uses the existing black-wordmark artwork. Saved Elementor content remains unchanged.

## Deploy to an existing Hostinger site

1. Back up the live files and database; test on a matching staging copy first.
2. Confirm that the live site's page IDs and Elementor element IDs match those used by these image maps. Existing WordPress, Elementor, page content and other uploads are prerequisites.
3. Copy the tracked contents of `app/public/` into the live WordPress document root (typically `public_html/`), preserving the directory structure. Merge these files into the existing installation; do not delete unrelated live files.
4. Clear Elementor's generated files/data and page caches, then clear the hosting cache.
5. Check the homepage, Invixium, product cards and mobile navigation.

Pushing to GitHub does not deploy to Hostinger until a separate deployment workflow is configured. Do not point a whole-site replacement deployment at the live WordPress document root using this partial repository.

## Excluded local data

The `.gitignore` uses an explicit deployment allowlist. It excludes WordPress configuration and core, the local database, server configuration, logs, audit snapshots, backups and third-party plugins. No credentials belong in this repository.

## Validation of the local update

- All 126 published pages returned HTTP 200, included the new stylesheet and rendered the expected mapped card counts.
- All 1,206 mapped card image files exist; images are distinct within each page.
- Eight representative pages passed desktop and mobile overflow/card-clipping checks.
- Database checksums verified that saved Elementor content/settings were unchanged.
- Full visual review of all pages remains incomplete.

## Rollback

Restore the previous deployed presentation files and clear caches. To disable only the newest sitewide layer, remove `epm-sitewide.php` from the deployed must-use plugin directory and refresh Elementor's page caches.
