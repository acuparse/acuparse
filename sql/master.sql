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

CREATE DATABASE IF NOT EXISTS `acuparse` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `acuparse`;

DROP TABLE IF EXISTS `5n1_status`;
CREATE TABLE `5n1_status`
(
    `device`      varchar(6) COLLATE utf8_bin NOT NULL,
    `battery`     varchar(6) COLLATE utf8_bin NOT NULL,
    `rssi`        tinyint(1)                  NOT NULL,
    `last_update` timestamp                   NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `access_status`;
CREATE TABLE `access_status`
(
    `battery`     varchar(6) COLLATE utf8_bin NOT NULL,
    `last_update` timestamp                   NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `archive`;
CREATE TABLE `archive`
(
    `reported`       timestamp     NOT NULL DEFAULT current_timestamp(),
    `tempF`          decimal(5, 2) NOT NULL,
    `feelsF`         decimal(5, 2) NOT NULL,
    `windSmph`       decimal(5, 2) NOT NULL,
    `windSmph_avg2m` decimal(5, 2) NOT NULL,
    `windDEG`        decimal(3, 0) NOT NULL,
    `windDEG_avg2m`  decimal(3, 0) NOT NULL,
    `relH`           decimal(3, 0) NOT NULL,
    `pressureinHg`   decimal(4, 2) NOT NULL,
    `dewptF`         decimal(5, 2) NOT NULL,
    `rainin`         decimal(5, 2) NOT NULL,
    `total_rainin`   decimal(5, 2) NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `atlas_status`;
CREATE TABLE `atlas_status`
(
    `battery`     varchar(6) COLLATE utf8_bin NOT NULL,
    `rssi`        tinyint(1)                  NOT NULL,
    `last_update` timestamp                   NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `cwop_updates`;
CREATE TABLE `cwop_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `dailyrain`;
CREATE TABLE `dailyrain`
(
    `dailyrainin` decimal(5, 2)            NOT NULL,
    `date`        date                     NOT NULL,
    `last_update` timestamp                NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `device`      char(1) COLLATE utf8_bin NOT NULL,
    `source`      char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `generic_updates`;
CREATE TABLE `generic_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `humidity`;
CREATE TABLE `humidity`
(
    `relH`      decimal(3, 0)            NOT NULL,
    `device`    char(1) COLLATE utf8_bin NOT NULL,
    `source`    char(1) COLLATE utf8_bin NOT NULL,
    `timestamp` timestamp                NOT NULL DEFAULT current_timestamp()
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `last_update`;
CREATE TABLE `last_update`
(
    `timestamp` datetime NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `light`;
CREATE TABLE `light`
(
    `timestamp`              timestamp                   NOT NULL DEFAULT current_timestamp(),
    `lightintensity`         varchar(7) COLLATE utf8_bin NOT NULL,
    `measured_light_seconds` varchar(6) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `lightning`;
CREATE TABLE `lightning`
(
    `timestamp`            timestamp                   NOT NULL DEFAULT current_timestamp(),
    `strikecount`          varchar(3) COLLATE utf8_bin NOT NULL,
    `interference`         tinyint(1)                  NOT NULL,
    `last_strike_ts`       datetime                    NOT NULL,
    `last_strike_distance` varchar(2) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `outage_alert`;
CREATE TABLE `outage_alert`
(
    `last_sent` timestamp  NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `status`    tinyint(1) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `password_recover`;
CREATE TABLE `password_recover`
(
    `uid`  int(4)                    NOT NULL,
    `hash` char(32) COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `pressure`;
CREATE TABLE `pressure`
(
    `inhg`      decimal(4, 2)            NOT NULL,
    `timestamp` timestamp                NOT NULL DEFAULT current_timestamp(),
    `device`    char(1) COLLATE utf8_bin NOT NULL,
    `source`    char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `pws_updates`;
CREATE TABLE `pws_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `rainfall`;
CREATE TABLE `rainfall`
(
    `rainin`      decimal(5, 2)            NOT NULL,
    `last_update` timestamp                NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `device`      char(1) COLLATE utf8_bin NOT NULL,
    `source`      char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`
(
    `uid`        int(4)                        NOT NULL,
    `device_key` char(40) COLLATE utf8_bin     NOT NULL,
    `token`      char(40) COLLATE utf8_bin     NOT NULL,
    `user_agent` varchar(255) COLLATE utf8_bin NOT NULL,
    `timestamp`  timestamp                     NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `system`;
CREATE TABLE `system`
(
    `name`  varchar(255) COLLATE utf8_bin NOT NULL,
    `value` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `temperature`;
CREATE TABLE `temperature`
(
    `timestamp` timestamp                NOT NULL DEFAULT current_timestamp(),
    `tempF`     decimal(5, 2)            NOT NULL,
    `heatindex` decimal(5, 2)                     DEFAULT NULL,
    `feelslike` decimal(5, 2)                     DEFAULT NULL,
    `windchill` decimal(5, 2)                     DEFAULT NULL,
    `dewptf`    decimal(5, 2)                     DEFAULT NULL,
    `device`    char(1) COLLATE utf8_bin NOT NULL,
    `source`    char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `towers`;
CREATE TABLE `towers`
(
    `sensor`  char(8) COLLATE utf8_bin      NOT NULL,
    `name`    varchar(255) COLLATE utf8_bin NOT NULL,
    `arrange` tinyint(1)                    NOT NULL DEFAULT 0,
    `private` tinyint(1)                    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `tower_data`;
CREATE TABLE `tower_data`
(
    `id`        int(11)                     NOT NULL,
    `tempF`     decimal(5, 2)               NOT NULL,
    `relH`      decimal(3, 0)               NOT NULL,
    `battery`   varchar(6) COLLATE utf8_bin NOT NULL,
    `rssi`      tinyint(1)                  NOT NULL,
    `timestamp` timestamp                   NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `sensor`    char(8) COLLATE utf8_bin    NOT NULL,
    `device`    char(1) COLLATE utf8_bin    NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `uid`      int(4)                         NOT NULL,
    `username` varchar(32) CHARACTER SET utf8 NOT NULL,
    `email`    varchar(255) COLLATE utf8_bin  NOT NULL,
    `password` varchar(255) COLLATE utf8_bin  NOT NULL,
    `admin`    tinyint(1)                     NOT NULL DEFAULT 0,
    `added`    timestamp                      NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `uvindex`;
CREATE TABLE `uvindex`
(
    `timestamp` timestamp                   NOT NULL DEFAULT current_timestamp(),
    `uvindex`   varchar(2) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `wc_updates`;
CREATE TABLE `wc_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `winddirection`;
CREATE TABLE `winddirection`
(
    `degrees`   decimal(3, 0)            NOT NULL,
    `gust`      decimal(3, 0)                     DEFAULT NULL,
    `timestamp` timestamp                NOT NULL DEFAULT current_timestamp(),
    `device`    char(1) COLLATE utf8_bin NOT NULL,
    `source`    char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `windspeed`;
CREATE TABLE `windspeed`
(
    `speedMPH`   decimal(5, 2)            NOT NULL,
    `gustMPH`    decimal(5, 2)                     DEFAULT NULL,
    `averageMPH` decimal(5, 2)                     DEFAULT NULL,
    `timestamp`  timestamp                NOT NULL DEFAULT current_timestamp(),
    `device`     char(1) COLLATE utf8_bin NOT NULL,
    `source`     char(1) COLLATE utf8_bin NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `windy_updates`;
CREATE TABLE `windy_updates`
(
    `timestamp` timestamp                 NOT NULL DEFAULT current_timestamp(),
    `query`     tinytext COLLATE utf8_bin NOT NULL,
    `result`    tinytext COLLATE utf8_bin NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

DROP TABLE IF EXISTS `wu_updates`;
CREATE TABLE `wu_updates`
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
    ADD UNIQUE KEY `windSmph` (`reported`, `windSmph`),
    ADD UNIQUE KEY `windDEG` (`reported`, `windDEG`),
    ADD UNIQUE KEY `relH` (`reported`, `relH`),
    ADD UNIQUE KEY `pressureinHg` (`reported`, `pressureinHg`),
    ADD UNIQUE KEY `rainin` (`reported`, `rainin`),
    ADD UNIQUE KEY `total_rainin` (`reported`, `total_rainin`);

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

ALTER TABLE `lightning`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `password_recover`
    ADD PRIMARY KEY (`uid`);

ALTER TABLE `pressure`
    ADD PRIMARY KEY (`timestamp`),
    ADD UNIQUE KEY `inhg` (`inhg`, `timestamp`);

ALTER TABLE `pws_updates`
    ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `rainfall`
    ADD PRIMARY KEY (`device`) USING BTREE;

ALTER TABLE `sessions`
    ADD PRIMARY KEY (`device_key`),
    ADD KEY `uid` (`uid`);

ALTER TABLE `system`
    ADD PRIMARY KEY (`name`);

ALTER TABLE `temperature`
    ADD PRIMARY KEY (`timestamp`),
    ADD UNIQUE KEY `tempF` (`timestamp`, `tempF`);

ALTER TABLE `towers`
    ADD PRIMARY KEY (`sensor`),
    ADD UNIQUE KEY `sensor` (`sensor`);

ALTER TABLE `tower_data`
    ADD PRIMARY KEY (`id`),
    ADD KEY `sensor` (`sensor`, `timestamp`) USING BTREE;

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
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
    MODIFY `uid` int(4) NOT NULL AUTO_INCREMENT;

ALTER TABLE `password_recover`
    ADD CONSTRAINT `password_recover_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sessions`
    ADD CONSTRAINT `sessions_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `tower_data`
    ADD CONSTRAINT `tower_sensor_id` FOREIGN KEY (`sensor`) REFERENCES `towers` (`sensor`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `rainfall` (`rainin`, `last_update`)
VALUES ('0.00', '1970-01-01 00:00:00');

INSERT INTO `outage_alert` (`last_sent`, `status`)
VALUES ('1970-01-01 00:00:00', '0');

INSERT INTO `last_update` (`timestamp`)
VALUES ('1970-01-01 00:00:00');

INSERT INTO `5n1_status` (`device`, `battery`, `rssi`, `last_update`)
VALUES ('access', 'normal', '0', '1970-01-01 00:00:00');
INSERT INTO `5n1_status` (`device`, `battery`, `rssi`, `last_update`)
VALUES ('hub', 'normal', '0', '1970-01-01 00:00:00');

INSERT INTO `atlas_status` (`battery`, `rssi`, `last_update`)
VALUES ('normal', '0', '1970-01-01 00:00:00');

INSERT INTO `access_status` (`battery`, `last_update`)
VALUES ('normal', '1970-01-01 00:00:00');

INSERT INTO `system` (`name`, `value`)
VALUES ('schema', '3.0');

COMMIT;
