<?php
    
    require_once("Lime.php");

    $app = new Lime\App();

    $app->bind("/", function() use($app){
        return "Hello World!";
    });

    $app->run();