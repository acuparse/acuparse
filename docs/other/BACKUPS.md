# Acuparse Backup Guide

A script is included in `cron` to run daily backups. It will run automatically on Docker installs, but local installs
will need to enable this manually.

## Docker Containers

The backup task is enabled in the container by default. Edit your `acuparse.env` file and set `BACKUPS_ENABLED=0` to
disable. See also: [Docker Backup/Restore](https://docs.acuparse.com/DOCKER/#backuprestore)

## Local Install

### Backup

- Copy the backup script in `cron/backup` to your home directory.

    ```bash
    cp /opt/acuparse/cron/backup ~/
    ```

- Update the script with your Acuparse SQL Password.
- Optionally edit `KEEP_BACKUPS_FOR` and `BACKUP_CAM_IMAGES` with your custom values.
- Add the backup script to your system Cron.

    ```bash
    crontab -l | { cat; echo "* 0 * * * /bin/bash ~/backup"; } | crontab -
    ```

### Restore

Extract the backup archive.

```bash
tar -xvf /var/opt/acuparse/backups/<BACKUPDATE>.tar.gz
```

Then restore your database.

```bash
mysql -p<MYSQL_ROOT_PASSWORD> acuparse < mysql.sql
```

Copy your config file back `cp config.php /opt/acuparse/src/usr/config.php`
