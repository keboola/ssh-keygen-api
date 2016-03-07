<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

require_once __DIR__.'/vendor/autoload.php';
// CORS preflight request handle
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
	header('Access-Control-Allow-Headers: content-type, x-requested-with, x-storageapi-token, x-kbc-runid, x-requested-by, x-user-agent, x-kbc-manageapitoken');
	header('Access-Control-Max-Age: 86400'); // preflight timeout
	die();
}

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

    $workingDir = __DIR__;
    $privateKeyFile = $workingDir . '/ssh.key';
    $publicKeyFile = $privateKeyFile . '.pub';

    $process = new Process("ssh-keygen -b 2048 -t rsa -f " . $privateKeyFile . " -N '' -q");

    try {
        $process->mustRun();

        $privateKey = file_get_contents($privateKeyFile);
        $publicKey = file_get_contents($publicKeyFile);

        unlink($privateKeyFile);
        unlink($publicKeyFile);
    } catch (\Exception $e) {
        return $app->json([
            'status' => 'error',
            'output' => $process->getOutput(),
            'errorOutput' => $process->getErrorOutput()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return $app->json([
        'private' => $privateKey,
        'public' => $publicKey
    ], Response::HTTP_CREATED);
});

$app->run();

return $app;
