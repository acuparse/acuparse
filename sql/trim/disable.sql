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
 * File: sql/trim/disable.sql
 * Removes the trim rules from MySQL events
 */

SET GLOBAL event_scheduler = "ON";

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

DROP EVENT IF EXISTS `trim_openweather_updates`;
DROP EVENT IF EXISTS `rebuild_openweather_updates`;

DROP EVENT IF EXISTS `trim_generic_updates`;
DROP EVENT IF EXISTS `rebuild_generic_updates`;

DROP EVENT IF EXISTS `trim_light`;
DROP EVENT IF EXISTS `rebuild_light`;

DROP EVENT IF EXISTS `trim_uvindex`;
DROP EVENT IF EXISTS `rebuild_uvindex`;

DROP EVENT IF EXISTS `rebuild_archive`;

DROP EVENT IF EXISTS `rebuild_dailyrain`;

DROP EVENT IF EXISTS `flush_query_cache`;

DROP EVENT IF EXISTS `trim_tower_data`;
DROP EVENT IF EXISTS `rebuild_tower_data`;

SET GLOBAL event_scheduler = "OFF";
