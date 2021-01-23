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
 * File: sql/updates/v3.0/beta1.sql
 * SQL upgrade operations for version 3.0-beta1
 */

SET AUTOCOMMIT = 0;
START TRANSACTION;

DROP TABLE `lightning`;

CREATE TABLE `lightningData`
(
    `strikecount`          SMALLINT UNSIGNED NOT NULL,
    `interference`         BOOLEAN           NOT NULL,
    `last_strike_ts`       DATETIME          NULL,
    `last_strike_distance` TINYINT UNSIGNED  NULL,
    `source`               char(1)           NOT NULL
)
    ENGINE = MyISAM
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

CREATE TABLE `towerLightning`
(
    `dailystrikes`   SMALLINT UNSIGNED NOT NULL,
    `currentstrikes` SMALLINT UNSIGNED NOT NULL,
    `date`           DATE              NOT NULL,
    `last_update`    DATETIME          NOT NULL
)
    ENGINE = MyISAM
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

CREATE TABLE `atlasLightning`
(
    `dailystrikes`   SMALLINT UNSIGNED NOT NULL,
    `currentstrikes` SMALLINT UNSIGNED NOT NULL,
    `date`           DATE              NOT NULL,
    `last_update`    DATETIME          NOT NULL
)
    ENGINE = MyISAM
    DEFAULT CHARSET = utf8
    COLLATE = utf8_bin;

ALTER TABLE `lightningData`
    ADD PRIMARY KEY (`source`);

ALTER TABLE `towerLightning`
    ADD PRIMARY KEY (`date`);

ALTER TABLE `atlasLightning`
    ADD PRIMARY KEY (`date`);

CREATE TABLE `windguru_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

ALTER TABLE `archive`
    CHANGE `tempF` `tempF`                     FLOAT(5, 2)           NOT NULL,
    CHANGE `feelsF` `feelsF`                   FLOAT(5, 2)           NOT NULL,
    CHANGE `windSmph` `windSpeedMPH`           FLOAT(5, 2)           NOT NULL,
    CHANGE `windSmph_avg2m` `windSpeedMPH_avg` FLOAT(5, 2)           NULL DEFAULT NULL,
    CHANGE `pressureinHg` `pressureinHg`       FLOAT(4, 2)           NOT NULL,
    CHANGE `dewptF` `dewptF`                   FLOAT(5, 2)           NOT NULL,
    CHANGE `rainin` `rainin`                   FLOAT(5, 2)           NOT NULL,
    CHANGE `total_rainin` `total_rainin`       FLOAT(5, 2)           NOT NULL,
    CHANGE `windDEG` `windDEG`                 SMALLINT UNSIGNED     NOT NULL,
    CHANGE `relH` `relH`                       TINYINT UNSIGNED      NOT NULL,
    CHANGE `windDEG_avg2m` `windGustDEG`       SMALLINT(5) UNSIGNED  NULL DEFAULT NULL,
    ADD `windGustMPH`                          TINYINT(3) UNSIGNED   NULL DEFAULT NULL AFTER `windDEG`,
    ADD `uvindex`                              TINYINT(3) UNSIGNED   NULL DEFAULT NULL AFTER `total_rainin`,
    ADD `light`                                MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `uvindex`,
    ADD `lightSeconds`                         SMALLINT(5) UNSIGNED  NULL DEFAULT NULL AFTER `light`,
    ADD `lightning`                            SMALLINT(5) UNSIGNED  NULL DEFAULT NULL AFTER `lightSeconds`;

ALTER TABLE `password_recover`
    DROP FOREIGN KEY `password_recover_uid`;

ALTER TABLE `sessions`
    DROP FOREIGN KEY `sessions_uid`;

ALTER TABLE `users`
    CHANGE `uid` `uid`     SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE `admin` `admin` BOOLEAN           NOT NULL DEFAULT FALSE,
    ADD `token`            CHAR(40)          NULL AFTER `password`;

ALTER TABLE `sessions`
    CHANGE `uid` `uid` SMALLINT UNSIGNED NOT NULL,
    ADD CONSTRAINT `sessions_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `password_recover`
    CHANGE `uid` `uid` SMALLINT UNSIGNED NOT NULL,
    ADD CONSTRAINT `password_recover_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `humidity`
    CHANGE `relH` `relH` TINYINT UNSIGNED NOT NULL;

ALTER TABLE `winddirection`
    CHANGE `degrees` `degrees` SMALLINT UNSIGNED NOT NULL,
    CHANGE `gust` `gust`       SMALLINT UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `dailyrain`
    CHANGE `dailyrainin` `dailyrainin` FLOAT(5, 2) NOT NULL;

ALTER TABLE `pressure`
    CHANGE `inhg` `inhg` FLOAT(4, 2) NOT NULL;

ALTER TABLE `rainfall`
    CHANGE `rainin` `rainin` FLOAT(5, 2) NOT NULL;

ALTER TABLE `temperature`
    CHANGE `tempF` `tempF`         FLOAT(5, 2) NOT NULL,
    CHANGE `heatindex` `heatindex` FLOAT(5, 2) NULL DEFAULT NULL,
    CHANGE `feelslike` `feelslike` FLOAT(5, 2) NULL DEFAULT NULL,
    CHANGE `windchill` `windchill` FLOAT(5, 2) NULL DEFAULT NULL,
    CHANGE `dewptf` `dewptf`       FLOAT(5, 2) NULL DEFAULT NULL;

ALTER TABLE `windspeed`
    CHANGE `speedMPH` `speedMPH`     FLOAT(5, 2) NOT NULL,
    CHANGE `gustMPH` `gustMPH`       FLOAT(5, 2) NULL DEFAULT NULL,
    CHANGE `averageMPH` `averageMPH` FLOAT(5, 2) NULL DEFAULT NULL;

ALTER TABLE `tower_data`
    DROP FOREIGN KEY `tower_sensor_id`;

ALTER TABLE `tower_data`
    CHANGE `id` `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE `tempF` `tempF` FLOAT(5, 2)      NOT NULL,
    CHANGE `relH` `relH`   TINYINT(3)       NOT NULL,
    ADD CONSTRAINT `tower_sensor_id` FOREIGN KEY (`sensor`) REFERENCES `towers` (`sensor`) ON DELETE CASCADE ON UPDATE CASCADE;

UPDATE `system`
SET `value` = '3.0-beta1'
WHERE `system`.`name` = 'schema';

INSERT INTO `system` (`name`, `value`)
VALUES ('latestRelease', '3.0.0-beta1');

INSERT INTO `system` (`name`, `value`)
VALUES ('lastUpdateCheck', '2000-01-01 00:00:00');

COMMIT;
