# Acuparse Docker Installation Guide

**Running Acuparse in Docker is currently in early but stable support.**

**The Acuparse container itself does contain a local database.**
Use `acuparse/mariadb` for best compatibility.

Acuparse is available on [Docker Hub](https://hub.docker.com/r/acuparse/acuparse).

To use set your image to:

- `acuparse/acuparse` (Docker Hub)
- `registry.gitlab.com/acuparse/acuparse` (GitLab Registry)

## Docker Compose

Use Docker Compose to run Acuparse in production. A script is available to set up your host.

## Installation

### Automated Installation

- Download the installer.

    ```bash
    curl -O https://gitlab.com/acuparse/installer/raw/master/install_docker
    ```

- Run the installer
    - On a fresh Ubuntu/Debian system without Docker or Compose

      ```bash
      sudo bash install_docker full | tee ~/acuparse.log
      ```

    - On an existing system with Docker installed but not Compose

     ```bash
     sudo bash install_docker compose | tee ~/acuparse.log
     ```

    - On an existing system with Docker and Compose installed.
        - MacOS users should install Docker and Compose manually,
        then run the command below.

         ```bash
         sudo bash install_docker | tee ~/acuparse.log
         ```

- Add your user to the Docker group

    ```bash
    sudo usermod -a -G docker $USER
    ```

### Manual Installation

**Install Docker and Docker Compose** on your system before continuing.

- [Get Docker](https://docs.docker.com/get-docker/)
- [Get Docker Compose](https://docs.docker.com/compose/install/)

- Download and install the Acuparse compose files to `/opt/acuparse/`.
  
    ```bash
    curl 'https://gitlab.com/acuparse/installer/-/archive/master/installer-master.zip?path=docker' -o acuparse_docker.zip`
    ```
  
**You MUST edit the `acuparse.env` file to set your SQL password before use!**

## Helper Script

A script is installed automatically to help assist with running your containers.
If you installed the Docker Compose files manually, copy the script to `/usr/local/bin`.

```bash
mv acuparse /usr/local/bin
chmod +x /usr/local/bin/acuparse
```

### Starting

```bash
acuparse start
```

### Restarting

```bash
acuparse restart
```

### Stopping

```bash
acuparse stop
```

To **REMOVE ALL DATA** and start over

```bash
acuparse destroy
```

### Updating

Also updates the run script and the docker-compose config.

```bash
sudo acuparse update
```

### Logs

```bash
acuparse logs
```

### Shell/Console

Acuparse

```bash
acuparse console
```

Database

```bash
acuparse dbconsole
```

### Status

```bash
acuparse status
```

## Volumes

You must configure volumes for your SQL database, or it will be lost on container restart.

The default compose file will do this for you.

### Local Bind Volumes

The following volumes are created in `/opt/acuparse/volumes`

- `/opt/acuparse/volumes/webcam`
    - Copy your webcam images to this directory for use in Acuparse.
    - See Webcam details below
- `/opt/acuparse/volumes/backups`
    - Acuparse Config and Database backups.
    - Also, mounted to the Database container; if you need to perform a restore.

## Webcam

When using a webcam with Acuparse, run the webcam scripts on the docker host. Then copy the images to `/opt/acuparse/volumes/webcam`
instead of the default location `/opt/acuparse/src/img/cam`.

The container is not setup to run the webcam scripts directly or be accessed via SSH.

## Backup/Restore

The automated backup script will run once daily and save backups to `/opt/acuparse/volumes/backups`.

### Restore Database

Connect to your `acuparse console` and extract the archive

```bash
tar -xvf /var/opt/acuparse/backups/<BACKUPDATE>.tar.gz
```

Then connect to your `acuparse dbconsole` and restore your database

```bash
mysql -p$MYSQL_ROOT_PASSWORD acuparse < mysql.sql
```

### Restore Config File

Connect to your `acuparse console` and extract the archive located in `tar -xvf /var/opt/acuparse/backups/<BACKUPDATE>.tar.gz`.

Copy your config file back `cp config.php /opt/acuparse/src/usr/config.php`
