<?php

use Silex\Provider\FormServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = new Dotenv\Dotenv('../' . __DIR__);
$dotenv->load();

$app = new Silex\Application();

$app['debug'] = true;
$app['locale'] = 'en';

$app->register(new FormServiceProvider());
$app->register(
    new Silex\Provider\TwigServiceProvider(),
    [
        'twig.path' => __DIR__ . '/../src/Templates/',
        ''
    ]
);
$app->register(
    new Silex\Provider\TranslationServiceProvider(),
    [
        'translator.domains' => [],
    ]
);

// DI
$app['media_wiki'] = function () {
    return new \TYPO3\Hilda\Service\MediaWiki();
};
$app['transformation'] = function () {
    return new \TYPO3\Hilda\Service\Transformation();
};

// Routing
$app->match('/', 'TYPO3\\Hilda\\Controller\\ReleaseNotes::formAction');
$app->match('/generate', 'TYPO3\\Hilda\\Controller\\ReleaseNotes::generateAction');

$app->run();