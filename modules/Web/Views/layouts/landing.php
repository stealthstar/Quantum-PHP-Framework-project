<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title ?></title>

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="shortcut icon" href="<?php echo base_url() ?>/assets/images/favicon.ico">
        <link rel='stylesheet' href='<?php echo asset('css/materialize.min.css') ?>' type='text/css' media='screen,projection' />
        <link rel='stylesheet' href='<?php echo asset('css/custom.css') ?>' type='text/css' />
    </head>

    <body>

        <main class="teal accent-4">
            <div class="container wrapper">
                <div class="row">
                    <?php echo view() ?>
                </div>
            </div>

            <ul class="bg-bubbles">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </main>

        <?php echo debugbar() ?>

        <script type='text/javascript' src='<?php echo asset('js/materialize.min.js') ?>'></script>
    </body>
</html>