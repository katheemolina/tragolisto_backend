<?php

/** @var \Laravel\Lumen\Routing\Router $router */


use Illuminate\Support\Facades\DB;

$router->options('{any:.*}', function() {
    return response('', 200);
});

$router->group(['prefix' => 'ferni'], function () use ($router) {
    $router->post('/new-chat', 'FerniController@handleChat');
    $router->post('/send-message', 'FerniController@handleChat');
    $router->get('/chats/{userId}', 'FerniController@getChats');
    $router->get('/messages/{chatId}', 'FerniController@getMessages');
});


$router->group(['prefix' => 'api/tragos'], function () use ($router) {
    $router->get('/', 'TragosController@getTragos');
    $router->get('{id}', 'TragosController@getTragoPorID');
    $router->post('/', 'TragosController@crearTrago');
    $router->put('{id}', 'TragosController@actualizarTrago');
    $router->delete('{id}', 'TragosController@eliminarTrago');
});

$router->get('/api/toptragos', 'TragosController@obtenerTop3Favoritos');

$router->group(['prefix' => 'api/ingredientes'], function () use ($router) {
    $router->get('/', 'IngredientesController@obtenerIngredientes');
    $router->get('{id}', 'IngredientesController@obtenerIngredientePorId');
    $router->post('/', 'IngredientesController@crearIngrediente');
    $router->put('{id}', 'IngredientesController@actualizarIngrediente');
    $router->delete('{id}', 'IngredientesController@eliminarIngrediente');
});

$router->group(['prefix' => 'api/favoritos'], function () use ($router) {
    $router->post('/', 'FavoritoController@guardar');
    $router->delete('{favorito_id}', 'FavoritoController@eliminar');
    $router->get('{user_id}', 'FavoritoController@listar');
});

$router->group(['prefix' => 'api/modofiesta'], function () use ($router) {
    $router->get('/', 'JuegoController@modoFiesta');
    $router->get('{id}', 'JuegoController@getJuegoPorID');
    $router->post('/', 'JuegoController@crearJuego');
    $router->put('{id}', 'JuegoController@actualizarJuego');
    $router->delete('{id}', 'JuegoController@eliminarJuego');
});

// $router->post('/ferni', 'FerniController@responder');

$router->post('/login-google', 'LoginGoogleController@login');
$router->post('/verificar-onboarding', 'UserController@verificarOnboarding');
$router->post('/completar-onboarding', 'UserController@completarOnboarding');
$router->get('/usuarios', 'UserController@obtenerUsuarios');

$router->get('/api/indicadores', 'IndicadoresController@obtenerIndicadores');
