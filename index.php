
<?php

    if(basename($_SERVER['REQUEST_URI']) == 'index.php') {
        header('Location: ./');
    }


    include __DIR__ . '/core/loader.php';