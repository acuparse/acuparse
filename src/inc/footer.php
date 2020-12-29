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
 * File: src/inc/footer.php
 * Page Footer
 */

/** @var mysqli $conn Global MYSQL Connection */
/**
 * @return array
 * @var object $config Global Config
 */
/**
 * @return array
 * @var object $appInfo Global Application Info
 */

?>
<!-- Page Footer -->
<hr>

<footer>
    <div class="row footer-appname">
        <div class="col-auto mx-auto">
            <p>Powered by <a href="<?= $appInfo->homepage; ?>"
                             target='_blank'><strong><?= ucfirst($appInfo->name); ?></strong></a>
                <?php
                /** @var string $installed */
                if ($installed === true) {
                if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && $_SESSION['admin'] === true) {
                    ?>
                    <span class="small">(Version <a target="_blank"
                                                    href="https://github.com/acuparse/acuparse/tree/v<?= $config->version->app; ?>"><?= $config->version->app; ?></a>)</span>
                <?php }
                $lastUpdate = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `timestamp` FROM `last_update`")); ?>
                <br><span class="small">Last update: <?= $lastUpdate['timestamp']; ?></span></p>
            <?php } ?>
        </div>
    </div>
</footer>
</div>
<!-- END Site Container -->

<!-- JS -->
<script src="/lib/mit/jquery/js/jquery.min.js"></script>
<script async src="/lib/mit/fontawesome/js/all.min.js"></script>
<script defer src="/lib/mit/bootstrap/js/bootstrap.min.js"></script>
<script defer src="/lib/mit/instantpage/instantpage.js" type="module"></script>
<?php
if ($config->google->recaptcha->enabled === true && ($_SERVER['PHP_SELF'] === '/recover.php' || $_SERVER['PHP_SELF'] === '/contact.php' || $_SERVER['PHP_SELF'] === '/admin/account.php')) {
    ?>
    <script defer src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function onSubmit(token) {
            document.getElementById("recaptcha-form").submit();
        }
    </script>
<?php }
if ($config->google->analytics->enabled === true) { ?>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $config->google->analytics->id; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '<?= $config->google->analytics->id; ?>');
    </script>
<?php } ?>

<!-- Page Specific Scripts -->
<?php
if (isset($page_footer)) {
    echo $page_footer;
}
?>

</body>
</html>
