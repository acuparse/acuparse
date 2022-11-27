<?php

function getJSONWeatherData($cron=false)
{
    global $config;
    require_once(APP_BASE_PATH . '/fcn/weather/getCurrentWeatherData.php');
    $getData = new getCurrentWeatherData(false, true);
    $data = $getData->getJSONConditions();
    // Using Tower Data
    if ($cron === true && $config->upload->sensor->external === 'tower') {
        require_once(APP_BASE_PATH . '/fcn/cron/towerData.php');
    }
    $jsonExportMain = array("main" => $data);

    if ($config->station->device === 0) {
        if ($config->station->primary_sensor === 0) {
            require_once(APP_BASE_PATH . '/fcn/weather/getCurrentAtlasData.php');
            $getAtlasData = new getCurrentAtlasData();
            $jsonExportAtlas = array("atlas" => $getAtlasData->getJSONData());

            // Load Lightning Data:
            if ($config->station->lightning_source === 1) {
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentLightningData.php');
                $getLightningData = new atlas\getCurrentLightningData('json');
                $jsonExportLightning = array("lightning" => $getLightningData->getJSONData());
                $result = array_merge($jsonExportMain, $jsonExportAtlas, $jsonExportLightning);
            } // Load Tower Lightning Data:
            elseif ($config->station->lightning_source === 2) {
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                $getTowerLightningData = new tower\getCurrentLightningData('json');
                $jsonExportTowerLightning = array("towerLightning" => $getTowerLightningData->getJSONData());
                $result = array_merge($jsonExportMain, $jsonExportAtlas, $jsonExportTowerLightning);
            } // Load Atlas and Tower Lightning Data:
            elseif ($config->station->lightning_source === 3) {
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentLightningData.php');
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                $getLightningData = new atlas\getCurrentLightningData('json');
                $getTowerLightningData = new tower\getCurrentLightningData('json');
                $jsonExportLightning = array("lightning" => $getLightningData->getJSONData());
                $jsonExportTowerLightning = array("towerLightning" => $getTowerLightningData->getJSONData());
                $result = array_merge($jsonExportMain, $jsonExportAtlas, $jsonExportLightning, $jsonExportTowerLightning);
            }
            else {
                $result = array_merge($jsonExportMain, $jsonExportAtlas);
            }
        } else if ($config->station->primary_sensor === 1) {
            if ($config->station->lightning_source === 2) {
                require_once(APP_BASE_PATH . '/fcn/weather/getCurrentTowerLightningData.php');
                $getTowerLightningData = new tower\getCurrentLightningData('json');
                $jsonExportTowerLightning = array("towerLightning" => $getTowerLightningData->getJSONData());
                $result = array_merge($jsonExportMain, $jsonExportTowerLightning);
            } else {
                $result = $jsonExportMain;
            }
        }
    } else {
        $result = $jsonExportMain;
    }
    if (empty($result)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
        echo json_encode(['Error' => "Weather Data Unavailable"]);
        exit();
    } else {
        return json_encode($result);
    }
}
