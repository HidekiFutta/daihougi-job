<?php
if (mb_send_mail( "hima71f@gmail.com", "TEST SUBJECT", "TEST BODY")) {
  echo "送信完了";
} else {
  echo "送信失敗";
}