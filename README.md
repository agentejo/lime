Lime
====

Lime is a micro web framework for quickly creating web applications in PHP with minimal effort.

```php
$app = new Lime\App();

$app->bind("/", function() {
    return "Hello World!";
});

$app->run();
```

Just include one file (~ 35KB) and you're ready to go.


## Routes

In Lime, a route is an HTTP method paired with a URL-matching pattern. Each route is associated with a block:

```php
$app->get("/", function() {
    return "This was a GET request...";
});

$app->post("/", function() {
    return "This was a POST request...";
});

$app->bind("/", function() {
    return "This was a GET or POST request...";
});
```

Routes are matched in the order they are defined. The first route that matches the request is invoked.

Route patterns may include named parameters, accessible via the params hash:

```php
$app->get("/posts/:id/:name", function($params) {
    return $params["id"].'-'.$params["name"];
});

$app->post("/misc/*", function($params) {
    return $params[":splat"];
});

$app->bind("#/pages/(about|contact)#", function($params) {
    return implode("\n", $params[":captures"]);
});
```


## Conditions

Routes may include a variety of matching conditions, such as the user agent:

```php
$app->get("/foo", function() {
    // GET request...
}, strpos($_SERVER['HTTP_USER_AGENT'], "Safari")!==false);
```

## Create Urls

```php
$route = $app->routeUrl('/my/route');
$url   = $app->baseUrl('/assets/script.js');
```

## Templates

In general you can utilize any template engine you want. Lime provides a simple template engine:

```php
$app->get("/", function() {

        $data = array(
            "name"  => 'Frank',
            "title" => 'Template demo'
        );

        return $this->render("views/view.php with views/layout.php", $data);
});
```

views/view.php:

```php
<p>
    Hello <?=$name?>.
</p>
```

views/layout.php:

```php
<!DOCTYPE HTML>
<html lang="en-US">
<head>
        <meta charset="UTF-8">
        <title><?=$title?></title>
</head>
<body>
        <a href="<?=$this->routeUrl('/')?>">Home</a>
        <hr>
        <?php echo $content_for_layout;?>
</body>
</html>
```

## You like OO style?

Just bind a class:

```php
class Pages {

    protected $app;

    public function __construct($app){
        $this->app = $app;
    }

    /*
        accessible via
        /pages or /pages/index
    */
    public function index() {
        return $this->app->render("pages/index.php");
    }

    /*
        accessible via
        /pages/contact
    */
    public function contact() {
        return $this->app->render("pages/contact.php");
    }

    /*
        accessible via
        /pages/welcome/foo
    */
    public function welcome($name) {
        return $this->app->render("pages/welcome.php", array("name"=>$name));
    }
}

// bind Class to map routes
$app->bindClass("Pages");
```

## Registry

Store any values by setting a key to the $app object.

```php
$app["config.mykey"] = array('test' => 123);
```

Path access helper with <code>/</code>

```php
$value = $app["config.mykey/test"]; // will return 123
```

## Paths

Register paths for quicker access

```php
$app->path('views', __DIR__.'/views');

$view = $app->path('views:detail.php');
$view = $app->render('views:detail.php');
```

Gets url to file

```php
$url  = $app->pathToUrl('folder/file.php');
$url  = $app->pathToUrl('view:file.php');
```

## Dependency injection

```php
$app->service("db", function(){

    // object will be lazy created after accessing $app['db']
    $obj = new PDO(...);

    return $obj;

});

$app["db"]->query(...);
```

## Events


```php

// register callback
$app->on("customevent", function(){
    // code to execute on event
}, $priority = 0);

// trigger custom events
$app->trigger("customevent", $params=array());
```

You can utilize three system events: before, after and shutdown

```php
// render custom error pages

$app->on("after", function() {

    switch($this->response->status){
        case "404":
            $this->response->body = $this->render("views/404.php");
            break;
        case "500":
            $this->response->body = $this->render("views/500.php");
            break;
    }
});
```




## Helpers

You can extend Lime by using your custom helpers:

```php
class MyHelperClass extends Lime\Helper {

    public function test(){
        echo "Hello!";
    }
}

$app->helpers["myhelper"] = 'MyHelperClass';

$app("myhelper")->test();
```

#### Build in helpers

**Session**

```php
$app("session")->init($sessionname=null);
$app("session")->write($key, $value);
$app("session")->read($key, $default=null);
$app("session")->delete($key);
$app("session")->destroy();
```

**Cache**

```php
$app("cache")->setCachePath($path);
$app("cache")->write($key, $value, $duration = -1);
$app("cache")->read($key, $default=null);
$app("cache")->delete($key);
$app("cache")->clear();
```
