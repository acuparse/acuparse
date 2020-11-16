# Acuparse Troubleshooting Guide

## Syslog

The best way to troubleshoot your install is to view the syslog. All Acuparse output is logged there.

```bash
tail -f /var/log/syslog
```

### Sensor ID's

Sensor ID's are usually located on the sensors themselves. If you can't locate them, the syslog will report all sensors it does not recognize.

## Cron Job

The cron job is essential for the proper operation of Acuparse.
If your station remains offline and/or there are no updates to external providers, there is a chance this is why.

Run the following commands to update your crontab.

```bash
crontab -l | { cat; echo "* * * * * php /opt/acuparse/cron/cron.php > /opt/acuparse/logs/cron.log 2>&1"; } | crontab -
```

You can check for errors running cron jobs in the log file.

```bash
tail -f /opt/acuparse/logs/cron.log
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

### NTP

The date and time are critical to Acuparse operations. Both on the server and on your Access device.

The Access device will check for NTP on `pool.ntp.org`. Ensure your Access can connect to NTP here, or redirect your DNS
for `pool.ntp.org` to a local NTP server.

If the installer was not able to configure NTP for you, review your OS documentation for the proper way to configure NTP
on your system.

### Google DNS

The Access device appears to be configured to resolve DNS through `8.8.8.8` by default. Testing shows that the Access will
query your local DNS for a while, but for currently unknown reasons, will switch and lock onto `8.8.8.8`.
This could be a way to prevent DNS redirects, but there is no data to support that.

To solve, redirect DNS requests from your Access destined for `8.8.8.8` to your local DNS server in your local firewall rules.
Also, ensure your Acuparse hostname set in your Access will resolve through `8.8.8.8`.

### Cisco Switches

If your Access constantly reboots/reconnects when connected to a Cisco switch, enable Portfast.

```text
config terminal
interface FastEthernet0/XX
spanning-tree portfast
end
```
