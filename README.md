Lime
====

Lime is a micro web framework for quickly creating web applications in PHP with minimal effort inspired by sinatra.
    

    $app = new Lime\App();

    $app->bind("/", function() {
        return "Hello World!";
    });

    $app->run();

Just include one file (~ 30KB) and you're ready to go.


## Routes

In Lime, a route is an HTTP method paired with a URL-matching pattern. Each route is associated with a block:


    $app->get("/", function() {
        return "This was a GET request...";
    });

    $app->post("/", function() {
        return "This was a POST request...";
    });

    $app->bind("/", function() {
        return "This was a GET or POST request...";
    });
     

Routes are matched in the order they are defined. The first route that matches the request is invoked.

Route patterns may include named parameters, accessible via the params hash:


    $app->get("/posts/:id/:name", function($params) {
        return $params["id"].'-'.$params["name"];
    });

    $app->post("/misc/*", function($params) {
        return $params[":splat"];
    });

    $app->bind("#/pages/(about|contact)#", function($params) {
        return implode("\n", $params[":captures"]);
    });


## Conditions

Routes may include a variety of matching conditions, such as the user agent:

    $app->get("/foo", function() {
        // GET request...
    }, strpos($_SERVER['HTTP_USER_AGENT'], "Safari")!==false);


## Templates

In general you can utilize any template engine you want. Lime provides a simple template engine:

    $app->get("/", function(){
     
            $data = array(
                "name"  => 'Frank', 
                "title" => 'Template demo'
            );
     
            return $app->render("views/view.php with views/layout.php", $data);
    });

views/view.php:

    <p>
        Hello <?php echo $name;?>.
    </p>

views/layout.php:

    <!DOCTYPE HTML>
    <html lang="en-US">
    <head>
            <meta charset="UTF-8">
            <title><?php echo $title;?></title>
    </head>
    <body>
            <?php echo $content_for_layout;?>
    </body>
    </html>


## You like OO style?

Just bind a class:

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
                    return $app->render("pages/index.php");
            }

            /* 
                    accessible via 
                    /pages/contact
            */
            public function contact() {
                    return $app->render("pages/contact.php");
            }

            /* 
                    accessible via 
                    /pages/welcome/foo
            */
            public function welcome($name) {
                    return $app->render("pages/welcome.php", array("name"=>$name));
            }
    }


    $app->bindClass("Pages");


## Dependency injection

    
    $app->service("db", function(){
        
        $obj = new PDO(...);
        
        return $obj;

    });

    $app["db"]->query(...);


## Events

You can utilize three system events: before, after and shutdown

    // render custom error pages

    $app->on("after", function() use($app){
        
        switch($app->response["status"]){
            case "404":
                $app->response["body"] = $app->render("views/404.php");
                break;
            case "500":
                $app->response["body"] = $app->render("views/500.php");
                break;
        }
    });


## Helpers

You can extend Lime by using your custom helpers:

    class MyHelperClass extend Lime\Helper {
        
        public function test(){
            echo "Hello!"
        }
    }

    $app->helpers["myhelper"] = 'MyHelperClass';

    $app("myhelper")->test();
