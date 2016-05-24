<?php

$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/database_sqlite.php');
    $db->connect();
    return $db;
});


$app->router->add('users', function() use ($app) {
    $app->theme->setTitle("Användare");
    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'list',
    ]);

});

$app->router->add('add', function() use ($app) {
    $app->theme->setTitle("Add user");

    $form = new \Mos\HTMLForm\CForm();

    $form = $form->create([], [
        'username' => [
            'type'        => 'text',
            'label'       => 'Username/Acronym',
            'required'    => true,
            'validation'  => ['not_empty'],
        ],
        'name' => [
            'type'        => 'text',
            'label'       => 'Name',
            'required'    => true,
            'validation'  => ['not_empty'],
        ],
        'password' => [
            'type'        => 'password',
            'label'       => 'Password',
            'required'    => true,
            'validation'  => ['not_empty'],
        ],
        'email' => [
            'type'        => 'text',
            'required'    => true,
            'validation'  => ['not_empty', 'email_adress'],
        ],
        'submit' => [
            'type'        => 'submit',
            'callback'  => function($form) {
                $form->saveInSession = true;
                return true;
            }
        ],
    ]);

    // Check the status of the form
    $status = $form->check();

    if ($status === true) {

        $app->dispatcher->forward([
            'controller' => 'users',
            'action'     => 'add',
        ]);

    } else if ($status === false) {

        var_dump('Check method returned false');
        die;
    }


    $app->views->add('me/page', [
        'title' => 'Add user',
        'content' => $form->getHTML()
    ]);
});