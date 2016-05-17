<?php
/**
 * File: nav.php
 */

?>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><?php echo $site_name; ?></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li <?php if ($_SERVER['PHP_SELF'] == '/index.php'){echo 'class="active"';} ?>>
                    <a href="/"><i class="wi wi-thermometer"></i> Live Weather</a>
                </li>
                <li <?php if ($_SERVER['PHP_SELF'] == '/archive.php'){echo 'class="active"';} ?>>
                    <a href="archive"><i class="fa  fa-archive"></i> Archives</a>
                </li>
            </ul>
        </div>
    </div>
</nav>