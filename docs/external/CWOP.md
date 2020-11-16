# Acuparse Citizen Weather Observer Program Updater Guide

CWOP has a more complicated format and initialization process. Review the following site for [more details](http://www.wxqa.com/faq.html).

After you have sent data packets to CWOP, check that your data is successfully reaching the findu.com server by visiting
`http://map.findu.com/<your_cwop_id>`.

If your plotted location on the findU map is correct, send an e-mail to cwop-support@noaa.gov giving your CW/DW designator
and ask to be registered. Your data can then flow to NOAA.

## Registration

[http://www.findu.com/citizenweather/cw_form.html](http://www.findu.com/citizenweather/cw_form.html)

## Configuration

1. Change enabled to true
1. Input your station ID
1. Enter your station coordinates in `ddmm.hhN/dddmm.hhW` format.
    - Where d is degrees, m is minutes and h is hundredths of minutes.
    - The hemispheres are represented by capital letters; N, E, S, W
1. Check the update interval. 10 minute intervals is a good choice but no sooner than every 5.
