<?php
//----------------------------------------------------
//１．入力チェック(受信確認処理追加)
//----------------------------------------------------
//商品名 受信チェック:item
if(!isset($_POST["item"]) || $_POST["item"]==""){
  exit("ParameError ! item");
}

//金額 受信チェック:prices
if(!isset($_POST["prices"]) || $_POST["prices"]==""){
   exit("ParameError ! prices");
}

//商品紹介文 受信チェック:description
if(!isset($_POST["descriptions"]) || $_POST["descriptions"]==""){
  exit("ParameError ! descriptions");
}

//ファイル受信チェック※$_FILES["******"]["name"]の場合
if(!isset($_FILES["fname"]["name"]) || $_FILES["fname"]["name"]==""){
  exit("ParameError ! fname");
}



//----------------------------------------------------
//２. POSTデータ取得
//----------------------------------------------------
$fname  = $_FILES["fname"]["name"];   //File名
$item   = $_POST["item"];   //商品名
$price  = $_POST["prices"];   //価格(数字：intvalを使う)
$descriptions = $_POST["descriptions"];   //商品紹介文


//1-2. FileUpload処理
$upload = "../img_sample/"; //画像アップロードフォルダへのパス
//アップロードした画像を../img/へ移動させる記述↓
if(move_uploaded_file($_FILES['fname']['tmp_name'], $upload.$fname)){
  //FileUpload:OK
} else {
  //FileUpload:NG
  echo "Upload failed";
  echo $_FILES['upfile']['error'];
}

//----------------------------------------------------
//３. DB接続します(エラー処理追加)
//----------------------------------------------------
try {
  $pdo = new PDO('mysql:dbname=gs_d13_23_kadai;charset=utf8;host=localhost','root','');
} catch (PDOException $e) {
  exit('DbConnectError:'.$e->getMessage());
}

//----------------------------------------------------
//４．データ登録SQL作成
//----------------------------------------------------
$stmt = $pdo->prepare("INSERT INTO 0629_ecgift_sample(id, item, prices, fname,
descriptions, indate, created_at, updated_at )VALUES(NULL, :item, :prices, :fname, :descriptions, sysdate(), now(), now())");
$stmt->bindValue(':item', $item, PDO::PARAM_STR);
$stmt->bindValue(':prices', $prices, PDO::PARAM_INT); //数値
$stmt->bindValue(':fname', $fname, PDO::PARAM_STR);
$stmt->bindValue(':descriptions', $descriptions, PDO::PARAM_STR);
$status = $stmt->execute();

//----------------------------------------------------
//５．データ登録処理後
//----------------------------------------------------
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("QueryError:".$error[2]);
}else{
  //５．item.phpへリダイレクト
  header("Location: item_sample.php");
  exit;
}
?>
