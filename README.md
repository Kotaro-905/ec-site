<h1>ECサイト</h1>


<h2>環境構築</h2>

## Dockerビルド
<ul>
　<li>1.git clone git@github.com:Kotaro-905/ec-site.git</li>
  <li>2.docker-compose up -d --build</li>
  </ul>

※MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください

## Laravel環境構築
<ul>
　<li>1  docker-compose exec php bash</li>
　<li>2. composer install</li>
　<li>3. .env.exampleファイルから.envを作成し、環境変数を変更</li>
　<li>4. php artisan key:generate</li>
　<li>5. php artisan migrate</li>
　<li>6. php artisan db:seed</li>
  <li>7. php artisan storage:link</li>
</ul>

### 環境変数
STRIPE_KEY・STRIPE_SECRET　は未設定のため、KEYの取得をお願いいたします。

## 使用技術
<ul>
 <li>PHP 8.1.33</li>
 <li>Laravel 8.83.29</li>
 <li>MySQL　8.0.26</li>
  <li>Docker（開発環境</li>
  <li>Laravel Fortify（認証機能</li>
  <li>HTML/CSS（クラスベースのスタイリング</li>
</ul>

## テスト実行手順
<ul> 
<li><b>1. テスト用データベースを作成</b></li> 
</ul>
 <pre> docker-compose exec php bash mysql -u root -p # パスワード: root CREATE DATABASE laravel_test; exit </pre> 
 <ul> 
 <li><b>2. テスト環境でマイグレーションを実行</b></li> 
 </ul> 
 <pre> docker-compose exec php bash php artisan migrate:fresh --env=testing </pre> 
 <ul> 
 <li>上記コマンドにより、<code>laravel_test</code> データベース上にテーブルが再作成されます。</li> <li><code>--env=testing</code> オプションにより、<code>.env.testing</code> の設定が使用されます。</li> 
 <li>既存テーブルをすべて削除して再構築する場合に <code>migrate:fresh</code> を使用します。</li> 
 </ul>


## 補足
<ul>
<li>鈴木北斗コーチから、機能要件 FN012 の通りメール認証画面から商品一覧画面に遷移するようにお聞きしています。Figmaとは導線が異なります機能要件を優先しています。</li>
<li>会員登録時のパスワードは８文字以上の入力をお願いします</li>
</ul>

## URL
・開発環境：http://localhost/
・phpMyAdmin：http://localhost:8080/index.php?route=/database/structure&db=information_schema
