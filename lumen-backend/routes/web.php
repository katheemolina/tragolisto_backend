<?php

/** @var \Laravel\Lumen\Routing\Router $router */


use Illuminate\Support\Facades\DB;

$router->post('/ferni', 'FerniController@responder');

$router->group(['prefix' => 'ferni'], function () use ($router) {
    $router->post('/new-chat', 'FerniController@newChat');
    $router->post('/send-message', 'FerniController@sendMessage');
    $router->get('/chats/{userId}', 'FerniController@getChats');
    $router->get('/messages/{chatId}', 'FerniController@getMessages');
});

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

$router->post('/ferni', 'FerniController@responder');

$router->post('/login-google', 'LoginGoogleController@login');
$router->post('/verificar-onboarding', 'UserController@verificarOnboarding');
$router->post('/completar-onboarding', 'UserController@completarOnboarding');
