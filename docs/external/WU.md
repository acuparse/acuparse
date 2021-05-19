# Acuparse Weather Underground Updater Guide

## Registration

1. Sign up for a new account: [https://www.wunderground.com/signup](https://www.wunderground.com/signup)
1. Add a new device: [https://www.wunderground.com/member/devices/new](https://www.wunderground.com/member/devices/new)
1. Choose `Personal Weather Station`
1. Device Hardware: Choose `other`
1. Set your location
1. Fill out your station data

## Configuration

1. Change enabled to true
1. Add your station ID and Weather Underground password

## Webcam

### FTP Upload

The `combined` and `remote` camera upload scripts include a section for uploading via FTP.

### URL Upload

(Currently Broken?)

1. Add a new device: [https://www.wunderground.com/member/devices/new](https://www.wunderground.com/member/devices/new)
1. Choose `Outdoor Webcam`
1. Camera Type: Choose `URL`
1. Set your location
1. Fill out your camera data
    1. Device Image URL = `http(s)://<yourip/domain>/img/cam/latest.jpg`
