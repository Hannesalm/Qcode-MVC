<?php

require __DIR__.'/config_with_app.php';

include ('database_test.php');
include ('user.php');

$di->set('QuestionsController', function() use ($di) {
    $controller = new \Anax\Questions\QuestionsController();
    $controller->setDI($di);
    return $controller;
});


$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

$app->theme->configure(ANAX_APP_PATH . 'config/theme-mvc.php');
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar.php');


$app->router->add('', function() use ($app) {
    $app->theme->setTitle("Hem");

    $main = "";
    $app->views->add('project/page', ['content' => $main,], 'column1');

    $app->dispatcher->forward([
        'controller' => 'users',
        'action'     => 'listMostActiveUsers',
    ]);

    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'listMostPopularTags',
    ]);

    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'listLatestQuestions',
    ]);


});

$app->router->add('about', function() use ($app) {
    $app->theme->setTitle("About us");

    $content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->add('project/about', [
        'content' => $content,
    ]);
});

$app->router->add('questions', function() use ($app) {
    $app->theme->setTitle("Hem");


    $app->theme->setTitle("Questions");
    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'list',
    ]);
});

$app->router->add('tags', function() use ($app) {

    $app->theme->setTitle("Tags");

    $app->dispatcher->forward([
        'controller' => 'questions',
        'action'     => 'tags',
    ]);
});

$app->router->add('login', function() use ($app) {
    $app->theme->setTitle("Login");

});


$app->router->handle();
$app->theme->render();
