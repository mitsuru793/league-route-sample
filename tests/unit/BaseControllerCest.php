<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;

use LeagueRouteSample\CustomStrategy;
use LeagueRouteSample\BaseController;

class MyController extends BaseController {
    public function customStrategy(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        return [$request, $response, $args];
    }
}

class BaseControllerCest
{
    public function _before(UnitTester $I)
    {
        $container = new League\Container\Container;
        $container->share('response', Response::class);
        $container->share('emitter', \Zend\Diactoros\Response\SapiEmitter::class);
        $router = $router = new \League\Route\RouteCollection($container);

        $this->router = $router;
        $this->container = $container;
    }

    public function _after(UnitTester $I)
    {
    }

    public function beforeAction(UnitTester $I)
    {
        $this->router->map('GET', '/news/custom-strategy', 'MyController::customStrategy');
        $this->router->setStrategy(new CustomStrategy);
        $request = ServerRequestFactory::fromGlobals()
            ->withUri(new Uri('http://mylocal.net/news/custom-strategy'))
            ->withMethod('GET');
        $response = $this->router->dispatch($request, $this->container->get('response'));
        $I->assertRegExp('/beforeAction/', (string)$response->getBody());
        $I->assertRegExp('/afterAction/', (string)$response->getBody());
    }
}
