# Acuparse MQTT Guide

MQTT is an industry standard messaging protocol for IoT messaging.

## Configuration

1. Select the Enable button in MQTT settings.
2. Set your MQTT Server, hostname/IP, Port and Main Topic.
3. Optionally enter your username, password, and ClientID.

## Usage

Acuparse will publish the same data it outputs as JSON to your broker when your readings update.
It uses the main topic set in settings as the root topic, with all data being published to the same topic format as the
existing JSON endpoint. The raw JSON will be published to the root topic, with individual sensor readings published as
sub-topics. Towers will publish the raw JSON to the `sensor_id` sub topic.

### Topics

- Main Topic (default: acuparse)
    - main
    - atlas
    - lightning
    - towers
        - `sensor id`

See the [API examples](https://docs.acuparse.com/API/#examples) for the available readings.
