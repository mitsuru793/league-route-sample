<?

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
require_once './BaseController.php';

class News extends BaseController
{
    /**
     * BaseController#before/afterActionを試すためのメソッドです。
     * __callを呼び出すためにアクションメソッドをpublicではなく、protectedにする必要があります。
     * privateにすると呼び出しがループしてスタックオーバーになります。
     */
    protected function beforeAfterAction(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        echo '--- start $News->beforeAfterAction() ---<br />';
        echo '<h1>beforeAfterAction</h1>';
        echo '<h3>params()</h3>';
        var_dump($this->params('get'));
        var_dump($this->params('post'));
        // afterActionに渡すために戻り値が必要です。
        return [$request, $response, $args];
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // writeしたものより先にechoしたものがブラウザに描画されます。
        $response->getBody()->write('<h1>index</h1>');
        echo 'from echo<br />';
        ?> from raw html<br /> <?
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $response->getBody()->write('<h1>show</h1>');
        $arg_keys = ['id', 'word', 'regex'];
        foreach ($arg_keys as $key) {
            if (isset($args[$key])) {
                $response->getBody()->write("{$key}: {$args[$key]}");
            }
        }
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $response->getBody()->write('<h1>contact</h1>');

        $response->getBody()->write('<h2>input data</h2>');
        $response->getBody()->write('
        <form action="/news/create" method="post">
            <input type="text" name="name">
            <input type="text" name="title">
            <input type="submit" value="送信">
        </form>');

        $response->getBody()->write('<h2>submit data</h2>');
        ob_start();
        echo '<h3>getQueryParams()</h3>';
        echo 'GETで送信したものが入ります。';
        echo var_dump($request->getQueryParams());

        echo '<h3>getParsedBody()</h3>';
        echo 'POSTで送信したものが入ります。';
        echo var_dump($request->getParsedBody());

        echo '<h3>getUri()</h3>';
        echo 'クエリ(GET)が入ります。';
        echo var_dump($request->getUri());

        $buff = ob_get_clean();
        $response->getBody()->write($buff);
    }
}
