<?

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class News
{
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
}
