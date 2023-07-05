<?php

/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2023 Maxwell Power
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
 * File: src/fcn/dashboard/index.php
 * The Initial Dashboard HTML
 */

/**
 * @var object $config Global Config
 */
?>
<!-- Time Section -->
<section id="system-time" class="system-time">
    <div class="row">
        <div class="col-auto mx-auto">
            <div>
                <p id="system-time-display"></p>
            </div>
        </div>
    </div>
</section>

<!-- Live Weather Section -->
<section id="live-weather">
    <div class="row">
        <div class="col mx-auto text-center">
            <img src="/img/loading/<?= ($config->site->theme === 'twilight') ? 'live-dark' : 'live'; ?>.gif"
                 alt="Loading Data">
            <h3>Crunching numbers ...</h3>
        </div>
    </div>
</section>
