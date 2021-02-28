# Acuparse Windy Updater Guide

## Registration

1. Go to [https://community.windy.com/register](https://community.windy.com/register) and fill out the form.
1. Agree to the data collection policy.
1. Visit [https://stations.windy.com/stations](https://stations.windy.com/stations) and add a new station.
1. When your station is added, click "Show Key" to get your `API Key`.

## Configuration

1. Change enabled to true.
1. Add your Windy `ID`, `Station ID`, and `API Key`.
    1. `ID` is the first `ID` NOT the `Station ID`.
    1. If you have multiple Windy stations, enter your `station ID`. Default is 0.

## Webcam

1. Visit [https://www.windy.com/webcams/add](https://www.windy.com/webcams/add) and add a new camera.
    1. Page URL = `http(s)://<yourip/domain>/camera`
    1. Image URL = `http(s)://<yourip/domain>/img/cam/latest.jpg`
