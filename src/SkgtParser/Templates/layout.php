<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?= $this->e($title) ?></title>
        <meta name="description" content="">
        <meta name="author" content="">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/skeleton.css">
        <link rel="stylesheet" href="css/main.css">

        <script type="text/javascript" src="js/main.js"></script>

        <link rel="icon" type="image/png" href="images/favicon.png">

    </head>
    <body>
        <div class="container">
            <?= $this->section('content') ?>
        </div>
    </body>
</html>

