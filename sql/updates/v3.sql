/*
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2019 Maxwell Power
 * @author Maxwell Power <max@acuparse.com>
 * @link http://www.acuparse.com
 * @license AGPL-3.0+
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this code. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * File: sql/updates/v3.sql
 * Pending SQL Operations
 * SQL upgrade operations for version 3.0
 */

ALTER TABLE `winddirection`
    ENGINE =MyISAM;
ALTER TABLE `windspeed`
    ENGINE =MyISAM;
ALTER TABLE `pressure`
    ENGINE =MyISAM;
ALTER TABLE `temperature`
    ENGINE =MyISAM;
ALTER TABLE `humidity`
    ENGINE =MyISAM;
ALTER TABLE `dailyrain`
    ENGINE =MyISAM;
ALTER TABLE `rainfall`
    ENGINE =MyISAM;

ALTER TABLE `tower_data`
    CHANGE `relH` `relH` DECIMAL(3, 0) NOT NULL,
    ADD `battery`        VARCHAR(6)    NOT NULL AFTER `relH`,
    ADD `rssi`           TINYINT(1)    NOT NULL AFTER `battery`,
    ADD `device`         CHAR(1)       NOT NULL AFTER `sensor`;
ALTER TABLE `winddirection`
    CHANGE `degrees` `degrees` DECIMAL(3, 0) NOT NULL FIRST,
    ADD `gust`                 DECIMAL(3, 0) NULL AFTER `degrees`,
    ADD `device`               CHAR(1)       NOT NULL AFTER `timestamp`,
    ADD `source`               CHAR(1)       NOT NULL AFTER `device`;
ALTER TABLE `windspeed`
    CHANGE `speedMPH` `speedMPH` DECIMAL(5, 2) NOT NULL FIRST,
    ADD `gustMPH`                DECIMAL(5, 2) NULL AFTER `speedMPH`,
    ADD `averageMPH`             DECIMAL(5, 2) NULL AFTER `gustMPH`,
    ADD `device`                 CHAR(1)       NOT NULL AFTER `timestamp`,
    ADD `source`                 CHAR(1)       NOT NULL AFTER `device`;
ALTER TABLE `pressure`
    ADD `device`         CHAR(1)       NOT NULL AFTER `inhg`,
    ADD `source`         CHAR(1)       NOT NULL AFTER `device`,
    CHANGE `inhg` `inhg` DECIMAL(4, 2) NOT NULL FIRST;
ALTER TABLE `temperature`
    ADD `heatindex` DECIMAL(5, 2) NULL AFTER `tempF`,
    ADD `feelslike` DECIMAL(5, 2) NULL AFTER `heatindex`,
    ADD `windchill` DECIMAL(5, 2) NULL AFTER `feelslike`,
    ADD `dewptf`    DECIMAL(5, 2) NULL AFTER `windchill`,
    ADD `device`    CHAR(1)       NOT NULL AFTER `dewptf`,
    ADD `source`    CHAR(1)       NOT NULL AFTER `device`;
ALTER TABLE `humidity`
    CHANGE `relH` `relH` DECIMAL(3, 0) NOT NULL FIRST,
    ADD `device`         CHAR(1)       NOT NULL AFTER `relH`,
    ADD `source`         CHAR(1)       NOT NULL AFTER `device`;
ALTER TABLE `dailyrain`
    CHANGE `dailyrainin` `dailyrainin` DECIMAL(5, 2) NOT NULL FIRST,
    ADD `device`                       CHAR(1)       NOT NULL AFTER `last_update`,
    ADD `source`                       CHAR(1)       NOT NULL AFTER `device`;
ALTER TABLE `rainfall`
    ADD `device` CHAR(1) NOT NULL AFTER `last_update`,
    ADD `source` CHAR(1) NOT NULL AFTER `device`;

CREATE TABLE `access_status`
(
    `battery`     VARCHAR(6) NOT NULL,
    `last_update` timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

ALTER TABLE `access_status`
    ADD PRIMARY KEY (`last_update`);
INSERT INTO `access_status` (`battery`, `last_update`)
VALUES ('normal', '1970-01-01 00:00:00');

CREATE TABLE `atlas_status`
(
    `battery`     VARCHAR(6) NOT NULL,
    `rssi`        TINYINT(1) NOT NULL,
    `last_update` timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

ALTER TABLE `atlas_status`
    ADD PRIMARY KEY (`last_update`);
INSERT INTO `atlas_status` (`battery`, `rssi`, `last_update`)
VALUES ('normal', '0', '1970-01-01 00:00:00');

CREATE TABLE `5n1_status`
(
    `device`      VARCHAR(6) NOT NULL,
    `battery`     VARCHAR(6) NOT NULL,
    `rssi`        TINYINT(1) NOT NULL,
    `last_update` timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

ALTER TABLE `5n1_status`
    ADD PRIMARY KEY (`device`);
INSERT INTO `5n1_status` (`device`, `battery`, `rssi`, `last_update`)
VALUES ('access', 'normal', '0', '1970-01-01 00:00:00');
INSERT INTO `5n1_status` (`device`, `battery`, `rssi`, `last_update`)
VALUES ('hub', 'normal', '0', '1970-01-01 00:00:00');

CREATE TABLE `uvindex`
(
    `timestamp` timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `uvindex`   varchar(2) NOT NULL
)
    ENGINE = MyISAM
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

ALTER TABLE `uvindex`
    ADD PRIMARY KEY (`timestamp`);

CREATE TABLE `light`
(
    `timestamp`              timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `lightintensity`         varchar(7) NOT NULL,
    `measured_light_seconds` varchar(6) NOT NULL
)
    ENGINE = MyISAM
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

ALTER TABLE `light`
    ADD PRIMARY KEY (`timestamp`);

CREATE TABLE `lightning`
(
    `timestamp`            timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `strikecount`          varchar(3) NOT NULL,
    `interference`         tinyint(1) NOT NULL,
    `last_strike_ts`       datetime  NOT NULL,
    `last_strike_distance` varchar(2) NOT NULL
)
    ENGINE = MyISAM
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

ALTER TABLE `lightning`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `rainfall`
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`device`) USING BTREE;

ALTER TABLE `tower_data`
    DROP INDEX `sensor`,
    ADD INDEX `sensor` (`sensor`, `timestamp`) USING BTREE;

ALTER TABLE `archive`
    ENGINE =MyISAM;
ALTER TABLE `archive`
    DROP INDEX `readings`;
ALTER TABLE `archive`
    ADD UNIQUE `tempF` (`reported`, `tempF`);
ALTER TABLE `archive`
    ADD UNIQUE `windSmph` (`reported`, `windSmph`);
ALTER TABLE `archive`
    ADD UNIQUE `windDEG` (`reported`, `windDEG`);
ALTER TABLE `archive`
    ADD UNIQUE `relH` (`reported`, `relH`);
ALTER TABLE `archive`
    ADD UNIQUE `pressureinHg` (`reported`, `pressureinHg`);
ALTER TABLE `archive`
    ADD UNIQUE `rainin` (`reported`, `rainin`);
ALTER TABLE `archive`
    ADD UNIQUE `total_rainin` (`reported`, `total_rainin`);

ALTER TABLE `dailyrain`
    DROP INDEX `date`;
ALTER TABLE `dailyrain`
    ADD UNIQUE `archive` (`dailyrainin`, `date`, `last_update`);

ALTER TABLE `humidity`
    ADD UNIQUE `relH` (`relH`, `timestamp`);

ALTER TABLE `pressure`
    ADD UNIQUE `inhg` (`inhg`, `timestamp`);

ALTER TABLE `temperature`
    ADD UNIQUE `tempF` (`timestamp`, `tempF`);

ALTER TABLE `winddirection`
    ADD UNIQUE `degrees` (`degrees`, `timestamp`);

ALTER TABLE `windspeed`
    ADD UNIQUE `speedMPH` (`speedMPH`, `timestamp`);

TRUNCATE TABLE `last_update`;
INSERT INTO `last_update` (`timestamp`)
VALUES ('1970-01-01 00:00:00');

TRUNCATE TABLE `outage_alert`;
INSERT INTO `outage_alert` (`last_sent`, `status`)
VALUES ('1970-01-01 00:00:00', '0');

TRUNCATE TABLE `sessions`;

UPDATE `system`
SET `value` = '3.0'
WHERE `system`.`name` = 'schema';
