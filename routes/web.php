<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|

Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
    var_dump($query->sql); // Dumps sql
    var_dump($query->bindings); //Dumps data passed to query
    var_dump($query->time); //Dumps time sql took to be processed
});
*/
$router->get('users','UserController@listAllUsers');





$router->get('bazaar/{id}/items','BazaarController@listAllItems');
$router->get('bazaar/{id}/users/{user_id}/items','BazaarController@listItemsOfUser');

$router->POST('register', array( 'uses' => 'RegistrationController@store'));
$router->GET('register/verify/{confirmation_code}', array( 'uses' => 'RegistrationController@confirm'));

$router->group(['prefix' => 'api'], function() use ($router)
{
    $router->get('users/{id}', ['middleware' => 'auth', 'uses' =>  'UserController@show']);
    $router->POST('users', 'RegistrationController@registerUser');
    $router->get('users','UserController@listAllUsers');
    $router->POST('bazaar','BazaarController@addBazaar');
    $router->get('bazaar','BazaarController@listAllUpcoming');
    $router->get('bazaar/{id}/sales','BazaarController@getSales');
    $router->POST('/authenticate', 'AuthController@loginPost');
    $router->get('bazaar/upcoming/sale_number/{id}','BazaarController@checkSaleNumberExists');
    $router->get('bazaar/upcoming/sale_numbers','BazaarController@listRegisteredSaleNumbers');
    $router->get('bazaar/upcoming/sale_numbers_available','BazaarController@listSaleNumbers');
    $router->get('bazaar/upcoming/users','BazaarController@listAllUsersUpcoming');
    $router->POST('bazaar/upcoming/user', 'RegistrationController@createUserInUpcomingBasar');
    $router->PUT('bazaar/upcoming/user', 'RegistrationController@changeUserInUpcomingBasar');
    $router->DELETE('bazaar/upcoming/user/{salenumber}', 'RegistrationController@deleteUserInUpcomingBasar');

    $router->GET('receipt', 'ReceiptController@getAllReceipts');
    $router->POST('receipt', 'ReceiptController@createReceipt');
    $router->POST('receipt/{id}/settle', 'ReceiptController@settleReceipt');
    $router->POST('receipt/{id}/item', 'ReceiptController@addItem');
    $router->GET('receipt/{id}/item', 'ReceiptController@getReceiptItems');
    $router->DELETE('receipt/{receiptId}/item/{itemId}','ReceiptController@delItem');
    $router->PUT('receipt/{receiptId}/item/{itemId}','ReceiptController@editItem');

    $router->GET('settlement', 'ReceiptController@processSettlement');

});