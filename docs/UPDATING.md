# Acuparse Update Guide

Detailed upgrade instructions for significant releases will be published in the [docs/updates](https://docs.acuparse.com/#version-updates) folder,
when required.

## UPDATING TO VERSION 3

See the [Version 3 Update Guide](https://docs.acuparse.com/updates/v3) for detailed instructions.

## Local Install

- Pull the changes from Version Management.

```bash
cd /opt/acuparse
sudo git pull
```

- Navigate to your Acuparse website to complete the update.

## Docker Install

The Acuparse CI system rebuilds the latest images weekly to ensure the images include upstream updates.

- If you installed using the installer and have the helper scripts available.

```bash
sudo acuparse update
```

- If the helpers are not installed. Pull the image from Docker with compose.

```bash
docker-compose pull
docker-compose -f /opt/acuparse/docker-compose.yml up -d
```

- To update just the Acuparse image

```bash
docker pull acuparse/acuparse
docker-compose -f /opt/acuparse/docker-compose.yml up -d
```

### Update Docker Compose

- If you installed using the installer and have the helper scripts available.

```bash
sudo acuparse update
sudo acuparse update_compose
```

- If the helpers are not installed. Update Compose manually

```bash
curl -L "https://github.com/docker/compose/releases/download/{COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
docker-compose -f /opt/acuparse/docker-compose.yml up -d
```

## Update Debian Buster to Bullseye

### Bare Installer

- A script is available to update your OS from Debian Buster to Bullseye.

    ```bash
    curl -O https://gitlab.com/acuparse/installer/-/raw/master/resources/bullseye && sudo bash bullseye | tee ~/bullseye.log
    ```
  
    - The installer will also attempt to update your Apache config to include support for TLS1.1 Check your
      `/etc/apache2/sites-available/acuparse-ssl.conf` and ensure you have the following after `SSLEngine on`
        - Run `systemctl restart apache2` if you need to make changes.

        ```text
        SSLProtocol all +TLSv1.1 -SSLv3
        SSLCipherSuite ALL:@SECLEVEL=1
        ```

### Docker Installer

- Docker installer users can update the base OS by running

    ```bash
    sed -i 's/buster/bullseye/g' /etc/apt/sources.list
    sed -i 's#deb http://security.debian.org/ bullseye/updates#deb https://security.debian.org/debian-security bullseye-security#g' /etc/apt/sources.list
    sed -i 's#deb-src http://security.debian.org/ bullseye/updates#deb-src https://security.debian.org/debian-security bullseye-security#g' /etc/apt/sources.list
    apt update
    apt dist-upgrade
    ```

- Rasbian users should also update Docker Compose
    - `pip3 install docker-compose`
