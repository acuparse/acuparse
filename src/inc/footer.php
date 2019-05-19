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
 * File: src/inc/footer.php
 * Page Footer
 */

?>

<!-- JS -->

<script src="/lib/mit/jquery/js/jquery.min.js"></script>
<script src="/lib/mit/bootstrap/js/bootstrap.min.js"></script>
<?php
if ($config->google->recaptcha->enabled === true && ($_SERVER['PHP_SELF'] === '/recover.php' || $_SERVER['PHP_SELF'] === '/contact.php' || $_SERVER['PHP_SELF'] === '/admin/account.php')) {
    ?>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function onSubmit(token) {
            document.getElementById("recaptcha-form").submit();
        }
    </script>
<?php } ?>

<!-- Page Specific Scripts -->
<?php
if (isset($page_footer)) {
    echo $page_footer;
}
?>

<!-- Page Footer -->
<hr>

<footer>
    <div class="row footer-appname">
        <div class="col-auto mx-auto">
            <p>Powered by <a href="<?= $appInfo->homepage; ?>"
                             target='_blank'><strong><?= ucfirst($appInfo->name); ?></strong></a>
                <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && $_SESSION['admin'] === true) { ?>
                    <br><span class="small">Version <?= $config->version->app; ?></span> <?php } ?></p>
        </div>
    </div>
</footer>
</div>
<!-- END Site Container -->

<!-- instant.page prefetch -->
<script src="/lib/mit/instant.page/1.2.2.js" type="module"></script>

</body>
</html>
