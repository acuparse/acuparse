<?php
/**
 * Acuparse - AcuRite®‎ smartHUB and IP Camera Data Processing, Display, and Upload.
 * @copyright Copyright (C) 2015-2017 Maxwell Power
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

?>
<!-- JS -->

<?php
if ($config->google->recaptcha->enabled === true && ($_SERVER['PHP_SELF'] === '/recover.php' || $_SERVER['PHP_SELF'] === '/admin/account.php')) {
    ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function onSubmit(token) {
            document.getElementById("recaptcha-form").submit();
        }
    </script>
    <?php
}

if ($config->google->analytics->enabled === true) { ?>
    <!-- Google Analytics -->
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', '<?= $config->google->analytics->id; ?>', 'auto');
        ga('send', 'pageview');
    </script>
<?php } ?>
<script type="text/javascript" src="/lib/jquery/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/lib/bootstrap/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        function update() {
            $.ajax({
                url: '/?time',
                success: function (data) {
                    $("#time").html(data);
                    window.setTimeout(update, 1000);
                }
            });
        }

        update();
    });
</script>
<!-- Page Specific Scripts -->
<?php
if (isset($page_footer)) {
    echo $page_footer;
}
?>
<!-- Page Footer -->
<footer id="footer_display" class="footer_display">
    <hr>
    <div class="row footer_appinfo">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <?php $app_name = ucfirst($app_info->name); ?>
            Powered by <a href="<?= $app_info->homepage; ?>" target='_blank'><strong><?= $app_name; ?></strong></a>
            <?php if (isset($_SESSION['UserLoggedIn']) && $_SESSION['UserLoggedIn'] === true && $_SESSION['IsAdmin'] === true) { ?>
                <br> Version <?= $config->version->app;
            } ?>
        </div>
    </div>
</footer>
<!-- END Page Container -->
</div>

</body>
</html>
