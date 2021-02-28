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
 * File: src/fcn/mailer.php
 * Function to send system mail
 */

/**
 * @param $sendTo
 * @param $subject
 * @param $message
 * @param $siteName
 * @param $siteEmail
 * @param $mgDomain
 * @param $mgSecret
 * @return bool|mixed
 */

function sendViaMailgun($sendTo, $subject, $message, $siteName, $siteEmail, $mgDomain, $mgSecret): array
{
    $array_data = array(
        'from' => $siteName . ' <' . $siteEmail . '>',
        'to' => '<' . $sendTo . '>',
        'subject' => $subject,
        'html' => $message
    );

    $session = curl_init('https://api.mailgun.net/v3/' . $mgDomain . '/messages');
    curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($session, CURLOPT_USERPWD, 'api:' . $mgSecret);
    curl_setopt($session, CURLOPT_POST, true);
    curl_setopt($session, CURLOPT_POSTFIELDS, $array_data);
    curl_setopt($session, CURLOPT_HEADER, false);
    curl_setopt($session, CURLOPT_ENCODING, 'UTF-8');
    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($session);
    curl_close($session);

    return json_decode($response, true);
}

function mailer($sendTo, $subject, $message, $replyTo = false, $replyToName = false, $disclaimer = true): array
{
    // Get the loader
    require(dirname(__DIR__) . '/inc/loader.php');

    /**
     * @return array
     * @return array
     * @var object $config Global Config
     * @var object $appInfo Global Application Info
     */

    if ($config->mailgun->enabled === false) {
        $headers = 'MIME-Version: 1.0' . PHP_EOL;
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
        $headers .= 'From: ' . $config->site->name . ' <' . 'noreply@' . $config->site->hostname . '>' . PHP_EOL;
        $headers .= 'X-Mailer: ' . $appInfo->name . ' ' . $appInfo->version . PHP_EOL;
        if ($replyTo !== false) {
            $headers .= 'Reply-to: ' . $replyToName . ' <' . $replyTo . '>' . PHP_EOL;
        }
    }

    $messageHeader = '<html lang="en"><head><title>' . $subject . '</title></head><body>';
    $messageFooter = '</body></html>';
    $messageDisclaimer = '<p>--<br>This is an automated message sent by the ' . $config->site->name . '.</p><p>Manage your account details by visiting <a href="https://' . $config->site->hostname . '">' . $config->site->hostname . '</a>.</p>';

    $message = ($disclaimer === false) ?
        $messageHeader . $message . $messageFooter
        :
        $messageHeader . $message . $messageDisclaimer . $messageFooter;

    // Send
    return ($config->mailgun->enabled === true) ?
        sendViaMailgun($sendTo, $subject, $message, $config->site->name, $config->site->email, $config->mailgun->domain,
            $config->mailgun->secret)
        :
        mail($sendTo, $subject, $message, $headers, '-f' . $config->site->email);
}
