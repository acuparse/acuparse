# Acuparse API Guide

**Current API version: `v1`**

The API is available at `http(s)://<yourip/domain>/api/<VERSION>`.

To get private data, you must pass an API token with the request.
You can create an API token from your user profile page.

**To pass your token, add it to the beginning or anywhere in your API query:**

> **NOTE:** Passing your token in a query string can be insecure and result in it being stored in your browser history.

- `?token=<API_TOKEN>`
    - No Query: `http(s)://<yourip/domain>/api/v1/json/dashboard?token=<API_TOKEN>`
    - Query: `http(s)://<yourip/domain>/api/v1/json/tower?token=<API_TOKEN>&id={TOWER_ID}`

## Dashboard (with Towers)

- HTML: `http(s)://<yourip/domain>/api/v1/html/dashboard`
- JSON: `http(s)://<yourip/domain>/api/v1/json/dashboard`

## Main Only

- HTML: `http(s)://<yourip/domain>/api/v1/html/dashboard?main`
- JSON: `http(s)://<yourip/domain>/api/v1/json/dashboard?main`

## All Towers

- HTML: `http(s)://<yourip/domain>/api/v1/html/tower`
- JSON: `http(s)://<yourip/domain>/api/v1/json/tower`

## Single Tower

- HTML: `http(s)://<yourip/domain>/api/v1/html/tower?id={TOWER_ID}`
- JSON: `http(s)://<yourip/domain>/api/v1/json/tower?id={TOWER_ID}`

## Archive

- HTML: `http(s)://<yourip/domain>/api/v1/html/archive`
- JSON: `http(s)://<yourip/domain>/api/v1/json/archive`

### Archive Search

- JSON: `http(s)://<yourip/domain>/api/v1/json/archive/search?query=&start=&end=&sort=&limit=`
    - Set `query` to one of `wind`/`rain`/`temp`/`relh`/`pressure`/`lightning`/`uv`/`light`.
    - Set `start` and optionally `end` to the Start/End Date and Time. Setting just the date will use 00:00:00 for time.
    If `end` is not specified, defaults to `now`.
    - Optionally, set `sort` to `asc` or `desc`. Sort defaults to `asc`.
    - Optionally, set `limit` to limit the number of results returned. **Defaults to 100 without a token/admin login!**

## Plain Text Output

- Camera Watermark: `http(s)://<yourip/domain>/api/v1/text/watermark`
- Date/Time: `http(s)://<yourip/domain>/api/v1/text/time`
- [Cumulus formatted](https://cumuluswiki.org/a/Realtime.txt) Realtime: `http(s)://<yourip/domain>/api/v1/text/realtime`

## System Data

- Health: `http(s)://<yourip/domain>/api/system/health`

### API Token/Admin (Login Required)

- Config Data: `http(s)://<yourip/domain>/api/system/config`
- PHP Info: `http(s)://<yourip/domain>/api/system/phpinfo`

## Examples

### PHP

#### Main Readings

```php
function getWeatherData($acuparseURL)
{
    $w = file_get_contents($acuparseURL . '/api/v1/json/dashboard?main');
    $w = json_decode($w);
    $w = $w->main;
    return $w;
}

$weather = getWeatherData('http(s)://<yourip/domain>');

// Main Readings
$tempF = $weather->main->tempF;
$tempC = $weather->main->tempC;
$tempF_trend = $weather->main->tempF_trend;
$feelsF = $weather->main->feelsF;
$feelsC = $weather->main->feelsC;
$dewptF = $weather->main->dewptF;
$dewptC = $weather->main->dewptC;
$tempC_high = $weather->main->tempC_high;
$tempF_high = $weather->main->tempF_high;
$high_temp_recorded = $weather->main->high_temp_recorded;
$tempC_low = $weather->main->tempC_low;
$tempF_low = $weather->main->tempF_low;
$low_temp_recorded = $weather->main->low_temp_recorded;
$tempC_avg = $weather->main->tempC_avg;
$tempF_avg = $weather->main->tempF_avg;
$relH = $weather->main->relH;
$relH_trend = $weather->main->relH_trend;
$windSpeedMPH = $weather->main->windSpeedMPH;
$windSpeedKMH = $weather->main->windSpeedKMH;
$windDEG = $weather->main->windDEG;
$windDIR = $weather->main->windDIR;
$windDEG_peak = $weather->main->windDEG_peak;
$windDIR_peak = $weather->main->windDIR_peak;
$windSpeedMPH_peak = $weather->main->windSpeedMPH_peak;
$windSpeedKMH_peak = $weather->main->windSpeedKMH_peak;
$windSpeed_peak_recorded = $weather->main->windSpeed_peak_recorded;
$windBeaufort = $weather->main->windBeaufort;
$rainIN = $weather->main->rainIN;
$rainMM = $weather->main->rainMM;
$rainTotalIN_today = $weather->main->rainTotalIN_today;
$rainTotalMM_today = $weather->main->rainTotalMM_today;
$pressure_inHg = $weather->main->pressure_inHg;
$pressure_kPa = $weather->main->pressure_kPa;
$pressure_trend = $weather->main->pressure_trend;
$sunrise = $weather->main->sunrise;
$sunset = $weather->main->sunset;
$moonrise = $weather->main->moonrise;
$moonset = $weather->main->moonset;
$moon_age = $weather->main->moon_age;
$moon_stage = $weather->main->moon_stage;
$moon_illumination = $weather->main->moon_illumination;
$moon_nextNew = $weather->main->moon_nextNew;
$moon_nextFull = $weather->main->moon_nextFull;
$moon_lastNew = $weather->main->moon_lastNew;
$moon_lastFull = $weather->main->moon_lastFull;
$lastUpdated = $weather->main->lastUpdated;

// Atlas Readings
$lightIntensity = $weather->atlas->lightIntensity;
$lightIntensity_text = $weather->atlas->lightIntensity_text;
$lightSeconds = $weather->atlas->lightSeconds;
$lightHours = $weather->atlas->lightHours;
$uvIndex = $weather->atlas->uvIndex;
$uvIndex_text = $weather->atlas->uvIndex_text;
$windGustDEG = $weather->atlas->windGustDEG;
$windGustDIR = $weather->atlas->windGustDIR;
$windGustMPH = $weather->atlas->windGustMPH;
$windGustKMH = $weather->atlas->windGustKMH;
$windGustPeakMPH = $weather->atlas->windGustPeakMPH;
$windGustPeakKMH = $weather->atlas->windGustPeakKMH;
$windGustDEGPeak = $weather->atlas->windGustDEGPeak;
$windGustDIRPeak = $weather->atlas->windGustDIRPeak;
$windGustPeakRecorded = $weather->atlas->windGustPeakRecorded;
$windAvgMPH = $weather->atlas->windAvgMPH;
$windAvgKMH = $weather->atlas->windAvgKMH;
$battery = $weather->atlas->battery;
$signal = $weather->atlas->signal;
$lastUpdate = $weather->atlas->lastUpdate;

// Lightning
$dailystrikes = $weather->lightning->dailystrikes;
$currentstrikes = $weather->lightning->currentstrikes;
$interference = $weather->lightning->interference;
$last_strike_ts = $weather->lightning->last_strike_ts;
$last_strike_distance_KM = $weather->lightning->last_strike_distance_KM;
$last_strike_distance_M = $weather->lightning->last_strike_distance_M;
$last_update = $weather->lightning->last_update;
```

#### Tower Readings

```php
function getTowerSensorData($acuparseURL, $acuparseToken)
{
    $t = file_get_contents($acuparseURL . '/api/v1/json/tower?token=' . $acuparseToken);
    $t = json_decode($t);
    return $t->towers;
}

$towers = getTowerSensorData('http(s)://<yourip/domain>', '<API_Token>');

foreach ($towers as $tower) {
    $name = $tower->name;
    $tempF = $tower->tempF;
    $tempF_high = $tower->tempF_high;
    $tempF_low = $tower->tempF_low;
    $tempF_trend = $tower->tempF_trend;
    $tempC = $tower->tempC;
    $tempC_high = $tower->tempC_high;
    $tempC_low = $tower->tempC_low;
    $high_temp_recorded = $tower->high_temp_recorded;
    $low_temp_recorded = $tower->low_temp_recorded;
    $relH = $tower->relH;
    $relH_trend = $tower->relH_trend;
    $battery = $tower->battery;
    $signal = $tower->signal;
    $lastUpdated = $tower->lastUpdated;
}
```
