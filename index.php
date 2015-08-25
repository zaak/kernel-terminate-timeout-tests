GARBAGE <?php

use Symfony\Component\HttpFoundation\Request;
use Test\ControllerResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernel;
use Test\Response;
use Symfony\Component\HttpKernel\KernelEvents;

require_once __DIR__ . '/vendor/autoload.php';

$request = Request::createFromGlobals();
$dispatcher = new EventDispatcher();

function getMicrotime() {
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}

function dumbSleep($delay) {
    $endTime = getMicrotime() + $delay;
    while(getMicrotime() < $endTime) {}
}

$tests = [
    'execution-after-terminate' => function() use ($dispatcher) {
        $dispatcher->addListener(KernelEvents::TERMINATE, function() {
            $startTime = time();
            $counter = 1;
            while ($counter < 10) {
                file_put_contents(__DIR__ . '/logs/' . date('H_i_s', $startTime), $counter++ . "\n", FILE_APPEND);
                dumbSleep(1);
            }
        });

        Response::closeOutputBuffers(0, false);

        $response = Response::create('kernel.terminate test ' . time());

        return $response;
    },
    'timeout' => function() use ($dispatcher) {
        $counter = 1;

        Response::closeOutputBuffers(0, false);
        while($counter++ < 15) {
            echo ' ';
            ob_end_flush();
            flush();
            sleep(1);
        }

        $response = Response::create('timeout test ' . time());

        return $response;
    }
];

$resolver = new ControllerResolver($tests);
$kernel = new HttpKernel($dispatcher, $resolver);
$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);
