# Upgrading from 2.4.x

This release moves to PHP 7.2 Support on Debian Stretch(9), Ubuntu 18.04 LTS, and Raspbian Stretch(9).
Users running Debian can update directly. Ubuntu users should upgrade to 18.04 LTS first.

Upgrading your OS/PHP version is recommended but not required.

***NOTICE:*** Highly recommended to make a full system backup before upgrading PHP.

## Update Core

```bash
cd /opt/acuparse && sudo git pull
```

## PHP 7.3 Support

Acuparse now supports PHP 7.3. You can upgrade your PHP environment with the following script.

```bash
cd ~ && wget https://gitlab.com/acuparse/installer/raw/master/resources/php7_3 && sudo bash php7.3 && rm php7_3 | tee ~/php7_3.log
```
