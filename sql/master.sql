/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2020 Maxwell Power
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
 * File: sql/master.sql
 * The master database schema
 * Version 3.0
 */

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `5n1_status`
(
    `device`      varchar(6) COLLATE utf8_bin NOT NULL,
    `battery`     varchar(6) COLLATE utf8_bin NOT NULL,
    `rssi`        tinyint(1)                  NOT NULL,
    `last_update` timestamp                   NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `access_status`
(
    `battery`     varchar(6) COLLATE utf8_bin NOT NULL,
    `last_update` timestamp                   NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `archive`
(
    `reported`         timestamp            NOT NULL DEFAULT current_timestamp(),
    `tempF`            float(5, 2)          NOT NULL,
    `feelsF`           float(5, 2)          NOT NULL,
    `windSpeedMPH`     float(5, 2)          NOT NULL,
    `windSpeedMPH_avg` float(5, 2)                   DEFAULT NULL,
    `windDEG`          smallint(5) UNSIGNED NOT NULL,
    `windGustMPH`      tinyint(3) UNSIGNED           DEFAULT NULL,
    `windGustDEG`      smallint(5) UNSIGNED          DEFAULT NULL,
    `relH`             tinyint(3) UNSIGNED  NOT NULL,
    `pressureinHg`     float(4, 2)          NOT NULL,
    `dewptF`           float(5, 2)          NOT NULL,
    `rainin`           float(5, 2)          NOT NULL,
    `total_rainin`     float(5, 2)          NOT NULL,
    `uvindex`          tinyint(3) UNSIGNED           DEFAULT NULL,
    `light`            mediumint(8) UNSIGNED         DEFAULT NULL,
    `lightSeconds`     smallint(5) UNSIGNED          DEFAULT NULL,
    `lightning`        smallint(5) UNSIGNED          DEFAULT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `atlasLightning`
(
    `dailystrikes`   smallint(5) UNSIGNED NOT NULL,
    `currentstrikes` smallint(5) UNSIGNED NOT NULL,
    `date`           date                 NOT NULL,
    `last_update`    datetime             NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `atlas_status`
(
    `battery`     varchar(6) COLLATE utf8_bin NOT NULL,
    `rssi`        tinyint(1)                  NOT NULL,
    `last_update` timestamp                   NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `cwop_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `dailyrain`
(
    `dailyrainin` float(5, 2)              NOT NULL,
    `date`        date                     NOT NULL,
    `last_update` timestamp                NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `device`      char(1) COLLATE utf8_bin NOT NULL,
    `source`      char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `generic_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `humidity`
(
    `relH`      tinyint(3) UNSIGNED      NOT NULL,
    `device`    char(1) COLLATE utf8_bin NOT NULL,
    `source`    char(1) COLLATE utf8_bin NOT NULL,
    `timestamp` timestamp                NOT NULL DEFAULT current_timestamp()
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `last_update`
(
    `timestamp` datetime NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `light`
(
    `timestamp`              timestamp             NOT NULL DEFAULT current_timestamp(),
    `lightintensity`         mediumint(8) UNSIGNED NOT NULL,
    `measured_light_seconds` smallint(5) UNSIGNED  NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `lightningData`
(
    `strikecount`          smallint(5) UNSIGNED     NOT NULL,
    `interference`         tinyint(1)               NOT NULL,
    `last_strike_ts`       datetime            DEFAULT NULL,
    `last_strike_distance` tinyint(3) UNSIGNED DEFAULT NULL,
    `source`               char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `outage_alert`
(
    `last_sent` timestamp  NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `status`    tinyint(1) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `password_recover`
(
    `uid`  smallint(5) UNSIGNED      NOT NULL,
    `hash` char(32) COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `pressure`
(
    `inhg`      float(4, 2)              NOT NULL,
    `timestamp` timestamp                NOT NULL DEFAULT current_timestamp(),
    `device`    char(1) COLLATE utf8_bin NOT NULL,
    `source`    char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `pws_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `rainfall`
(
    `rainin`      float(5, 2)              NOT NULL,
    `last_update` timestamp                NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `device`      char(1) COLLATE utf8_bin NOT NULL,
    `source`      char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `sessions`
(
    `uid`        smallint(5) UNSIGNED          NOT NULL,
    `device_key` char(40) COLLATE utf8_bin     NOT NULL,
    `token`      char(40) COLLATE utf8_bin     NOT NULL,
    `user_agent` varchar(255) COLLATE utf8_bin NOT NULL,
    `timestamp`  timestamp                     NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `system`
(
    `name`  varchar(255) COLLATE utf8_bin NOT NULL,
    `value` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `temperature`
(
    `timestamp` timestamp                NOT NULL DEFAULT current_timestamp(),
    `tempF`     float(5, 2)              NOT NULL,
    `heatindex` float(5, 2)                       DEFAULT NULL,
    `feelslike` float(5, 2)                       DEFAULT NULL,
    `windchill` float(5, 2)                       DEFAULT NULL,
    `dewptf`    float(5, 2)                       DEFAULT NULL,
    `device`    char(1) COLLATE utf8_bin NOT NULL,
    `source`    char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `towerLightning`
(
    `dailystrikes`   tinyint(3) UNSIGNED NOT NULL,
    `currentstrikes` tinyint(3) UNSIGNED NOT NULL,
    `date`           date                NOT NULL,
    `last_update`    datetime            NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `towers`
(
    `sensor`  char(8) COLLATE utf8_bin      NOT NULL,
    `name`    varchar(255) COLLATE utf8_bin NOT NULL,
    `arrange` tinyint(1)                    NOT NULL DEFAULT 0,
    `private` tinyint(1)                    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `tower_data`
(
    `id`        int(11) UNSIGNED            NOT NULL,
    `tempF`     float(5, 2)                 NOT NULL,
    `relH`      tinyint(3)                  NOT NULL,
    `battery`   varchar(6) COLLATE utf8_bin NOT NULL,
    `rssi`      tinyint(1)                  NOT NULL,
    `timestamp` timestamp                   NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `sensor`    char(8) COLLATE utf8_bin    NOT NULL,
    `device`    char(1) COLLATE utf8_bin    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `users`
(
    `uid`      smallint(5) UNSIGNED           NOT NULL,
    `username` varchar(32) CHARACTER SET utf8 NOT NULL,
    `email`    varchar(255) COLLATE utf8_bin  NOT NULL,
    `password` varchar(255) COLLATE utf8_bin  NOT NULL,
    `token`    char(40) COLLATE utf8_bin               DEFAULT NULL,
    `admin`    tinyint(1)                     NOT NULL DEFAULT 0,
    `added`    timestamp                      NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `uvindex`
(
    `timestamp` timestamp           NOT NULL DEFAULT current_timestamp(),
    `uvindex`   tinyint(3) UNSIGNED NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `wc_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `winddirection`
(
    `degrees`   smallint(5) UNSIGNED     NOT NULL,
    `gust`      smallint(5) UNSIGNED              DEFAULT NULL,
    `timestamp` timestamp                NOT NULL DEFAULT current_timestamp(),
    `device`    char(1) COLLATE utf8_bin NOT NULL,
    `source`    char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `windguru_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `windspeed`
(
    `speedMPH`   float(5, 2)              NOT NULL,
    `gustMPH`    float(5, 2)                       DEFAULT NULL,
    `averageMPH` float(5, 2)                       DEFAULT NULL,
    `timestamp`  timestamp                NOT NULL DEFAULT current_timestamp(),
    `device`     char(1) COLLATE utf8_bin NOT NULL,
    `source`     char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `windy_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `wu_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `openweather_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

ALTER TABLE `5n1_status`
    ADD PRIMARY KEY (`device`);

ALTER TABLE `access_status`
    ADD PRIMARY KEY (`last_update`);

ALTER TABLE `archive`
    ADD PRIMARY KEY (`reported`),
    ADD UNIQUE KEY `tempF` (`reported`, `tempF`),
    ADD UNIQUE KEY `windSmph` (`reported`, `windSpeedMPH`),
    ADD UNIQUE KEY `windDEG` (`reported`, `windDEG`),
    ADD UNIQUE KEY `relH` (`reported`, `relH`),
    ADD UNIQUE KEY `pressureinHg` (`reported`, `pressureinHg`),
    ADD UNIQUE KEY `rainin` (`reported`, `rainin`),
    ADD UNIQUE KEY `total_rainin` (`reported`, `total_rainin`);

ALTER TABLE `atlasLightning`
    ADD PRIMARY KEY (`date`) USING BTREE;

ALTER TABLE `atlas_status`
    ADD PRIMARY KEY (`last_update`);

ALTER TABLE `cwop_updates`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `dailyrain`
    ADD PRIMARY KEY (`date`),
    ADD UNIQUE KEY `archive` (`dailyrainin`, `date`, `last_update`);

ALTER TABLE `generic_updates`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `humidity`
    ADD PRIMARY KEY (`timestamp`),
    ADD UNIQUE KEY `relH` (`relH`, `timestamp`);

ALTER TABLE `light`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `lightningData`
    ADD PRIMARY KEY (`source`);

ALTER TABLE `password_recover`
    ADD PRIMARY KEY (`uid`);

ALTER TABLE `pressure`
    ADD PRIMARY KEY (`timestamp`),
    ADD UNIQUE KEY `inhg` (`inhg`, `timestamp`);

ALTER TABLE `pws_updates`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `rainfall`
    ADD PRIMARY KEY (`device`);

ALTER TABLE `sessions`
    ADD PRIMARY KEY (`device_key`),
    ADD KEY `uid` (`uid`);

ALTER TABLE `system`
    ADD PRIMARY KEY (`name`);

ALTER TABLE `temperature`
    ADD PRIMARY KEY (`timestamp`),
    ADD UNIQUE KEY `tempF` (`timestamp`, `tempF`);

ALTER TABLE `towerLightning`
    ADD PRIMARY KEY (`date`) USING BTREE;

ALTER TABLE `towers`
    ADD PRIMARY KEY (`sensor`),
    ADD UNIQUE KEY `sensor` (`sensor`);

ALTER TABLE `tower_data`
    ADD PRIMARY KEY (`id`),
    ADD KEY `sensor` (`sensor`, `timestamp`);

ALTER TABLE `users`
    ADD PRIMARY KEY (`uid`);

ALTER TABLE `uvindex`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `wc_updates`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `winddirection`
    ADD PRIMARY KEY (`timestamp`),
    ADD UNIQUE KEY `degrees` (`degrees`, `timestamp`);

ALTER TABLE `windspeed`
    ADD PRIMARY KEY (`timestamp`),
    ADD UNIQUE KEY `speedMPH` (`speedMPH`, `timestamp`);

ALTER TABLE `windy_updates`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `wu_updates`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `tower_data`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
    MODIFY `uid` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `password_recover`
    ADD CONSTRAINT `password_recover_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sessions`
    ADD CONSTRAINT `sessions_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tower_data`
    ADD CONSTRAINT `tower_sensor_id` FOREIGN KEY (`sensor`) REFERENCES `towers` (`sensor`) ON DELETE CASCADE ON UPDATE CASCADE;

# Initial Defaults

INSERT INTO `rainfall` (`rainin`, `last_update`)
VALUES ('0.00', '2000-01-01 00:00:00');

INSERT INTO `outage_alert` (`last_sent`, `status`)
VALUES ('2000-01-01 00:00:00', '0');

INSERT INTO `last_update` (`timestamp`)
VALUES ('2000-01-01 00:00:00');

INSERT INTO `5n1_status` (`device`, `battery`, `rssi`, `last_update`)
VALUES ('access', 'normal', '0', '2000-01-01 00:00:00');
INSERT INTO `5n1_status` (`device`, `battery`, `rssi`, `last_update`)
VALUES ('hub', 'normal', '0', '2000-01-01 00:00:00');

INSERT INTO `atlas_status` (`battery`, `rssi`, `last_update`)
VALUES ('normal', '0', '2000-01-01 00:00:00');

INSERT INTO `access_status` (`battery`, `last_update`)
VALUES ('normal', '2000-01-01 00:00:00');

INSERT INTO `system` (`name`, `value`)
VALUES ('latestRelease', '3.1.0');

INSERT INTO `system` (`name`, `value`)
VALUES ('lastUpdateCheck', '2000-01-01 00:00:00');

# Schema Version

INSERT INTO `system` (`name`, `value`)
VALUES ('schema', '3.1');

COMMIT;
