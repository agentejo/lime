<?php
    
    require_once(__DIR__."/lib/Lime.php");

    $app = new Lime\App();

    $app->bind("/", function() use($app){
        return "Hello World!";
    });

    $app->run();