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
  curl -L "https://github.com/docker/compose/releases/download/{COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o 
  /usr/local/bin/docker-compose
  chmod +x /usr/local/bin/docker-compose
  docker-compose -f /opt/acuparse/docker-compose.yml up -d
```
