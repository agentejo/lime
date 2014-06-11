<?php

    require_once("src/Lime/App.php");

    $app = new Lime\App();

    $app->bind("/", function() use($app){
        return "Hello World!";
    });

    $app->run();