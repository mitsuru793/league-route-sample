<?php

use League\Route\Strategy\AbstractStrategy;
use League\Route\Strategy\StrategyInterface;
use League\Route\Route;

class CustomStrategy  extends AbstractStrategy implements StrategyInterface
{
    public function dispatch(callable $controller, array $vars, Route $route = null)
    {
        if (is_array($controller)) {
            // アクションメソッドを指定
            $args = call_user_func_array([$controller[0], 'beforeAction'], [
                $this->getRequest(),
                $this->getResponse(),
                $vars
            ]);
            $args = call_user_func_array($controller, $args);
            $response = call_user_func_array([$controller[0], 'afterAction'], $args);
        } else {
            // 無名関数を指定
            $response = call_user_func_array($controller, [
                $this->getRequest(),
                $this->getResponse(),
                $vars
            ]);
        }
        // $responseがnullやpsr7ではない時にresponseを組み立てる
        return $this->determineResponse($response);
    }
}
