<?php


/* --------------------  COMMENTS ----------------- */

$di->set('CommentController', function() use ($di) {
    $controller = new Anax\Comment\CommentController();
    $controller->setDI($di);
    return $controller;
});

$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/comments_sqlite.php');
    $db->connect();
    return $db;
});


$app->router->add('comment', function() use ($app) {

    $app->theme->setTitle("Welcome to Anax Guestbook");
    $app->views->add('comment/index');

    $app->dispatcher->forward([
        'controller' => 'comment',
        'action'     => 'view',
    ]);

});

$app->router->add('comments-reset', function() use ($app) {

    $app->db->dropTableIfExists('comments')->execute();

    $app->db->createTable(
        'comments',
        [
            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
            'content' => ['text'],
            'mail' => ['varchar(80)'],
            'name' => ['varchar(80)'],
            'web' => ['varchar(80)'],
            'timestamp' => ['datetime'],
        ]
    )->execute();

});
