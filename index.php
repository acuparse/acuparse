<?php
/**
 * File: index.php
 */

require '/var/www/html/inc/config.php';

// Weather
if (isset($_GET['weather'])) {
    include 'fcn/weather.php';
    get_current_weather();
    die();
}
// End weather

include 'inc/header.php';

?>
<div class="container">
    <div class="row">

        <script type="text/javascript">
            $(document).ready(function() {
                function update() {
                    $.ajax({
                        url: '?weather',
                        success: function(data) {
                            $("#weather").html(data);
                            window.setTimeout(update, 60000);
                        }
                    });
                }
                update();
            });
        </script>
        <div class="col-lg-12">
            <div id="weather"></div>
        </div>
    </div>
</div>

<?php
include 'inc/footer.php';