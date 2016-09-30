<?
namespace LeagueRouteSample;

require_once './BaseController.php';
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class News extends BaseController
{
    /**
     * BaseController#before/afterActionを試すためのメソッドです。
     * __callを呼び出すためにアクションメソッドをpublicではなく、protectedにする必要があります。
     * privateにすると呼び出しがループしてスタックオーバーになります。
     */
    protected function magicMethodCall(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $response->getBody()->write('--- start $News->magicMethodCall() ---<br />');
        $get  = $this->params('get');
        $post = $this->params('post');
        $response->getBody()->write(
        '<h3>params()</h3>'.
        "<p>params('get')  = {$get}</p>".
        "<p>params('post') = {$post}</p>");
        // afterActionに渡すために戻り値が必要です。
        return [$request, $response, $args];
    }

    /**
     * BaseController#before/afterActionを試すためのメソッドです。
     * league/routeの機能で、用意したカスタムストラテジーによってpublicメソッドでもフックが有効になります。
     */
    public function customStrategy(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        echo '--- start $News->beforeAfterAction() ---<br />';
        $get  = $this->params('get');
        $post = $this->params('post');
        echo '<h3>params()</h3>';
        echo "<p>params('get')  = {$get}</p>";
        echo "<p>params('post') = {$post}</p>";
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
        $response->getBody()->write('<p>このformはpost送信です。</p>');
        $response->getBody()->write('
        <form action="/news/create" method="post">
            <input type="text" name="name">
            <input type="text" name="title">
            <input type="submit" value="送信">
        </form>');

        $response->getBody()->write('<h2>submit data</h2>');
        ob_start(); // var_dumpもwriteするため
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
