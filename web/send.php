<?php
  ini_set("display_errors", 'On');
  error_reporting(E_ALL);
?>

<?php  
  session_start();
  if(!$_SESSION) {
    echo '送信できませんでした。再度お試しください。';
    echo '詳しくは　"itdrive@daihougi.ne.jp"　までお問い合わせください。';
  }
  
  //参考HP　https://designsupply-web.com/media/programming/1642/
  //任意入力項目の配列が空の場合のエラーメッセージ制御
  error_reporting(0); //エラー非表示  //composer updateしないとVendorの中身は更新されない
  //error_reporting(E_ALL ^ E_NOTICE);

  require '../vendor/autoload.php';

  //タイムスタンプ
  date_default_timezone_set('Asia/Tokyo');
  $timeStamp = time();
  $dateFormatYMD = date('Y年m月d日',$timeStamp);
  $dateFormatHIS = date('H時i分s秒',$timeStamp);
  $outputDate = $dateFormatYMD.$dateFormatHIS;
  $conn = $_SESSION["conncon2"];
  $E_Address = $_SESSION["Tanto_Address"];
  //XSS対策用サニタイズ
  
  function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
  
  //配列データの処理
  //$checkboxArray = implode(",",$_SESSION['スキル']);

  //メール本文内に表示するデータの変数化
  $event = h($_POST["title"]);//"明日から役立つセミナー";
  $count = h($_POST["a"]);
  $text = h($_SESSION['input_text']);
  $kana = h($_SESSION['所属']);
  $emails = h($_SESSION['email_1']);
  $zipcode = h($_SESSION['Dナンバー']);
  $radio = h($_SESSION['ブロック']);
 
  //自動返信メール本文（ヒアドキュメント）
  $messageUser = <<< EOD
  <html>
  <body>
  <p>{$text}　様</p>
  
  <p>「{$event}」 より下記の内容で受付ました。</p>
  
     ---------------------------------------------------------------
  <ul> 
  <li>【受付　番号】{$count}</li>
  <li>【氏　　　名】{$text}</li>
  <li>【施　設　名】{$kana}</li>
  <li>【メ　ー　ル】{$emails}</li>
  <li>【大放技番号】{$zipcode}</li>
  <li>【ブロック名】{$radio}</li>
  </ul>
      ---------------------------------------------------------------
  
  <p>・当会は、職業斡旋業務は認められていません。<br>
  　 以下のリンクから、各自でご確認ください。</p>
  <p>・https://www.city.yao.osaka.jp/0000059621.html<br>
  ・http://www.midorigaokahp.jp/recruit/xpoffer.html<br>
  ・https://hrmos.co/pages/holonics/jobs?category=1484370662128041984
  <p>・更新日時によって、情報が古い場合がございます。
  <p>・ご不明な点は mail: osaka@daihougi.ne.jp<br>
  　までお問い合わせください。</p>
  
  </body>
  </html>
EOD;

  //管理者確認用メール本文（ヒアドキュメント）
   $messageAdmin = <<< EOD
HPより以下の登録がありました。

----------------------------------------------------

【イベント名】{$event}
【受付　番号】{$number}
【氏　　　名】{$text}
【施　設　名】{$kana}
【メ　ー　ル】{$emails}
【大放技番号】{$zipcode}
【ブロック名】{$radio}

----------------------------------------------------
EOD;

//メール共通送信設定
//mb_language("ja");
//mb_internal_encoding("UTF-8");

//if(!empty($_SESSION['email_1'])) {
//https://sendgrid.kke.co.jp/docs/Integrate/Code_Examples/v3_Mail/php.html

$email = new \SendGrid\Mail\Mail();
    $email->setFrom("itdrive@daihougi.ne.jp", "大放技");
    $email->setSubject("大放技求人情報");
    $email->addTo($emails, "User");
    $email->addContent("text/html", $messageUser);
    $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
    try {
      //echo "OK";
      $response = $sendgrid->send($email);
      //print $response->statusCode() . "\n";
      //print_r($response)->headers());
      //print $response->body() . "\n";    
    } catch (Exception $e) {
      echo 'Caught exception: '. $e->getMessage() ."\n";
  }

#$email = new \SendGrid\Mail\Mail();
#  $email->setFrom("itdrive@daihougi.ne.jp", "大放技");
#  $email->setSubject("大放技求人情報受付");
#  $email->addTo("hima71f@yahoo.co.jp", "User");
#  $email->addTo($E_Address, "User"); //担当者のアドレス
#  $email->addContent("text/plain", $messageAdmin);
#  $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
 # try {
#    //echo "OK2";
#    $response = $sendgrid->send($email);
    //print $response->statusCode() . "\n";
    //print_r($response->headers());
    //print $response->body() . "\n";
 # } catch (Exception $e) {
#    echo 'Caught exception: '. $e->getMessage() ."\n";
#}
    
$isSend = true;
  //} else {
   // $isSend = false;
  //}
  session_destroy();

?>

<?php if($isSend):
  //受付番号：カウントアップ
  //$conn = "host=ec2-3-230-219-251.compute-1.amazonaws.com port=5432 dbname=dfbkketl37sb46 user=roytnotfcgqxlo password=bdcd362658461f859b4b12571848bd943631b2b5c7429ea05ab2412f6ea3b373";
    
  $link = pg_connect($conn);
  if (!$link) {
      die('接続失敗です。'.pg_last_error());
  }
  // タイムゾーンの初期化と日付の取得
  date_default_timezone_set('Asia/Tokyo');

  //pg_set_client_encoding($conn, "sjis");
  
  #$result = pg_query($link,'SELECT id FROM meibo');
  #if (!$result) {
  #    die('クエリーが失敗しました。'.pg_last_error());
  #} 
  #for ($i = 0 ; $i < pg_num_rows($result) ; $i++){
  #    $rows = pg_fetch_array($result, NULL, PGSQL_ASSOC);
  #}
  #if ($keitai=="会場参加"){
  #  $rows = $rows['count'] + 1;
  #  pg_query($link, "UPDATE sanka SET count= $rows WHERE id = '1'"); 
  #} else{
  #  $rows = $rows['web'] + 1;
  #  pg_query($link, "UPDATE sanka SET web= $rows WHERE id = '1'"); 
  #}
  //参加者名簿
  $result2 = pg_query($link,'SELECT * FROM meibo');
  if (!$result2) {
      die('クエリーが失敗しました。'.pg_last_error());
  } 
  #for ($i = 0 ; $i < pg_num_rows($result2) ; $i++){
  #    $rows = pg_fetch_array($result2, NULL, PGSQL_ASSOC);
  $b = pg_num_rows($result2); // 行数確認
  $b = $b+1;
  //insert
  $sql = "INSERT INTO meibo 
  VALUES ($b,$count,'$outputDate','$text','$kana','$emails','$zipcode','$radio')";

  $result2_flag = pg_query($link,$sql);
  $close_flag = pg_close($link); 
?>

<!DOCTYPE html>  
<html lang="ja">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=shift_jis">
  <meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Last-Modified" content="Fri, 3 Dec 2021 04:52:01 GMT">
  <meta http-equiv="Expires" content="Fri, 3 Dec 2021 04:52:06 GMT">

	<title>大放技イベント申請完了フォーム</title>
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="apple-touch-icon" sizes="180x180" href="/favicon.ico">
</head>
<body>
		<div align="center">
			<table cool="" gridx="16" gridy="16" showgridx="" showgridy="" usegridx="" usegridy="" width="630" height="357" cellspacing="0" cellpadding="0" border="0">
				<tbody><tr height="16">
					<td colspan="3" width="629" height="16"></td>
					<td width="1" height="16"><spacer type="block" width="1" height="16"></spacer></td>
				</tr>
				<tr height="340">
					<td width="31" height="340"></td>
					<td content="" csheight="340" xpos="31" width="566" valign="top" height="340">
						<div align="center">
							<p><br>
								<font size="3">大放技求人情報取得申請，受付完了．<br>
								</font></p>
							<p><font size="3">現在の求人情報を【<?php echo h($_SESSION['email_1']); ?>】まで<br>
              お送りましたので，内容をご確認下さい．
              
              </font></p>
						  <p></p>
							<p>なお，メールが届かない場合は，下記までご連絡下さい．<br>
							</p>
							<p><br>
								<br>
								
								お問い合わせ先<br>
								<br>
								　　(公社)大阪府診療放射線技師会<br>
								　　 　Mail：itdrive@daihougi.ne.jp </a><br>
								<br>
								<br>
							</p>
							<p><br>
								<a href="http://www.daihougi.ne.jp/" target="_top"><img src="/conf_ok.jpg" alt="" width="137" height="27" border="0"></a></p>
						</div>
					</td>
					<td width="32" height="340"></td>
					<td width="1" height="340"><spacer type="block" width="1" height="340"></spacer></td>
				</tr>
				<tr cntrlrow="" height="1">
					<td width="31" height="1"><spacer type="block" width="31" height="1"></spacer></td>
					<td width="566" height="1"><spacer type="block" width="566" height="1"></spacer></td>
					<td width="32" height="1"><spacer type="block" width="32" height="1"></spacer></td>
					<td width="1" height="1"></td>
				</tr>
			</tbody></table>
		</div>
  </body>
</html>

<?php else: ?> 
  <p>送信エラー：メールフォームからの送信に失敗しました。お手数ですが再度お試しください。 
  </p>
<?php endif; ?>