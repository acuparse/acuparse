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
 * File: sql/trim/enable.sql
 * Adds the trim rules to MySQL events
 */

SET GLOBAL event_scheduler="ON";

DROP EVENT IF EXISTS `trim_windspeed`;
DROP EVENT IF EXISTS `rebuild_windspeed`;

DROP EVENT IF EXISTS `trim_temperature`;
DROP EVENT IF EXISTS `rebuild_temperature`;

DROP EVENT IF EXISTS `trim_humidity`;
DROP EVENT IF EXISTS `rebuild_humidity`;

DROP EVENT IF EXISTS `trim_winddirecton`;
DROP EVENT IF EXISTS `rebuild_winddirection`;

DROP EVENT IF EXISTS `trim_pressure`;
DROP EVENT IF EXISTS `rebuild_pressure`;

DROP EVENT IF EXISTS `trim_wu_updates`;
DROP EVENT IF EXISTS `rebuild_wu_updates`;

DROP EVENT IF EXISTS `trim_wc_updates`;
DROP EVENT IF EXISTS `rebuild_wc_updates`;

DROP EVENT IF EXISTS `trim_cwop_updates`;
DROP EVENT IF EXISTS `rebuild_cwop_updates`;

DROP EVENT IF EXISTS `trim_pws_updates`;
DROP EVENT IF EXISTS `rebuild_pws_updates`;

DROP EVENT IF EXISTS `trim_windy_updates`;
DROP EVENT IF EXISTS `rebuild_windy_updates`;

DROP EVENT IF EXISTS `trim_generic_updates`;
DROP EVENT IF EXISTS `rebuild_generic_updates`;

DROP EVENT IF EXISTS `trim_tower_data`;
DROP EVENT IF EXISTS `rebuild_tower_data`;

DROP EVENT IF EXISTS `rebuild_archive`;

DROP EVENT IF EXISTS `rebuild_dailyrain`;

DROP EVENT IF EXISTS `flush_query_cache`;

CREATE EVENT `trim_windspeed` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO DELETE FROM `windspeed` WHERE `timestamp` < (NOW() - INTERVAL 24 HOUR);
CREATE EVENT `rebuild_windspeed` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO OPTIMIZE TABLE `archive`;

CREATE EVENT `trim_temperature` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO DELETE FROM `temperature` WHERE `timestamp` < (NOW() - INTERVAL 24 HOUR);
CREATE EVENT `rebuild_temperature` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO OPTIMIZE TABLE `temperature`;

CREATE EVENT `trim_humidity` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO DELETE FROM `humidity` WHERE `timestamp` < (NOW() - INTERVAL 24 HOUR);
CREATE EVENT `rebuild_humidity` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO OPTIMIZE TABLE `humidity`;

CREATE EVENT `trim_winddirecton` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO DELETE FROM `winddirection` WHERE `timestamp` < (NOW() - INTERVAL 24 HOUR);
CREATE EVENT `rebuild_winddirection` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO OPTIMIZE TABLE `winddirection`;

CREATE EVENT `trim_pressure` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO DELETE FROM `pressure` WHERE `timestamp` < (NOW() - INTERVAL 24 HOUR);
CREATE EVENT `rebuild_pressure` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO OPTIMIZE TABLE `pressure`;

CREATE EVENT `trim_wu_updates` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO TRUNCATE TABLE `wu_updates`;
CREATE EVENT `rebuild_wu_updates` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO ALTER TABLE `wu_updates` ENGINE = InnoDB;

CREATE EVENT `trim_wc_updates` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO TRUNCATE TABLE `wc_updates`;
CREATE EVENT `rebuild_wc_updates` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO ALTER TABLE `wc_updates` ENGINE = InnoDB;

CREATE EVENT `trim_cwop_updates` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO TRUNCATE TABLE `cwop_updates`;
CREATE EVENT `rebuild_cwop_updates` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO ALTER TABLE `cwop_updates` ENGINE = InnoDB;

CREATE EVENT `trim_pws_updates` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO TRUNCATE TABLE `pws_updates`;
CREATE EVENT `rebuild_pws_updates` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO ALTER TABLE `pws_updates` ENGINE = InnoDB;

CREATE EVENT `trim_windy_updates` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO TRUNCATE TABLE `windy_updates`;
CREATE EVENT `rebuild_windy_updates` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO ALTER TABLE `windy_updates` ENGINE = InnoDB;

CREATE EVENT `trim_generic_updates` ON SCHEDULE EVERY 1 DAY STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE ENABLE DO TRUNCATE TABLE `generic_updates`;
CREATE EVENT `rebuild_generic_updates` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO ALTER TABLE `generic_updates` ENGINE = InnoDB;

CREATE EVENT `rebuild_tower_data` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO ALTER TABLE `tower_data` ENGINE = InnoDB;

CREATE EVENT `rebuild_archive` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO OPTIMIZE TABLE `archive`;

CREATE EVENT `rebuild_dailyrain` ON SCHEDULE EVERY 1 MONTH STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO OPTIMIZE TABLE `dailyrain`;

CREATE EVENT flush_query_cache ON SCHEDULE EVERY 1 HOUR STARTS '1970-01-01 00:00:00' ON COMPLETION PRESERVE DO FLUSH QUERY CACHE;
