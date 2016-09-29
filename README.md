# league-route-sample

PHPのルーティングライブラリである`league/route`のサンプルコードです。comoposerのホスティング先であるpackagistsのパッケージ名とGithubのものは違いますが、同ファイルです。

* [Github](https://github.com/thephpleague/route)
* [ドキュメント](http://route.thephpleague.com/)

日本語での情報は検索しても出てきません。公式ドキュメントも簡素です。requestとresponseのメソッドについては、[PSR7のインターフェースのソースコード](https://github.com/php-fig/http-message)を見ると良いです。Docで説明が載っています。

version 1系のドキュメントを見たい場合は、リポジトリをcloneして、ブランチ`gh-pages-v1`をチェックアウトしてgemの`jekyll serve`を起動して下さい。`localhost:4000`で見ることができます。1系のドキュメントは左サイドバーのリンク先が間違っているので、自分でパスを入力して移動する必要があります。

## Set up

Macのデフォルトのapacheを使っている場合は、`/etc/apache2/httpd.conf`の末尾に下記をコピペしてください。その後に、`sudo apachectl restart`で再起動しないと設定は反映されません。

全てのパスを`/index.php`に回す設定は、`/.htaccess`に記載してUPしてあります。

cloneしたリポジトリはドキュメントルートである`/Library/WebServer/DocumentRoot/route`に置く必要があります。

ドメインを設定するために、`/etc/hosts`の末尾に`127.0.0.1	mylocal.net`をコピペして下さい。

```xml
<VirtualHost *:80>
  ServerName mylocal.net
  DocumentRoot /Library/WebServer/Documents/route/

  <Directory /Library/WebServer/Documents/route>
    Options FollowSymLinks Indexes
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
```


## 内容

サンプルコードの特徴です。

* Newsコントローラーを用意。
* ルーティングの記述は`/index.php`にあり。
* apacheで全てのリクエストを`index.php`に回すように`/.htaccess`で設定済み。

パスは`/`と`/news/`以下を設定してあります。ルーティングについては`/index.php`にコメントで説明を記述してあります。

### アクセサ

requestとresponseのプロパティを操作するには下記のメソッドを使います。

* アクセスはget~()
* セットはwith~()
* 削除はwihtout~()
* 存在の確認はhas~()

この3つのメソッドの戻り値は、自分のコピーで値を操作したものです。これを不変オブジェクトと言います。そのためメソッドを実行したレシーバーのプロパティが書き換えられることはありません。

注意点として、常に戻り値を変数に入れるなどして扱う必要があります。レシーバーをそのまま使っても値は変更されていません。メソッドチェーンで書くと便利です。

```php
<?php
$request->getMethod();
$newReq = $request->withMethod('changed');

$request->getMethod(); // GET
$newReq->getMethod();  // changed

// メソッドチェーン
$newReq = $request
    ->withHeader('my-header1', 'foo')
    ->withHeader('my-header2', 'bar');
```

### zend-diacotrs メソッド一覧

`get_class_methods($request)`で確認できます。出力を整理しました。

#### Request

**getとwithの両方がある**

* ServerParams
* UploadedFiles
* CookieParams
* QueryParams
* ParsedBody
* Method
* ProtocolVersion
* Body
* RequestTarget
* Uri

**attribute系**

requestオブジェクトに自由にkey-valueを持たせることができます。

* getAttributes
* getAttribute
* withAttribute
* withoutAttribute

**header系**

* getHeaders
* hasHeader
* getHeader
* getHeaderLine
* withHeader
* withAddedHeader
* withoutHeader

#### Response

**getとwithの両方がある**

* Body
* ProtocolVersion

**getかwithの片方のみ**

* getStatusCode
* getReasonPhrase
* withStatus

**header系**

* getHeaders
* hasHeader
* getHeader
* getHeaderLine
* withHeader
* withAddedHeader
* withoutHeader

#### Stream

`$request->getBody()`で取得できます。`$stream->write('output')`で出力内容をバッファに溜めます。

* __toString
* close
* detach
* attach
* getSize
* tell
* eof
* isSeekable
* seek
* rewind
* isWritable
* write
* isReadable
* read
* getContents
* getMetadata
