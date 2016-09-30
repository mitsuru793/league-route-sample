<?php

echo '--- start index.php---<br />';

require_once "vendor/autoload.php";
require_once './News.php';
require_once './CustomStrategy.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$container = new League\Container\Container;

// PSRを実装したクラスを使って登録。dispatchに渡す専用のBean, Modelみたいなもの？
// dispatch()の時にget()で取り出して渡す。
$container->share('response', Zend\Diactoros\Response::class);
$container->share('request', function () {
    return Zend\Diactoros\ServerRequestFactory::fromGlobals(
        $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
    );
});

$container->share('emitter', Zend\Diactoros\Response\SapiEmitter::class);

$router = new League\Route\RouteCollection($container);

// コントローラーとアクションメソッド名は大文字小文字を区別しませんが、定義元と合わせた方が良い。
$router->map('GET', '/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $response->getBody()->write('<h1>Welcome Index!</h1>');
});

echo '--- start $router->map() ---<br />';
// 先頭の共通パスはグループ化することができます。
$router->group('/news', function ($router) {
    // 複数にマッチする場合は、先に定義されたものが使われます。
    // つまりに先にマッチしたらそこでルーティングは終了です。

    // `/news`でマッチ。末尾にスラッシュがあると駄目
    $router->map('GET', '/', 'News::index');

    // idが数字だけの時マッチ。idはintに変換されずに文字列のまま。
    // パラメーターを[  ]で囲まない場合は必須になる
    $router->map('GET', '/show/{id:number}', 'News::show');

    // wordがアルファベット(大文字・小文字)だけの時マッチ。
    // パラメータを[  ]で囲むとオプション値になる
    // オプションなので`/show/`もマッチする
    $router->map('GET', '/show/[{word:word}]', 'News::show');

    // パラメータのパターンには正規表現が使える
    $router->map('GET', '/show/{regex:user_\d+}', 'News::show');

    $router->map('GET', '/create', 'News::create');
    $router->map('POST', '/create', 'News::create');

    // __callによるbefore/afterActionの呼び出し確認
    $router->map('GET', '/magic-method-call', 'News::magicMethodCall');
});

// CustomStrategyによるbefore/afterActionの呼び出し確認
// setStrategyは前に定義したルーティングにも影響するので、ifを使用。
// group()内では、$routerのクラスが変わるのでsetStrategy()が使えないので、外側で定義。
if ($_SERVER['REQUEST_URI'] === '/news/custom-strategy') {
    $router->setStrategy(new CustomStrategy);
    $router->map('GET', '/news/custom-strategy', 'News::customStrategy');
}

$response = $router->dispatch($container->get('request'), $container->get('response'));

// emitしないと$response->getBody()->write()の内容がブラウザに出力されない。
// emitしなくてもハンドラ内でのechoなどは出力される。
$container->get('emitter')->emit($response);
