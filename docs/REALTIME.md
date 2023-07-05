# Acuparse RTL Realtime Update Guide

Acuparse can now take data from your RTL-SDR dongle and update your data in real time.

The AcuRite Access device is limited to sending updates to MyAcuRite and Acuparse every five minutes. Since the sensors
like the Atlas and Iris are actually capable of sending data much more frequently, this guide will show you how to
take advantage of the raw data that your sensors broadcast to the Access device.

This guide will walk you through the process of setting up your RTL-SDR dongle and getting it working with Acuparse.

!!! danger
    This feature is currently in a minimally tested `beta` state and may not work as expected. Please report any bugs
    on the [Issue Tracker](https://gitlab.com/acuparse/acuparse/-/issues/new?issuable_template=Bug).

RTL Dongle support is supplemental to your Access/SmartHub! You will need to configure those devices first. This feature
is designed for advanced users with timely data reporting requirements and requires some existing Linux and Acuparse
knowledge. If you are not comfortable working with Linux or the Acuparse structure, you should not attempt to use this
feature.

!!! warning
    RTL readings and realtime updates are extremely noisy and will result in a substantial increase in network and
    database traffic. Users on RaspberryPi's should consider using a high capacity, max endurance memory card. It's also
    highly recommended to ensure [database trimming](https://docs.acuparse.com/INSTALL/#database-trimming) is enabled.

## Unavailable Data

RTL readings do not update the systems `last_updated` timestamp as not all readings are recorded through RTL and are
received in rotating batches. You may still receive system offline messages, even while actively receiving realtime RTL
data. When the primary sensor is offline, Acuparse will stop building archive updates. Data received from the RTL dongle
will continue to be displayed, but will not be recorded in the archive or sent to external services while the primary
sensor is offline. This is due to the system data being in an inconsistent state, while the Access/SmartHub is offline.

While efforts have been made to keep errors to a minimum when the primary sensor is offline, you may encounter strange
issues when both the RTL dongle and primary sensor are offline. The dashboard has been updated to display the last
update time and realtime status in the footer. The `system/health` api endpoint has also been updated to include the
`realtime` and `last_updated` values. The health endpoint returns the latest `last_updated` value using timestamps from
`windspeed` since it is the most frequently updated sensor.

RTL does not currently support many of the advanced features provided by through the Access/SmartHub.
RTL should be used to facilitate live readings on the dashboard, in conjunction with your exiting hardware setup.
RTL readings will also be used when building archive data and when uploading to external services.

- **Barometric Pressure**
    - Pressure is calculated by the Access/SmartHub and is not available via RTL.
- **Lightning**
    - Lightning is already a complex calculation and is currently best left to the Access update cycle.
- **Rainfall**
    - RTL data was inconsistent and unreliable. Further testing/logic required.

## Installation

Use of RTL Dongles is only supported using the RTL Relay Docker containers. You can send data to any physical Acuparse
install, but the RTL relay service itself, will only run using Docker. Acuparse will use the MAC address from your
primary device to authenticate readings. You must set the environment variable `PRIMARY_MAC_ADDRESS` in your relay
configuration to match the MAC address of your primary device. (no spaces, `'`, `"`, `-`, or `:`).
You can find this in your Acuparse configuration settings.

The RTL relay service consists of two container services

1. relay
    - A small custom application designed to send the RTL captured syslog data to Acuparse for further processing.

2. rtl
    - The [rtl_433 software](https://github.com/merbanan/rtl_433) is maintained by external parties and is only supported by
   Acuparse when used with the [hertzg/rtl_433](https://github.com/hertzg/rtl_433_docker) container build.

### Existing Acuparse Install (Non-Docker)

1. Install Docker and Compose.
    - [Install Docker](https://docs.docker.com/get-docker)
    - [Install Compose](https://docs.docker.com/compose/install)

2. Create a new RTL Relay Docker Compose config file and add the config below. Update `PRIMARY_MAC_ADDRESS` with the MAC
   address of your primary device. Update `ACUPARSE_HOST` with the hostname or IP address of your Acuparse install. If you have Acuparse running on a
   non-standard port, add `- ACUPARSE_PORT='<PORT>'` under `environment:` with the port you are using for Acuparse.

```yaml
version: '3.7'

services:
  relay:
    image: acuparse/rtl-relay
    environment:
      - PRIMARY_MAC_ADDRESS=000000000000
      - ACUPARSE_HOSTNAME=acuparse.example.com
    restart: always

  rtl:
    image: hertzg/rtl_433
    restart: always
    command: -F syslog:relay:10514
    devices:
      - "/dev/bus/usb/001:/dev/bus/usb/001"
    depends_on:
      - relay
```

### Existing Docker Based Acuparse Install

If you are currently running Acuparse in Docker, you only need to add the RTL containers to your existing compose
config file. Add the two containers below under the existing `services:` but before `volumes:` in your
`docker-compose.yml` file.

```yaml
      relay:
        image: acuparse/rtl-relay
        environment:
          - PRIMARY_MAC_ADDRESS=000000000000
        restart: always

      rtl:
        image: hertzg/rtl_433
        restart: always
        command: -F syslog:relay:10514
        devices:
          - "/dev/bus/usb/001:/dev/bus/usb/001"
        depends_on:
          - relay
```

### Using Acuparse with an existing RTL 433 implementation

The Acuparse RTL relay server listens for syslog messages from the [rtl_433](https://github.com/merbanan/rtl_433/)
software on port `10514`. You can use any exiting implementation to send readings to the relay server.

Use the following command in your rtl_433 config to send syslog data to the relay service for processing.

```bash
-F syslog:<ACUPARSE_RELAY_SERVER_IP>:10514
```

#### Stanalone Relay Container

You can run a standalone rtl-relay container using the following command.

```bash
docker run -d --rm --name rtl-relay \
    -e PRIMARY_MAC_ADDRESS=000000000000 \
    -e ACUPARSE_HOSTNAME=acuparse.example.com \
    -p 10514:10514 \
    -p 10514:10514/udp \
    acuparse/rtl-relay
```

The relay listen on any local port by changing the `-p` flag. You will need to update the `command:` in your
rtl_433 config to match the port you are using.

`-p <LOCAL_PORT>:10514`, `-p <LOCAL_PORT>:10514/udp`

```bash
-F syslog:<ACUPARSE_RELAY_SERVER_IP>:<PORT>
```

### Enable Realtime Updates

Once you have the RTL Relay service up and running, you can enable realtime updates in your Acuparse settings.
Realtime updates is located in the Sensor tab.

#### System Timezone

Docker uses UTC as the timezone by default and Acuparse expects RTL readings to be in UTC. If you are not using
the default container/config, you can have RTL readings reported using the system timezone instead.

## Updating

To update your RTL Relay containers, run the following command:

```bash
docker-compose pull && docker-compose up -d
```

## Troubleshooting

Check the Acuparse `/var/log/syslog` for any errors and to ensure your RTL relay is active and sending data.
You can also review the logs from the docker relay container using `docker-compose logs relay -f`.

If you need assistance using/configuring this feature, please reach out to the project via [Community Support](/#community-support).

### Container Exit Code 132 (SIGILL?)

If your relay is dying with exit code 132 (SIGILL?) and gives this log output, update your compose configuration to
run the container as privileged. This is due to a bug 32bit buster installs and some security changes in docker.

```log
relay_1     | Node.js[1]: ../src/util.cc:190:double node::GetCurrentTimeInMicroseconds(): Assertion `(0) == (uv_gettimeofday(&tv))' failed.
relay_1     | Node.js[1]: ../src/util.cc:190:double node::GetCurrentTimeInMicroseconds(): Assertion `(0) == (uv_gettimeofday(&tv))' failed.
relay_1     | Node.js[1]: ../src/util.cc:190:double node::GetCurrentTimeInMicroseconds(): Assertion `(0) == (uv_gettimeofday(&tv))' failed.
relay_1     | Node.js[1]: ../src/util.cc:190:double node::GetCurrentTimeInMicroseconds(): Assertion `(0) == (uv_gettimeofday(&tv))' failed.
relay_1     | Node.js[1]: ../src/util.cc:190:double node::GetCurrentTimeInMicroseconds(): Assertion `(0) == (uv_gettimeofday(&tv))' failed.
acuparse_relay_1 exited with code 132
```

Update your compose file to include `privileged: true` under the `relay:` service.

```yaml
      relay:
        image: acuparse/rtl-relay
        privileged: true
        environment:
          - PRIMARY_MAC_ADDRESS=000000000000
        restart: always
```
