<?php
namespace LeagueRouteSample;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BaseController {
    protected $request  = null;
    protected $response = null;
    protected $args     = null;

    /**
     * メソッドにアクセス出来ないときに呼ばれる。
     * つまりprotectedやprivate、メソッドが存在しない時が対象。
     */
    public function __call($method, $args) {
        echo "__call() {$method}<br/>";
        if(!method_exists($this, $method)) {
            throw new Exception("Method doesn't exist");
        }
        // 不変オブジェクトのため、戻り値を次のアクションに渡す必要がある。
        $args = call_user_func_array([$this, 'beforeAction'], $args);
        // メインのアクションメソッド
        $args = call_user_func_array([$this, $method], $args);
        call_user_func_array([$this, 'afterAction'], $args);
    }

    /**
     * 各アクションメソッドが呼ばれる前に呼ばれる
     * 共通の前処理を記述
     */
    public function beforeAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        echo '--- start beforeAction() ---<br />';

        // params()などのヘルパーメソッドのためにオブジェクトを持っておく
        $this->request = $request;
        $this->response  = $response;
        $this->args  = $args;
        return [$request, $response, $args];
    }

    /**
     * 各アクションメソッドが呼ばれる前に呼ばれる
     * 共通の後処理を記述
     */
    public function afterAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        echo '--- start afterAction() ---<br />';
    }

    /**
     * GETとPOSTの両方から値を探す
     */
    protected function params(string $name)
    {
        $post = $this->request->getParsedBody();
        if (array_key_exists($name, $post)) {
            return $post[$name];
        }

        $get = $this->request->getQueryParams();
        if (array_key_exists($name, $get)) {
            return $_GET[$name];
        }
        return false;
    }
}
