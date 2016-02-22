<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->get('/', function () use ($app) {
    return $app->json([
        'SSH Keygen API'
    ]);
});

$app->post('/keys', function (Request $request) use ($app) {
    $process = new Process("ssh-keygen -b 2048 -t rsa -f ./ssh.key -N '' -q");
    $process->run();

    $privateKey = file_get_contents('ssh.key');
    $publicKey = file_get_contents('ssh.key.pub');

    unlink('./ssh.key');
    unlink('./ssh.key.pub');

    // return public key
    return $app->json([
        'private' => $privateKey,
        'public' => $publicKey
    ], 201);
});

$app->run();

return $app;
