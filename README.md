<h1>ECサイト</h1>


<h2>環境構築</h2>

## Dockerビルド
<ul>
　<li>1.git@github.com:Kotaro-905/ec-site.git</li>
  <li>2.docker-compose up -d --build</li>
  </ul>

※MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください

## Laravel環境構築
<ul>
　<li>1．docker-compose exec php bash</li>
　<li>2.composer install</li>
　<li>3..env.exampleファイルからenvを作成し、環境変数を変更</li>
　<li>4.php artisan key:generate</li>
　<li>5.php artisan migrate</li>
　<li>6.php artisan db:seed</li>
</ul>

## 使用技術
<ul>
 <li>PHP 7.4.9</li>
 <li>Laravel 8.83.29</li>
 <li>MySQL　9.3.0</li>
  <li>Docker（開発環境</li>
  <li>Laravel Fortify（認証機能</li>
  <li>HTML/CSS（クラスベースのスタイリング</li>
</ul>

## 補足
<ul>
<li>鈴木北斗コーチから、機能要件 FN012 の通りメール認証画面から商品一覧画面に遷移するようにお聞きしています。Figmaとは導線が異なります機能要件を優先しています。</li>
</ul>

## URL
・開発環境（お問い合わせフォーム）：http://localhost/
・phpMyAdmin：http://localhost:8080/index.php?route=/database/structure&db=information_schema
