# Acuparse Docker Installation Guide

**Running Acuparse in Docker is currently in early but stable support.**

**The Acuparse container itself does NOT contain a local database.**
Use `acuparse/mariadb` for best compatibility.

The Acuparse container image is available via Docker Hub (`acuparse/acuparse`) as well as the GitLab Container
Registry (`registry.gitlab.com/acuparse/acuparse`). A docker-compose file and a convenient helper script are available
from the Acuparse repo and are the only supported method of running Acuparse in containers. See below for automatic and
manual install instructions.

The Acuparse image includes the Acuparse application and web server. The Acuparse image does not include a database. The
included docker-compose file will run a Mariadb database image (acuparse/mariadb). If you do not use the compose file,
you'll need to arrange to run a database and give the Acuparse container access to it. The compose file will make
Acuparse available on port 80 and 443 of the host system. A self-signed certificate will be used for https but Let's
Encrypt can be enabled when needed.

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
        - MacOS users should install Docker and Compose manually, then run the command below.

         ```bash
         sudo bash install_docker | tee ~/acuparse.log
         ```

- Add your user to the Docker group

    ```bash
    sudo usermod -a -G docker $USER
    ```

!!! info
    The installer attempts to configure your environment variables automatically. Confirm your configuration
    variables are set properly in `/opt/acuparse/acuparse.env`. Also, ensure your `TZ` variable is set.

### Manual Installation

**Install Docker and Docker Compose** on your system before continuing.

- [Get Docker](https://docs.docker.com/get-docker/)
- [Get Docker Compose](https://docs.docker.com/compose/install/)

- Download and install the Acuparse compose files to `/opt/acuparse/`.
    - Supporting files for Docker Compose can also be found in
      the [Acuparse Installer Repository](https://gitlab.com/acuparse/installer/-/tree/master/docker).

    ```bash
    curl 'https://gitlab.com/acuparse/installer/-/archive/master/installer-master.zip?path=docker' -o acuparse_docker.zip`
    ```

**You MUST edit the `acuparse.env` file to set your SQL password and Timezone before use!**

If you are using a custom environment, ensure at the very least, you set the variables from `acuparse.env` in your
config:

- [Acuparse Environment Template](https://gitlab.com/acuparse/installer/-/blob/master/docker/acuparse.env)

## SSL Certificates

Acuparse includes a snake oil cert in the container that is fine for most uses. If you require a valid certficate, you
can enable Let's Encrypt using environment variables outlined in the template above.

## Ports

The Acuparse APP container will listen on ports 80 and 443 by default. The AcuRite Access needs to send data to port 443
and this cannot be changed. If you have multiple containers and ports in use, suggest running a
[load balancer](https://docs.nginx.com/nginx/admin-guide/load-balancer/http-load-balancer) to route traffic to your
container.

## Helper Script

A script is installed automatically to help assist with running your containers. If you installed the Docker Compose
files manually, copy the script to `/usr/local/bin`.

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

(**MUST BE RUN AS ROOT/SUDO**)

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

### [Local Bind Mounts](https://docs.docker.com/storage/bind-mounts)

The following mounts are created in `/opt/acuparse/volumes`

- `/opt/acuparse/volumes/webcam`
    - Copy your webcam images to this directory for use in Acuparse.
    - See Webcam details below
- `/opt/acuparse/volumes/backups`
    - Acuparse Config and Database backups.
    - Also, mounted to the Database and App containers; if you need to perform a restore.
    - This directory should not be used to store user files; aside from the backup tasks.
        - The backup task will clear all files in this directory.

### [Docker Volumes](https://docs.docker.com/storage/volumes)

- `acuparse_config`
    - Holds your configuration data.
- `acuparse_ssl`
    - Contains any Let's Encrypt Certificates.

## Webcam

When using a webcam with Acuparse, run the webcam scripts on the docker host. Then copy the images
to `/opt/acuparse/volumes/webcam`
instead of the default location `/opt/acuparse/src/img/cam`.

The container is not setup to run the webcam scripts directly or be accessed via SSH.

You can download the webcam templates as a

- [ZIP File](https://gitlab.com/acuparse/acuparse/-/archive/master/acuparse-master.zip?path=cam/templates)
- [TAR.GZ File](https://gitlab.com/acuparse/acuparse/-/archive/master/acuparse-master.tar.gz?path=cam/templates)
- [RAW Git](https://gitlab.com/acuparse/acuparse/-/tree/master/cam/templates)

## Email

For sending outbound email, using [mailgun](https://www.mailgun.com/) is the recommended option.

The Docker image has support for [Nullmailer](https://wiki.debian.org/nullmailer), if you need to use a custom SMTP
server.

Configure Nullmailer in your `acuparse.env` file.

```bash
# Enable SMTP Relay?
SMTP_RELAY=1

# SMTP Smarthost
# GMAIL = smtp.gmail.com smtp --port=587 --auth-login --user=<GMAIL_ADDRESS> --pass=<GMAIL_PASSWORD> --starttls
SMTP_HOST='mail smtp'
```

If you don't have a local SMTP server, you can add one to your docker compose config.

Example using [namshi/smtp](https://hub.docker.com/r/namshi/smtp).

```docker-compose
  mail:
    image: namshi/smtp
    restart: always
```

## Backup/Restore

The automated backup script will run once daily and save backups to `/opt/acuparse/volumes/backups` on your host.

Backup files are kept for 7 days by default and can be changed by modifying `KEEP_BACKUPS_FOR` in your `acuparse.env`
file. You can also disable backups by setting `BACKUPS_ENABLED=0` in `acuparse.env`.

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

Connect to your `acuparse console` and extract the archive located
in `tar -xvf /var/opt/acuparse/backups/<BACKUPDATE>.tar.gz`.

Copy your config file back `cp config.php /opt/acuparse/src/usr/config.php`
