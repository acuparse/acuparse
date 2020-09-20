<?php
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
 * File: src/inc/templates/dbConnectFailed.php
 * Database Connection Error
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Error 503: MySQL Connection Failed</title>
    <style>
        body {
            text-align: center;
            padding: 150px;
        }

        h1 {
            font-size: 50px;
        }

        body {
            font: 20px Helvetica, sans-serif;
            color: #333;
        }

        article {
            display: block;
            text-align: left;
            width: auto;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<article>
    <div>
        <h1>503 - MySQL Connection Failed</h1>
        <p>Unable to connect to your MySQL server. Either your MySQL server is not started or your
            credentials are incorrect.</p>
        <p>Check your configuration and that the MySQL service is started, then try again.</p>
    </div>
    <div>
        <p><Strong>Error Details:</Strong></p>
        <pre><?= mysqli_connect_error() ?></pre>
        </p>
    </div>
</article>
</body>
</html>
