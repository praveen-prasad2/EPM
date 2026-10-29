#!/usr/bin/env bash
set -euo pipefail

: "${HOSTINGER_SSH_KEY:?Add the HOSTINGER_SSH_KEY repository secret}"
: "${HOSTINGER_PATH:?Add the confirmed HOSTINGER_PATH repository secret}"
: "${RUNNER_TEMP:?Run this script through GitHub Actions}"

# Restrict interpolation to an absolute WordPress document root for this account.
if [[ ! "$HOSTINGER_PATH" =~ ^/home/u316398117/domains/[a-zA-Z0-9.-]+/public_html$ ]]; then
  echo 'HOSTINGER_PATH must be the confirmed /home/u316398117/domains/DOMAIN/public_html path.' >&2
  exit 1
fi

ssh_dir=$(mktemp -d "$RUNNER_TEMP/epm-ssh.XXXXXX")
trap 'rm -rf -- "$ssh_dir"' EXIT
chmod 700 "$ssh_dir"
printf '%s\n' "$HOSTINGER_SSH_KEY" | tr -d '\r' > "$ssh_dir/key"
chmod 600 "$ssh_dir/key"
unset HOSTINGER_SSH_KEY
if ! ssh-keygen -y -P '' -f "$ssh_dir/key" > /dev/null 2>&1; then
  echo '::error::HOSTINGER_SSH_KEY is not a readable, unencrypted private SSH key. Replace the GitHub secret with the complete contents of temp/epm-hostinger-deploy, including both BEGIN/END lines. Do not use the .pub file, a file path, or a password.'
  exit 1
fi
cp deploy/hostinger-known-hosts "$ssh_dir/known_hosts"
cat > "$ssh_dir/config" <<EOF
Host epm-hostinger
  HostName 82.25.125.20
  Port 65002
  User u316398117
  IdentityFile $ssh_dir/key
  IdentitiesOnly yes
  BatchMode yes
  StrictHostKeyChecking yes
  UserKnownHostsFile $ssh_dir/known_hosts
  ConnectTimeout 20
EOF
export RSYNC_RSH="ssh -F $ssh_dir/config"
ssh_command=(ssh -F "$ssh_dir/config" epm-hostinger)

# Complete the read-only checks before transferring any website files.
"${ssh_command[@]}" "set -eu; test -d '$HOSTINGER_PATH'; test -f '$HOSTINGER_PATH/wp-load.php'; test -f '$HOSTINGER_PATH/wp-config.php'; test -d '$HOSTINGER_PATH/wp-admin'; test -w '$HOSTINGER_PATH/wp-content'; command -v rsync; command -v wp; cd '$HOSTINGER_PATH'; wp core is-installed; wp option get home"
"${ssh_command[@]}" "cd '$HOSTINGER_PATH' && wp post list --post_type=page --post_status=publish --fields=ID --format=json" > "$ssh_dir/pages.json"
node - "$ssh_dir/pages.json" <<'NODE'
const fs = require('fs');
const ids = new Set(JSON.parse(fs.readFileSync(process.argv[2])).map(p => String(p.ID)));
const map = JSON.parse(fs.readFileSync('app/public/wp-content/mu-plugins/epm-sitewide-images.json'));
const missing = Object.keys(map).filter(id => !ids.has(id));
if (missing.length) throw new Error('Live page IDs do not match the local site: '+missing.join(', '));
console.log('All mapped page IDs exist on the target.');
NODE

rsync -rcn --itemize-changes app/public/ "epm-hostinger:$HOSTINGER_PATH/"
if [[ "${DEPLOY:-false}" != true ]]; then
  echo 'Connection and file preview completed. No website files were changed.'
  exit 0
fi

# Backups stay outside public_html. No --delete: unrelated site files are retained.
backup="/home/u316398117/epm-deploy-backups/${GITHUB_RUN_ID:?}-${GITHUB_RUN_ATTEMPT:?}"
"${ssh_command[@]}" "umask 077; mkdir -p '$backup'"
rsync -rc --chmod=D755,F644 --backup --backup-dir="$backup" --itemize-changes app/public/ "epm-hostinger:$HOSTINGER_PATH/"
"${ssh_command[@]}" "cd '$HOSTINGER_PATH' && wp eval 'foreach (get_posts([\"post_type\"=>\"page\",\"post_status\"=>\"publish\",\"numberposts\"=>-1,\"fields\"=>\"ids\"]) as \$id) { delete_post_meta(\$id, \"_elementor_element_cache\"); clean_post_cache(\$id); }' && wp cache flush"
echo "Files deployed. Replaced-file backups: $backup"
echo 'Clear the Hostinger/page cache and review the site. Newly added files are not included in replaced-file backups.'
