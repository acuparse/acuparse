# Acuparse Troubleshooting Guide

## Syslog

The best way to troubleshoot your install is to view the syslog. All output is logged there.

```bash
tail -f /var/log/syslog
```

### Sensor ID's

Sensor ID's are usually located on the sensors themselves. If you can't locate them, the syslog will report all sensors it does not recognize.

## Cron Job

The cron job is essential to the proper operation of Acuparse.
If your station remains offline, there is a chance this is why.

Run the following commands to update your crontab.

```bash
crontab -l | { cat; echo "* * * * * php /opt/acuparse/cron/cron.php > /opt/acuparse/logs/cron.log 2>&1"; } | crontab -
```

## Installation Errors

If you experience unexpected results while completing the web install, try removing your config file and retry.

```bash
sudo rm /opt/acuparse/src/usr/config.php
```

In more extreme cases, you may also need to remove and [reinitialize the Acuparse database](https://docs.acuparse.com/INSTALL/#setup-database).
