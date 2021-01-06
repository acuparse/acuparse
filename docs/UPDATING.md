# Acuparse Update Guide

Detailed upgrade instructions for significant releases will be published in
the [docs/updates](https://docs.acuparse.com/#version-updates) folder, when required.

## Local Install

- Pull the changes from Version Management.

```bash
cd /opt/acuparse
sudo git pull
```

- Navigate to your Acuparse website to complete the update.

## Docker Install

- If you installed using the installer and have the helper scripts available.

```bash
sudo acuparse update
```

- If the helpers are not installed. Pull the image from Docker with compose.

```bash
docker-compose pull
```

- To update just the Acuparse image

```bash
docker pull acuparse/acuparse
```
