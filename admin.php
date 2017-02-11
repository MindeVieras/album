<?php


$loader = require 'vendor/autoload.php';
$loader->add('', 'app/classes');

\Photobum\Config::bootstrap();



$f3 = \Base::instance();

$f3->route(
    'GET|POST|PUT|DELETE /admin/@controller',
    function ($f3) {
        if ($f3->get('VERB')=='post') {
            ddd($_POST);
        }
        $controller = sprintf('\Photobum\Admin\%s', ucfirst($f3->get('PARAMS.controller')));
        loadController($controller);
    }
);

$f3->route(
    'GET|POST|PUT|DELETE /admin/@controller/@method',
    function ($f3) {
        $controller = sprintf('\Photobum\Admin\%s', ucfirst($f3->get('PARAMS.controller')));
        $method = $f3->get('PARAMS.method');
        loadController($controller, $method);
    }
);

$f3->route(
    'GET|POST|PUT|DELETE /admin/@controller/@method/*',
    function ($f3) {
        $controller = sprintf('\Photobum\Admin\%s', ucfirst($f3->get('PARAMS.controller')));
        $method = $f3->get('PARAMS.method');
        $paramStr = str_replace(sprintf('/admin2/%s/%s/', $f3->get('PARAMS.controller'), $f3->get('PARAMS.method')), '', $f3->get('PATH'));
        $params = explode('/', $paramStr);
        loadController($controller, $method, $params);
    }
);

$f3->route(
    'GET /admin',
    function () {
        loadController('\Photobum\Admin\Admin', 'home');
    }
);

$f3->run();


function loadController($c, $m = 'view', $params = array())
{
    $keys = array();
    $vals = array();
    foreach ($params as $k => $p) {
        if ($k % 2 == 0) {
            $keys[] = $p;
        } else {
            $vals[] = $p;
        }
    }
    $pArray = array_combine($keys, $vals);

    $f3 = \Base::instance();
    if (!class_exists($c)) {
        $f3->error(404);
    }
    if (!method_exists($c, $m)) {
        $f3->error(404);
    }
    call_user_func(array(new $c, $m), $pArray);
}