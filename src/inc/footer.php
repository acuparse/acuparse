<?php
/**
 * Acuparse - AcuRite Access/smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2022 Maxwell Power
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

/**
 * @var mysqli $conn Global MYSQL Connection
 * @var object $config Global Config
 * @var object $appInfo Global Application Info
 * @var boolean $installed Is Acuparse Installed?
 */

if ($installed === true && (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true)) {
    $authenticatedUser = true;
    if ($_SESSION['admin'] === true) {
        $adminUser = true;
    }
}
?>
<!-- Page Footer -->
<hr>

<footer>
    <div class="row footer-appname">
        <div class="col-auto mx-auto">
            <p>Powered by <a href="<?= $appInfo->homepage; ?>"
                             target='_blank'><strong><?= ucfirst($appInfo->name); ?></strong></a>
                <?php
                if (isset($adminUser) && $adminUser === true) {
                    ?>
                    <span class="small">(Version <a target="_blank"
                                                    href="https://www.acuparse.com/releases/v<?= str_replace('.', '-', $config->version->app); ?>"><?= $config->version->app; ?></a>)</span>
                    <?php
                }
                ?>
            </p>
            <?= (isset($authenticatedUser) && $authenticatedUser === true && ($_SERVER['PHP_SELF'] === '/index.php' && empty($_GET))) ? '<p class="small text-muted"><span id="last-updated-timestamp"></span></p>' : NULL; ?>
        </div>
    </div>
</footer>
</div>
<!-- END Site Container -->

<!-- JS -->
<script src="/lib/mit/jquery/js/jquery.min.js"></script>
<script async src="/lib/mit/fontawesome/js/all.min.js"></script>
<script defer src="/lib/mit/bootstrap/js/bootstrap.bundle.min.js"></script>
<script defer src="/lib/mit/instantpage/instantpage.js" type="module"></script>
<?php
if ($config->google->recaptcha->enabled === true && ($_SERVER['PHP_SELF'] === '/recover.php' || $_SERVER['PHP_SELF'] === '/contact.php' || $_SERVER['PHP_SELF'] === '/admin/account.php')) {
    ?>
    <!-- Google reCAPTCHA -->
    <script defer src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function onSubmit(token) {
            document.getElementById("recaptcha-form").submit();
        }
    </script>
<?php }
if ($config->google->analytics->enabled === true) { ?>
    <!-- Google Analytics -->
    <script async
            src="https://www.googletagmanager.com/gtag/js?id=<?= $config->google->analytics->id; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', '<?= $config->google->analytics->id; ?>');
    </script>
<?php }
if ($config->matomo->enabled === true) { ?>
    <!-- Matomo -->
    <script>
        let _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
            let u = "//<?= $config->matomo->domain; ?>/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '<?= $config->matomo->site; ?>']);
            let d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
            g.async = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>
<?php } ?>

<!-- Page Specific Resources -->
<?php
if (isset($page_footer)) {
    echo $page_footer;
} ?>
</body>
</html>
