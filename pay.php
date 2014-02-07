<?php
function payment() {
	global $wpdb;
	if (isset($_GET['idzayavka'])) {
	$idzayavka = $_GET['idzayavka'];
	
	$result = $wpdb->get_row("SELECT * FROM zayavka WHERE id='$idzayavka'");
	$math = $wpdb->get_row("SELECT * FROM math WHERE id='$result->idmath'");
	$price = $wpdb->get_row("SELECT * FROM price WHERE idmath='$result->idmath' AND obl='$result->obl'");
		
		if ($result->edet == 1 and $math->archive == 0) {
		$popitka = $wpdb->get_row("SELECT COUNT(*) FROM payment WHERE idzayavka='$idzayavka'");
		if (current($popitka) == 0) {$pop='';} else {$pop = current($popitka) + 1;}
		
		$total = $price->cena * $result->kol;
		
		$description = "Матч $math->team1 - $math->team2 от пользователя: $result->fio";
		
		 $str = '<request>
			  <version>1.2</version>
			  <merchant_id>i7825555227</merchant_id>
			  <result_url>http://poehalinafootball.com.ua/oplata</result_url>
			  <server_url>server_url</server_url>
			  <order_id>userq'.$pop.'ww'.$result->id.'</order_id>
			  <default_phone>+380'.$result->tel.'</default_phone>
			  <pay_way>card,liqpay,delayed</pay_way>
			  <goods_id>'.$result->idmath.'</goods_id>
			  <amount>'.$total.'</amount>
			  <currency>UAH</currency>
			  <description>'.$description.'</description>
			  <exp_time>168</exp_time>
			</request>';
		 $operation_xml = base64_encode($str); 
		 $signature = base64_encode(sha1('Zt5nhuFCVgNwM6PCZxnKFIIyPo7LXWg3IDe'.$str.'Zt5nhuFCVgNwM6PCZxnKFIIyPo7LXWg3IDe', 1));

	    $operation_envelop = '<operation_envelope>
								  <operation_xml>'.$operation_xml.'</operation_xml>
								  <signature>'.$signature.'</signature>
							 </operation_envelope>';
		 $post = '<?xml version=\"1.0\" encoding=\"UTF-8\"?>
								  <request>
									   <liqpay>'.$operation_envelop.'</liqpay>
								  </request>';
		 $url = "https://www.liqpay.com/?do=api_xml";
		 $page = "/?do=api_xml";
		 $headers = array("POST ".$page." HTTP/1.0",
							 "Content-type: text/xml;charset=\"utf-8\"",
							 "Accept: text/xml",
							 "Content-length: ".strlen($post));
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		 curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		 curl_setopt($ch, CURLOPT_POST, 1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		 $result = curl_exec($ch);
		 curl_close($ch);
		 echo $result;
		 
			 echo "
		 <form action='https://www.liqpay.com/?do=clickNbuy' method='POST' />
			  <input type='hidden' name='operation_xml' value='".$operation_xml."' />
			  <input type='hidden' name='signature' value='".$signature."' />
			  <input value='Оплатить' style='padding:5px 10px;' type='submit'/>
		</form>
		 ";
		}
		else {
		if (!$price->cena) echo "Менеджер не указал стоимость.";
		if ($price->cena) echo "Менеджер не утвердил вашу заявку, ожидайте.";
		}
	 }
	 
	if (isset($_POST['signature']) or isset($_POST['operation_xml'])) {
		$xml = $_POST['operation_xml'];
		$xml_decoded = base64_decode($xml);
		
		$obj = simplexml_load_string($xml_decoded);
		$decoded = $_POST['signature'];
		$signature2 = base64_encode(sha1('Zt5nhuFCVgNwM6PCZxnKFIIyPo7LXWg3IDe'.$xml_decoded.'Zt5nhuFCVgNwM6PCZxnKFIIyPo7LXWg3IDe', 1));
		
		
		//echo '<pre>';
		//print_r($obj);
		//echo '</pre>';
		
		$text = iconv('UTF-8', 'ISO-8859-1', $obj->description);
		
		if ($signature2 == $decoded) 
		{
		$idzayavki = explode("ww",$obj->order_id);
		$idzaya = preg_replace("/[^0-9]/", '', $idzayavki[1]);
		
		$sql = "INSERT INTO payment (`idzayavka`, `idmath`, `res`, `summ`, `desc`, `trans`, `pay`, `mob`) VALUES ('$idzaya', '$obj->goods_id', '$obj->status', '$obj->amount', '$text', '$obj->transaction_id', '$obj->pay_way', '$obj->sender_phone')";
		$wpdb->query($sql);
		
		if ($obj->status == 'success') echo "Операция прошла успешно! Предположительно за 2 дня до матча вам прейдет смс со всеми подробностями.";
		if ($obj->status == 'delayed') echo "Операция прошла успешно! Ждем оплаты через терминал.";
		if ($obj->status == 'failure') echo "Операция не произведена.";
		if ($obj->status == 'wait_secure') echo "Платеж находится на проверке Приватбанка.";
		} 
		else {echo "Секретный ключ не совпал! Обратитесь к администратору.";}
	
	}

}
add_shortcode('payment', 'payment');
?>