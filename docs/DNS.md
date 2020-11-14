# Acuparse DNS Redirect Guide

You will need to redirect your DNS so that your Access/smartHub uploads your data to Acuparse and not MyAcuRite directly.
> **Note:** Access users can use the included script to modify the Access upload server instead of, or as well as, redirecting DNS.
>
>See [/admin/access_server](/admin/access_server) once logged into your site.
>
>You must use a hostname for the upload server. IP addresses will not work!

## External Connection to Acuparse

If your smartHUB/Access is connected to your local network and sending readings to MyAcurite directly, you will need to
install a DNS server on your local network.

### DNS Servers

- Install Bind9 on a device installed locally on your network
- Use something like [Pi-hole®](https://pi-hole.net)

### Firewalls

Use a firewall that allows you to customise your DNS.

- [pfSense®](https://www.pfsense.org/)
- [OPNsense®](https://opnsense.org/)

## Direct Connection to Acuparse

You can use your Acuparse server to redirect your DNS locally. To use this method, connect your smartHUB/Access directly
to an ethernet interface on the Acuparse system.

### MyAcurite Upload URL's

Redirecting the DNS locally will cause issues with uploads to MyAcurite. The redirect will cause Acuparse to upload data
to its self. In this case, you should select the secondary urls in the MyAcuRite config settings. Acuparse will then upload
readings to MyAcuRite as expected.

- Secondary DNS entries point to the Acuparse domain hosted on Cloudflare. Records are synced with Acurite on a regular basis.
