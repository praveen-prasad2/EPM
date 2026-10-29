# Connect this repository to Hostinger

Target website: https://slateblue-dugong-266855.hostingersite.com/

The SSH server's ED25519 key was checked against the supplied hPanel screenshot:
`SHA256:jXNgG2TT2cqWuKkHuOICPy2D7QEzTJqgTMSKMswcAWc`.
The matching public host key is pinned in `hostinger-known-hosts`.

## One-time setup

1. In Hostinger's SSH Access page, enable SSH and add the public key from the local `temp/epm-hostinger-deploy.pub` file. Name it `EPM GitHub deployment`.
2. In GitHub, open this repository's Settings → Secrets and variables → Actions → New repository secret.
3. Add `HOSTINGER_SSH_KEY` with the entire contents of the local `temp/epm-hostinger-deploy` private-key file, including the BEGIN/END lines. The private key stays outside Git; enter it directly into the GitHub secret, never a chat or repository file.
4. Confirm the absolute WordPress folder in Hostinger and add it as `HOSTINGER_PATH`. The expected layout is `/home/u316398117/domains/DOMAIN/public_html`; do not assume the domain folder from the temporary URL without checking.
5. Open Actions → Hostinger deployment → Run workflow, choose `master`, and leave the deployment box unchecked. This validates SSH, WordPress, WP-CLI, rsync, page IDs and previews changed files without modifying the website.
6. Confirm the target is the intended copy, that Elementor element IDs/content match the local site, and that you have a current Hostinger backup. Then rerun with the deployment box checked.
7. Clear the Hostinger/page cache and inspect the website.

Only tracked `app/public/` contents are copied. Unrelated files and the database remain in place. Replaced-file backups are stored outside the web root; they do not constitute a complete site backup or include newly added files.

Deployment is manual. A normal Git push will not change the hosted site. Automatic deployment can be enabled after a successful first deployment.

The workflow is prepared but has not yet been run against the server. It requires WP-CLI and rsync on the hosting account; the connection check will report missing prerequisites.
