<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app->get('/', function () use ($app) {
    return $app->json([
        'app' => 'SSH Keygen API',
        'usage' => [
            'GET /' => 'This info',
            'POST /' => 'Generate Private and Public keys'
        ]
    ]);
});

$app->post('/', function (Request $request) use ($app) {

    $process = new Process("ssh-keygen -b 2048 -t rsa -f ./ssh.key -N '' -q");
    $process->setWorkingDirectory(__DIR__);

    try {
        $process->mustRun();

        $privateKey = file_get_contents('ssh.key');
        $publicKey = file_get_contents('ssh.key.pub');

        unlink('./ssh.key');
        unlink('./ssh.key.pub');
    } catch (\Exception $e) {
        return $app->json([
            'status' => 'error',
            'output' => $process->getOutput(),
            'errorOutput' => $process->getErrorOutput()
        ], 500);
    }

    return $app->json([
        'private' => $privateKey,
        'public' => $publicKey
    ], 201);
});

$app->run();

return $app;
