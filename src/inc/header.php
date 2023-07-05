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
 * File: src/inc/header.php
 * Build the main site's header
 */

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var object $appInfo Global Application Info
 * @var boolean $installed
 * @var string $pageTitle Page Title
 */
$pageTitle = ($installed === true) ? $pageTitle . ' | ' . $config->site->name . ' | ' . $config->site->location : $pageTitle;
header('Content-Type: text/html; charset=UTF-8');
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="handheldfriendly" content="true">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?= $config->site->desc; ?>">
        <meta name="keywords" content="weather, <?= strtolower($config->site->location); ?>">

        <!-- Open Graph -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="<?= $config->site->name; ?>">
        <meta property="og:title" content="<?= $pageTitle ?>">
        <meta property="og:description" content="<?= $config->site->desc; ?>">
        <meta property="og:url" content="<?= $config->site->hostname; ?>">
        <meta property="og:image"
              content="<?= ($config->camera->enabled === true) ? '/img/cam/latest.jpg?' . time() : '/img/social.jpg'; ?>">

        <!-- Twitter -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:description" content="<?= $config->site->desc; ?>">
        <meta name="twitter:title" content="<?= $config->site->name; ?>">
        <meta name="twitter:image"
              content="<?= ($config->camera->enabled === true) ? '/img/cam/latest.jpg?' . time() : '/img/social.jpg'; ?>">

        <title><?= $pageTitle ?></title>

        <!-- CSS -->
        <?php
        if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], "chrome") > 0) { ?>
            <link rel="preload" href="/lib/mit/bootstrap/css/bootstrap.min.css" as="style"
                  onload="this.onload=null;this.rel='stylesheet'">
            <link rel="preload" href="/lib/mit/weather_icons/css/weather-icons.min.css" as="style"
                  onload="this.onload=null;this.rel='stylesheet'">
            <link rel="preload" href="/lib/mit/weather_icons/css/weather-icons-wind.min.css" as="style"
                  onload="this.onload=null;this.rel='stylesheet'">
            <noscript>
                <link rel="stylesheet" href="/lib/mit/bootstrap/css/bootstrap.min.css">
                <link href="/lib/mit/weather_icons/css/weather-icons.min.css" rel="stylesheet">
                <link href="/lib/mit/weather_icons/css/weather-icons-wind.min.css" rel="stylesheet">
            </noscript>
        <?php } else { ?>
            <link rel="stylesheet" href="/lib/mit/bootstrap/css/bootstrap.min.css">
            <link href="/lib/mit/weather_icons/css/weather-icons.min.css" rel="stylesheet">
            <link href="/lib/mit/weather_icons/css/weather-icons-wind.min.css" rel="stylesheet">
        <?php }

        if (($_SERVER['PHP_SELF'] === '/camera.php') && (isset($_GET['archive']))) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], "chrome") > 0) { ?>
                <link rel="preload" href="/lib/mit/lightbox/css/lightbox.min.css" as="style"
                      onload="this.onload=null;this.rel='stylesheet'">
                <noscript>
                    <link href="/lib/mit/lightbox/css/lightbox.min.css" rel="stylesheet">
                </noscript>
            <?php } else { ?>
                <link href="/lib/mit/lightbox/css/lightbox.min.css" rel="stylesheet">
                <?php
            }
        }
        if (($_SERVER['PHP_SELF'] === '/admin/tower.php') && (isset($_GET['view']))) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], "chrome") > 0) { ?>
                <link rel="preload" href="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.min.css" as="style"
                      onload="this.onload=null;this.rel='stylesheet'">
                <link rel="preload" href="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.structure.min.css" as="style"
                      onload="this.onload=null;this.rel='stylesheet'">
                <noscript>
                    <link href="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet">
                    <link href="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.structure.min.css" rel="stylesheet">
                </noscript>
            <?php } else {
                ?>
                <link href="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet">
                <link href="/lib/mit/jquery-ui-1.12.1.custom/jquery-ui.structure.min.css" rel="stylesheet">
                <?php
            }
        } ?>

        <!-- Base CSS -->
        <link href="/themes/base.min.css" rel="stylesheet">
        <!-- Site Theme -->
        <link href="/themes/<?= $config->site->theme; ?>.min.css" rel="stylesheet">

        <?php
        if ($_SERVER['PHP_SELF'] === '/index.php') { ?>
            <!-- Structured Data -->
            <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "<?= $config->site->name; ?>",
            "description": "<?= $config->site->desc; ?>",
            "url": "https://<?= $config->site->hostname; ?>/"
        }


            </script>
        <?php } ?>
    </head>
<body>

    <!-- Site Container -->
<div class="container">

    <!-- Navigation -->
<?php if ($_SERVER['PHP_SELF'] !== '/display.php') {
    include 'nav.php';
} ?>

<?php
// Messages
if (isset($_SESSION['messages'])) {
    echo '<div id="system-messages" class="row system-messages"><br><div class="col-md-8 col-12 mx-auto"><strong>', $_SESSION['messages'], '</strong></div></div>';
    unset($_SESSION['messages']);
}
// Logged in admin
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && $_SESSION['admin'] === true) {

    // Check if code updated
    if ($_SERVER['PHP_SELF'] === '/index.php' || $_SERVER['PHP_SELF'] === '/admin/index.php') {
        $result = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT `value` FROM `system` WHERE `name`='schema'"));
        $schema = $result['value'];
        if ((version_compare($schema, $appInfo->schema, '>')) || (version_compare($appInfo->version, $config->version->app, '>'))) {
            header("Location: /admin/install/?update");
            die();
        } else {
            // Check database and notify if update available
            if ($config->site->updates === true) {
                $result = mysqli_fetch_assoc(mysqli_query($conn,
                    "SELECT `value` FROM `system` WHERE `name`='latestRelease'"));
                $latestRelease = $result['value'];
                if ($latestRelease !== null) {
                    if (version_compare($appInfo->version, $latestRelease, '<')) {
                        include 'templates/updateAvailable.php';
                    }
                }
            }
        }
    }
}
