/**
 * Acuparse - AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2018 Maxwell Power
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
 * Version 2.3
 */

CREATE TABLE IF NOT EXISTS `archive` (
  `reported`       timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `cwop_updates` (
  `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `query`     tinytext COLLATE utf8_bin NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `dailyrain` (
  `date`        date          NOT NULL,
  `dailyrainin` decimal(5, 2) NOT NULL,
  `last_update` timestamp     NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `humidity` (
  `timestamp` timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `relH`      decimal(3, 0) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `pressure` (
  `timestamp` timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `inhg`      decimal(4, 2) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `pws_updates` (
  `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `query`     tinytext COLLATE utf8_bin NOT NULL,
  `result`    tinytext COLLATE utf8_bin NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `rainfall` (
  `rainin`      decimal(5, 2) NOT NULL,
  `last_update` timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `sessions` (
  `uid`        smallint(4)                   NOT NULL,
  `device_key` char(40) COLLATE utf8_bin     NOT NULL,
  `token`      char(40) COLLATE utf8_bin     NOT NULL,
  `user_agent` varchar(255) COLLATE utf8_bin NOT NULL,
  `timestamp`  timestamp                     NOT NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `temperature` (
  `timestamp` timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tempF`     decimal(5, 2) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `towers` (
  `sensor`  char(8) COLLATE utf8_bin      NOT NULL,
  `name`    varchar(255) COLLATE utf8_bin NOT NULL,
  `arrange` tinyint(1)                    NOT NULL,
  `private` tinyint(1)                    NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `tower_data` (
  `id`        int(11)                  NOT NULL,
  `tempF`     decimal(5, 2)            NOT NULL,
  `relH`      tinyint(4)               NOT NULL,
  `timestamp` timestamp                NOT NULL DEFAULT CURRENT_TIMESTAMP
  ON UPDATE CURRENT_TIMESTAMP,
  `sensor`    char(8) COLLATE utf8_bin NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `users` (
  `uid`      smallint(4)                    NOT NULL,
  `username` varchar(32) CHARACTER SET utf8 NOT NULL,
  `email`    varchar(255) COLLATE utf8_bin  NOT NULL,
  `password` varchar(255) COLLATE utf8_bin  NOT NULL,
  `admin`    tinyint(1)                     NOT NULL DEFAULT '0',
  `added`    timestamp                      NOT NULL DEFAULT CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1001
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `winddirection` (
  `timestamp` timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `degrees`   decimal(3, 0) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `windspeed` (
  `timestamp` timestamp     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `speedMPH`  decimal(5, 2) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `wu_updates` (
  `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `query`     tinytext COLLATE utf8_bin NOT NULL,
  `result`    tinytext COLLATE utf8_bin NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `wc_updates` (
  `timestamp` timestamp                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `query`     tinytext COLLATE utf8_bin NOT NULL,
  `result`    tinytext COLLATE utf8_bin NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE IF NOT EXISTS `system` (
  `name`  VARCHAR(255) NOT NULL,
  `value` VARCHAR(255) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `outage_alert` (
  `last_sent` timestamp  NOT NULL,
  `status`    tinyint(1) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `last_update` (
  `timestamp` datetime NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `password_recover` (
  `uid`  smallint(4) NOT NULL,
  `hash` char(32)    NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

ALTER TABLE `archive`
  ADD PRIMARY KEY (`reported`);

ALTER TABLE `cwop_updates`
  ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `dailyrain`
  ADD PRIMARY KEY (`date`),
  ADD UNIQUE KEY `date` (`date`);

ALTER TABLE `humidity`
  ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `pressure`
  ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `pws_updates`
  ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `rainfall`
  ADD PRIMARY KEY (`rainin`);

ALTER TABLE `sessions`
  ADD PRIMARY KEY (`device_key`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `temperature`
  ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `towers`
  ADD PRIMARY KEY (`sensor`),
  ADD UNIQUE KEY `sensor` (`sensor`);

ALTER TABLE `tower_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sensor` (`sensor`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

ALTER TABLE `winddirection`
  ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `windspeed`
  ADD PRIMARY KEY `timestamp` (`timestamp`);

ALTER TABLE `wu_updates`
  ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `wc_updates`
  ADD PRIMARY KEY (`timestamp`);

ALTER TABLE `password_recover`
  ADD PRIMARY KEY (`uid`);

ALTER TABLE `system`
  ADD PRIMARY KEY (`name`);

ALTER TABLE `tower_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `uid` smallint(4) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `tower_data`
  ADD CONSTRAINT `tower_sensor_id` FOREIGN KEY (`sensor`) REFERENCES `towers` (`sensor`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `password_recover`
  ADD CONSTRAINT `password_recover_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

INSERT INTO `rainfall` (`rainin`, `last_update`) VALUES ('0.00', '2018-01-01 00:00:00');

INSERT INTO `outage_alert` (`last_sent`, `status`) VALUES ('2018-01-01 00:00:00', '0');

INSERT INTO `last_update` (`timestamp`) VALUES ('2018-01-01 00:00:00');

INSERT INTO `system` (`name`, `value`) VALUES ('schema', '2.3');
