# テスト環境構築
***
## 参考リンク

- [docker desktopの参考サイト](https://qiita.com/zembutsu/items/a98f6f25ef47c04893b3) 
Windows 11にDocker Desktopを入れる手順（令和5年最新版）

- [mysqlの参考サイト](https://qiita.com/aki_number16/items/bff7aab79fb8c9657b62)
WindowsにMySQLをインストールする

## 1.Docker Desctopのダウンロード

- https://www.docker.com/products/docker-desktop/

こちらのサイトにアクセスしてdockerをダウンロード。

![](./image.png)

おそらくAMD64なので`Download for Windows - AMD6`を押下

## 2.インストール諸々

上記の参考サイトを参照してください。
[参考リンク](#参考リンク)

## 3.コンテナの起動

`docker-compose.yml`ファイルがある場所で下記コマンドを実行、しばらく待機。

```sh
docker compose up -d
```

## 4.MySQLのダウンロード

基本的には参考サイトにしたがってすすめてください。
[参考リンク](#参考リンク)

![](./image2.png)

もしかしたらver.8.0.40の方をダウンロードしたほうがいいかもしれない...。

## 5.MySQLの起動とデータベースへの接続

### 5-1.MySQL Workbenchの起動
前工程でコンテナを起動しているとすでにMySQLは立ち上がっている。
`MySQL`を操作するための`Workbench`の起動をする。

![](./image3.png)
※ない場合はインストール時に入れていないかもしれません。バージョンを下げるか、もしくはインストールしなおしてください。

### 5-2.Connectionの作成

![](./image4.png)

MySQL Connectionsの隣にある「`＋`」を押してSetup New Connection。

Connection Nameは`任意の名前`を、
Portは`3307`、
Usernameを`user`にしてOKボタンを押下。

パスワードは`password`です。

例：
![](./image5.png)

## 6.テーブル作成

queryのところに下記のコマンドをコピペ
```sql
use `php-docker-db`;
create table mytable(id int, name char(100));
insert into mytable(id, name) values(1, "poka"),(2,"hoge");
```

![](./image6.png)

`⚡`マークを押すか、`Ctrl + Enter`で実行します。

## 7.確認

ブラウザを開いて`localhost:8000/db.php`にアクセス

![](./image7.png)

こうなってたら成功。なんかほかの出てきたら相談してください。