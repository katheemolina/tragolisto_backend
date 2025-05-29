<?php

/** @var \Laravel\Lumen\Routing\Router $router */


use Illuminate\Support\Facades\DB;


$router->group(['prefix' => 'api/tragos'], function () use ($router) {
    $router->get('/', 'TragosController@getTragos');
    $router->get('{id}', 'TragosController@getTragoPorID');
});

$router->group(['prefix' => 'api/favoritos'], function () use ($router) {
    $router->post('/', 'FavoritoController@guardar');
    $router->delete('{favorito_id}', 'FavoritoController@eliminar');
    $router->get('{user_id}', 'FavoritoController@listar');
});

$router->group(['prefix' => 'api/modofiesta'], function () use ($router) {
    $router->get('/', 'JuegoController@modoFiesta');
    $router->get('{id}', 'JuegoController@getJuegoPorID');
});