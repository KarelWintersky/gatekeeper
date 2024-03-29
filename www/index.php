<?php
// error_reporting(E_ALL);
// ini_set('display_errors', getenv('IS_DEBUG'));

setlocale(LC_CTYPE, 'ru_RU.UTF8');

use Arris\DB;
use Dotenv\Dotenv;
use Pecee\SimpleRouter\SimpleRouter;

define('PATH_ROOT', dirname(__DIR__, 1));
define('PATH_CONFIG', dirname(__DIR__, 1) . '/config/');

require_once PATH_ROOT . '/vendor/autoload.php';
require_once PATH_ROOT . '/vendor/pecee/simple-router/helpers.php';
require_once PATH_ROOT . '/engine/websun.php';
require_once PATH_ROOT . '/engine/Template.php';
require_once PATH_ROOT . '/engine/functions.php';

Dotenv::create(PATH_CONFIG, '_env')->load();

try {
    DB::init(NULL, [
        'hostname' => getenv('DB_HOST'),
        'database' => getenv('DB_NAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
        'port' => getenv('DB_PORT'),
        'charset' => 'utf8',
        'charset_collate' => 'utf8_general_ci',
    ]);

    SimpleRouter::setDefaultNamespace('Gatehouse\Controllers');

    SimpleRouter::get('/', 'Allotment@page_list');
    SimpleRouter::get('/service', 'Page@page_services');

    SimpleRouter::group(['prefix' => '/ajax'], function (){
        SimpleRouter::get('/add_phone', 'Ajax@action_phone_add');
        SimpleRouter::get('/delete_phone', 'Ajax@action_phone_delete');

        SimpleRouter::get('/add_transport', 'Ajax@action_transport_add');
        SimpleRouter::get('/delete_transport', 'Ajax@action_transport_delete');
    });


    SimpleRouter::group(['prefix' => '/places'], function () {
        SimpleRouter::get('/', 'Allotment@page_list');
        SimpleRouter::get('/add', 'Allotment@form_add');
        SimpleRouter::get('/edit', 'Allotment@form_edit');

        SimpleRouter::get('/manage', 'Allotment@form_manage');

        SimpleRouter::post('/callback_add', 'Allotment@callback_add');
        SimpleRouter::post('/callback_edit', 'Allotment@callback_edit');
        SimpleRouter::get('/callback_delete', 'Allotment@callback_delete');
    });

    SimpleRouter::group(['prefix' => '/transport' ], function () {
        SimpleRouter::get('/', 'Transport@page_list');

        SimpleRouter::get('/add', 'Transport@form_add');
        SimpleRouter::get('/edit', 'Transport@form_edit');

        SimpleRouter::post('/callback_add', 'Transport@callback_add');
        SimpleRouter::post('/callback_edit', 'Transport@callback_edit');
        SimpleRouter::get('/callback_delete', 'Transport@callback_delete');
    });

    if (getenv('IS_DEBUG') == 1) {
        SimpleRouter::error(function (Pecee\Http\Request $request, \Exception $exception){
            d($exception);
            d($request);
            dd('');
        });
    }

    SimpleRouter::start();

} catch (\Exception $exception) {
    if ($exception->getCode() === 404) {
        http_redirect('/404.html');
    } else {
        d($exception);
        dd('');
    }
}


