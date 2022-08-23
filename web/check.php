<?php 
  session_start();
  
  $_SESSION["input_token"] = $_POST["input_token"];//なぜか$_SESSION["input_token"]の値が変わってしまう、強制的に
  
  if(!$_POST){
    header('Location: ./index.php');
    echo "un_ok1";
  }
  
  //タイムスタンプ
  date_default_timezone_set('Asia/Tokyo');
  $timeStamp = time();
  $dateFormatYMD = date('Y年m月d日',$timeStamp);
  $dateFormatHIS = date('H時i分s秒',$timeStamp);
  //$weekFormat = "（".$week[date('w',$timeStamp)]."）";
  $text_value0 = $dateFormatYMD.$dateFormatHIS;
  $text_value1 = $_POST['input_text'];
  $text_value2 = $_POST['所属'];
  $text_value3 = $_POST['email_1'];
  $text_value4 = $_POST['keitai'];
  $text_value5 = $_POST['区分'];
  
  if(empty($_POST['Nナンバー'])) {
    $_POST['Nナンバー'] = "****";}
  $text_value6 = $_POST['Nナンバー'];
  if(empty($_POST['Dナンバー'])) {
    $_POST['Dナンバー'] = "****";}
  $text_value7 = $_POST['Dナンバー'];
  if(empty($_POST['ブロック'])) {
    $_POST['ブロック'] = "****";}
  $text_value8 = $_POST['ブロック'];
  if (empty($_POST['Rナンバー'])){ 
    $_POST['Rナンバー'] ="****";}
  $text_value9 = $_POST['Rナンバー'];
  $text_value10 = $_POST['備考'];
  $title = $_SESSION["title"];
  $Tanto_Address = $_SESSION["Tanto_Address"];
  $zoom = $_SESSION["zoom"];
  $conn = $_SESSION["conncon"];
  $w_teiin = $_SESSION["w_teiin"]; 
  $k_teiin = $_SESSION["k_teiin"];

  //トークンチェック・POSTからSESSIONへ受け渡し
  if($_SESSION["input_token"] === $_POST["input_token"]) {
    $_SESSION = $_POST;
    $tokenValidateError = false;
  } else {          
    $tokenValidateError = true;
  }
  
  // カウントアップ：サーバー（データベース）に接続
  // https://tech-blog.rakus.co.jp/entry/2018/05/09/100346  
  // https://devcenter.heroku.com/ja/articles/getting-started-with-php?singlepage=true
  // https://db.just4fun.biz/?PHP/PostgreSQL%E3%81%AB%E6%8E%A5%E7%B6%9A%E3%81%99%E3%82%8B%E3%83%BBpg_connect
  // https://www.javadrive.jp/php/postgresql/index5.html
  //$conn = "host=ec2-3-230-219-251.compute-1.amazonaws.com port=5432 dbname=dfbkketl37sb46 user=roytnotfcgqxlo password=bdcd362658461f859b4b12571848bd943631b2b5c7429ea05ab2412f6ea3b373";
  
  $link = pg_connect($conn);
  if (!$link) {
    print('サーバーに接続できませんでした。<br>');
    print('再度お試しください。同じ結果なら、itdrive@daihougi.ne.jpまでご連絡ください。<br>');
   }
  pg_set_client_encoding("sjis");
    
  $result = pg_query('SELECT id, count, web FROM sanka');
  
  if (!$result) {
      die('クエリーが失敗しました。'.pg_last_error());
  } 
  for ($i = 0 ; $i < pg_num_rows($result) ; $i++){
      $rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
 // print('id='.$rows['id']);
 // print(',count='.$rows['count'].'<br>');
  }

  if ($text_value4=="会場参加"){
    $a = $rows['count'];
    $b = $rows['count'];
  } else{
    $a = $rows['web'];
  }

  //echo $a;
  //echo $b;
  //echo "OKOKOK";
  //https://tokkan.net/php/pos.html
  //pg_query($link, "UPDATE sanka SET count= $a WHERE id = '1'");   
  $close_flag = pg_close($link);
  //if ($close_flag){
  //    print('切断に成功しました。<br>');
  //}
  $_SESSION["Tanto_Address"] = $Tanto_Address;
  $_SESSION["zoom"] = $zoom;
  $_SESSION["conncon2"] = $conn; // send.php に値を渡す
?>

<!DOCTYPE html>  
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="description" content="大放技イベント登録フォーム" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>登録内容確認画面</title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.ico">

</head>
<body>
  <div>
    <form method="post" action="./send.php">
      <table>
        <thead>
          <tr>
            <th colspan="2">
              <h2>登録内容（確認画面）</h2>
            </th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <th width="170" align="Right">氏　名： </th>
            <td >
              <?php echo htmlspecialchars($_POST["input_text"], ENT_QUOTES, "UTF-8"); ?>
            </td>
          </tr>
          <tr>
            <th width="170" align="Right">所属施設・学校名：</th>
            <td>
              <?php echo htmlspecialchars($_POST["所属"], ENT_QUOTES, "UTF-8"); ?>
            </td>
          </tr>
          <tr>
            <th width="170" align="Right">メールアドレス：</th>
            <td>
              <?php echo htmlspecialchars($_POST["email_1"], ENT_QUOTES, "UTF-8"); ?>
            </td>
          </tr>
          <tr>
            <th width="170" align="Right">参加形態：</th>
            <td>
            <?php echo htmlspecialchars($_POST['keitai'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
          </tr>
          <tr>
            <th width="170" align="Right">区　分：</th>
            <td>
            <?php echo htmlspecialchars($_POST['区分'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
          </tr>
          <tr>
             <th width="170" align="Right">日放技番号：</th>
            <td>
              <?php echo htmlspecialchars($_POST['Nナンバー'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
          </tr>
          <tr>
            <th width="170" align="Right">大放技番号：</th>
            <td>
              <?php echo htmlspecialchars($_POST['Dナンバー'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
          </tr>
          <tr>
            <th width="170" align="Right">ブロック名：</th>
            <td>
              <?php echo htmlspecialchars($_POST['ブロック'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
          </tr>
          <tr>
            <th width="170" align="Right">領収書番号：</th>
            <td>
              <?php echo htmlspecialchars($_POST['Rナンバー'], ENT_QUOTES, 'UTF-8'); ?>
            </td>
          </tr>
          <tr>
            <th width="170" align="Right">備　考：</th>
            <td>
              <?php echo nl2br(htmlspecialchars($_POST['備考'], ENT_QUOTES, 'UTF-8')); ?>
            </td>
          </tr>
          <tr>
            <th style="text-align:left" colspan="2"> 
　　          <?php if($b<$k_teiin): ?>          
    　　　          <p>　この内容でよろしければ『送信する』ボタンを押して下さい．<br>
    　変更が必要な場合は『戻る』ボタンで登録フォームに戻ります．</p>
         　　 <?php else : ?>
         　　　　　 <p>　　<font color="red">会場参加は定員に達しました．</font><br>
         　 『戻る』ボタンで登録フォームに戻り、Web参加でお申し込みください．</p>
      　　    <?php endif; ?>
            </th>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"> 
              <input type="hidden" name="input_text" value="<?php echo $text_value1; ?>">
              <input type="hidden" name="所属" value="<?php echo $text_value2; ?>">
              <input type="hidden" name="email_1" value="<?php echo $text_value3; ?>">
              <input type="hidden" name="keitai" value="<?php echo $text_value4; ?>">
              <input type="hidden" name="区分" value="<?php echo $text_value5; ?>">
              <input type="hidden" name="Nナンバー" value="<?php echo $text_value6; ?>">
              <input type="hidden" name="Dナンバー" value="<?php echo $text_value7; ?>">
              <input type="hidden" name="ブロック" value="<?php echo $text_value8; ?>">
              <input type="hidden" name="Rナンバー" value="<?php echo $text_value9; ?>">
              <input type="hidden" name="備考" value="<?php echo $text_value10; ?>">
              <input type="submit" formaction="./index.php" value="戻る" style="position: relative; left: 110px; top: 20px;"/>

              <?php if(!$tokenValidateError): ?>
                <?php if($b<$k_teiin): ?>
                  　<input type="submit" value="送信する" style="position: relative; left: 130px; top: 20px;"/>
                <?php else : ?>
                    <input type="submit" disabled value="送信する" style="position: relative; left: 130px; top: 20px;"/>
                <?php endif; ?>
                
                <input type="hidden" name="a" value="<?php echo $a; ?>">
                <input type="hidden" name="title" value="<?php echo $title; ?>">
                
                <?php
                  //データを配列に
                  $list = array ($a,$text_value0,$text_value1, $text_value2, $text_value3, $text_value4,$text_value5,$text_value6,$text_value7,$text_value8,$text_value9,$text_value10,$title);
                  mb_convert_variables('Shift_JIS', 'UTF-8', $list);
                ?>            
              <?php endif; ?>
            </td>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>
</body>
</html>
