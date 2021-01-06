<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2021 Maxwell Power
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
 * File: src/inc/templates/updateAvailable.php
 * Update is Available
 */

/** @var string $latestRelease */
?>

<section id="update-message" class="row text-center update-message">
    <div class="col-md-8 col-12 mx-auto">
        <div class="alert alert-warning alert-dismissible">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <h4>Version <?= $latestRelease; ?> is available!</h4>
            <p><a href="https://docs.acuparse.com/UPDATING">See the Update Guide for details!</a></p>
        </div>
    </div>
</section>