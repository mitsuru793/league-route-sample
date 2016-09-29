<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BaseController {
    public function __call($method, $args) {
        echo "__call() {$method}<br/>";
        if(!method_exists($this, $method)) {
            throw new Exception("Method doesn't exist");
        }
        $args = call_user_func_array([$this, 'beforeAction'], $args);
        $args = call_user_func_array([$this, $method], $args);
        call_user_func_array([$this, 'afterAction'], $args);
    }

    protected function beforeAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $response->getBody()->write('--- start beforeAction() ---<br />');
        return [$request, $response, $args];
    }

    protected function afterAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
    }
}
