<?php
/**
 * Acuparse - AcuRite®‎ Access/smartHUB and IP Camera Data Processing, Display, and Upload.
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
 * File: src/fcn/mailer.php
 * Function to send mail
 */

function mailer($sendTo, $subject, $message, $replyTo = false, $replyToName = false, $disclaimer = true)
{
    // Get the loader
    require(dirname(__DIR__) . '/inc/loader.php');

    $headers = 'MIME-Version: 1.0' . PHP_EOL;
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
    $headers .= 'From: ' . $config->site->name . ' <' . $config->site->email . '>' . PHP_EOL;
    $headers .= 'X-Mailer: ' . $appInfo->name . ' ' . $appInfo->version . PHP_EOL;
    if ($replyTo !== false) {
        $headers .= 'Reply-to: ' . $replyToName . ' <' . $replyTo . '>' . PHP_EOL;
    }

    $messageHeader = "<html><head><title>$subject</title></head><body>";

    $messageDisclaimer = '<p>--<br>
    This is an automated message sent by the ' . $config->site->name . '<br>
    You are receiving this message because your email address was used for an account at ' . $config->site->name . '.<br>
    If you believe this to be an error, please reply to this message.</p>
    <p>You can manage your account details by visiting <a href="http://' . $config->site->hostname . '">' . $config->site->hostname . '</a></p>';

    $messageFooter = '</body></html>';

    if ($disclaimer === false) {
        $message = $messageHeader . $message . $messageFooter;
    } else {
        $message = $messageHeader . $message . $messageDisclaimer . $messageFooter;
    }

    // Make it so
    mail($sendTo, $subject, $message, $headers, '-f' . $config->site->email);
}
