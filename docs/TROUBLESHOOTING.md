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

## Access Device

If your not receiving data from your Access, ensure you have a DNS resolvable hostname set for the upload server and/or
a DNS redirect in place. Then, reboot your Access. The Access can at times require multiple reboots to begin sending data.

### Cisco Switches

If your Access constantly reboots/reconnects when connected to a Cisco switch, enable Portfast.

```text
config terminal
interface FastEthernet0/XX
spanning-tree portfast
end
```
