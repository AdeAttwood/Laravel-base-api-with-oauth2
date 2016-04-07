<?php


/**
 * Careates a instance on oauth2 server
 */
App::singleton('oauth2', function() {
    if (env('DB_CONNECTION', 'sqlite') === 'sqlite' ) {
        $pdo = new PDO('sqlite:' . Config::get('database.connections.sqlite.database')); 
        $storage = new OAuth2\Storage\Pdo($pdo);
        $server = new OAuth2\Server($storage);
    } else {
        $dbData = Config::get('database.connections.mysql');
        $dns =  'mysql:dbname=' . $dbData['database'] . ';host=' . $dbData['host'];
        $userName = $dbData['username'];
        $password = $dbData['password'];
        $pdo = new PDO($dns, $username, $password);
    }
    
    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
    $server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
    
    return $server;
});

Route::post('token', '\App\Extensions\Oauth2@getToken');
Route::get('authorizeClient', 'Oauth2Controller@authorizeClient');

