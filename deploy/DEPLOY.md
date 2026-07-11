# Deploying to Oracle Cloud (Always Free)

This gets the app running on a real, permanently-free VM that anyone on the
team can reach over the internet. Parts marked **[YOU]** require your own
account/console — I can't click through Oracle's signup for you. Parts
marked **[SERVER]** are commands you run once you're connected to the VM.

## 1. [YOU] Create an Oracle Cloud account

1. Go to oracle.com/cloud/free and sign up. You'll need an email, phone
   number, and a credit card for identity verification — Oracle does not
   charge it as long as you stay within the Always Free limits described
   below.
2. Pick your **Home Region** carefully during signup — this cannot be
   changed later. Pick whichever region is geographically closest to your
   team.
3. Wait for the "your account is ready" email (can take a few minutes to a
   few hours).

## 2. [YOU] Create the VM instance

1. In the Oracle Cloud console, open the hamburger menu → **Compute** →
   **Instances** → **Create Instance**.
2. **Name**: anything, e.g. `ecomm-hub`.
3. **Image and shape** → click **Edit**:
   - Image: **Canonical Ubuntu 24.04** (aarch64 build).
   - Shape: click **Change shape** → **Ampere** → **VM.Standard.A1.Flex**.
     Set **2 OCPUs / 12 GB memory** (leaves headroom in the 4 OCPU / 24 GB
     Always Free allowance if you ever want a second instance). Confirm the
     shape is labeled **"Always Free-eligible"** before continuing.
4. **Networking**: leave the defaults (it auto-creates a VCN with a public
   subnet and assigns a public IPv4 address). Note the public IP once the
   instance finishes provisioning.
5. **Add SSH keys**: choose "Generate a key pair for me" and **download both
   the private and public key files** — you cannot get the private key again
   later. (Or paste your own public key if you already have one.)
6. Click **Create**. Wait for the instance state to become **Running**
   (~1-2 minutes).

## 3. [YOU] Open the firewall at the cloud level

Oracle blocks everything except SSH by default, separately from the VM's own
OS firewall. This step is required or the site will be unreachable even
after `provision.sh` runs.

1. From the instance's detail page, click the **subnet** link under
   "Primary VNIC".
2. Click the **Default Security List** for that subnet.
3. **Add Ingress Rules** → add two rules:
   - Source CIDR `0.0.0.0/0`, IP Protocol TCP, Destination Port `80`
   - Source CIDR `0.0.0.0/0`, IP Protocol TCP, Destination Port `443`
4. Save.

## 4. [YOU] Connect and run the provisioning script

```bash
chmod 600 ~/Downloads/ssh-key-*.key   # the private key you downloaded
ssh -i ~/Downloads/ssh-key-*.key ubuntu@<your-public-ip>
```

Once connected **[SERVER]**:

```bash
git clone https://github.com/liamflores-09/ecomm-team.git /tmp/ecomm-team
cd /tmp/ecomm-team
bash deploy/provision.sh
```

This installs Nginx, PHP 8.3, MySQL, Composer, and Node; clones the real app
to `/var/www/ecomm-team`; builds it; creates the database; and starts
everything. It prints the generated MySQL password once at the end — you
generally won't need it again since it's already saved into the server's
`.env`, but note it down somewhere safe anyway.

When it finishes, visit `http://<your-public-ip>` in a browser. That's the
live app — anyone with that address can reach it, from any device.

## 5. Point a real domain at it (optional but recommended)

An IP address works, but a domain is what you'd actually hand your team.

1. Buy/use a domain, add an **A record** pointing to your VM's public IP.
2. **[SERVER]** Install HTTPS via Let's Encrypt:
   ```bash
   sudo apt-get install -y certbot python3-certbot-nginx
   sudo certbot --nginx -d yourdomain.com
   ```
3. Update `APP_URL` in `/var/www/ecomm-team/.env` to
   `https://yourdomain.com`, then `sudo php artisan config:cache`.

Without a domain, the app still works fine over plain HTTP at the IP — just
know that logins won't be encrypted in transit, which matters if this holds
real staff data.

## 6. Deploying future changes

Whenever you push new commits to `main` on GitHub:

```bash
ssh -i ~/Downloads/ssh-key-*.key ubuntu@<your-public-ip>
bash /var/www/ecomm-team/deploy/update.sh
```

## Notes on the Always Free limits

- The A1.Flex shape above uses 2 of the 4 free OCPUs and 12 of the 24 GB
  free RAM — you have room to spin up a second small instance later if
  needed, at no cost.
- Free tier includes 200 GB total block storage across instances — the
  default 50 GB boot volume is well within that.
- If the instance is ever reclaimed for inactivity (Oracle does this to
  free-tier accounts that show zero usage for a long stretch), logging into
  the console periodically avoids that — an actively-used team tool won't
  trigger it.
