<?php
function vsezayavki($atts) {
global $wpdb;
$id = get_current_user_id();
$gorod = get_user_meta($id, 'gorod');
global $gorodobl;
$gorodobl = $gorod[0];


if (isset($_POST['txtotz'])) {
$math = $wpdb->get_row( "SELECT * FROM math WHERE id='$_POST[mathnumber]'" );

if ($math->chemp == "LCH") $liga = "Лига чемпионов";
if ($math->chemp == "LE") $liga = "Лига европы";
if ($math->chemp == "UKR") $liga = "Чемпионат Украины";
if ($math->chemp == "ALL") $liga = "Матчи сборной и другие";
$time = date('d.m.Y' ,strtotime($math->time));

	$current_user = wp_get_current_user();
	$my_post = array(  
		 'post_title' => $liga." ".$math->team1." - ".$math->team2." ".$time,  
		 'post_content' => $_POST['txtotz'],  
		 'post_status' => 'publish',  
		 'post_author' => $current_user->id,  
		 'post_category' => array(5)  
	  );   
	wp_insert_post( $my_post ); 
	echo "<script>alert('Ваш отзыв отправлен! Отзыв опубликован в одноименном разделе, вверху страницы.');</script>";
	}


$current_user = wp_get_current_user(); 
$zayavki = $wpdb->get_results( "SELECT * FROM zayavka WHERE mail='$current_user->user_email'" );
echo "<table class='tablemath' width='946'><tr class='tittable'><td width='159'>Название матча</td><td width='88'>Поддержка</td><td width='87'>Кол-во</td><td width='167'>Город</td><td width='85'>Телефон</td><td width='220'>ФИО</td><td>Статус</td><td width='120'>Редактор</td></tr>";
foreach ( $zayavki as $zayavka ) {
$math = $wpdb->get_row( "SELECT * FROM math WHERE id='$zayavka->idmath'" );
			?>
<script>
function closemath() {
$('.vspform').css("display","none");
}

function dsp<?php echo $math->id; ?>() {
$('.vspform').css("display","block");
$('.vspform').html("<a OnClick='closemath(); return false;' href='#'><img style='position:absolute; right:-15px; top:-15px; width:30px; height:30px;' src='<?php echo $url = plugins_url().'/poehali'; ?>/error.png'></a><p align='center' style='font-weight:bold;'>Редактирование заявки на матч</p><br><form class='otpforma' method='POST'><p>Название матча</p><input class='avtorizinp2 readonly' name='nmath' type='text' readonly value='<?php echo $math->team1; ?> - <?php echo $math->team2; ?>'><p>Поддержать команду</p><select name='podkom' class='avtorizinp2'><option><?php echo $zayavka->podkom; ?></option><option><?php if ($zayavka->podkom == $math->team1) {echo $math->team2;} else { echo $math->team1; }?></option></select><p>Количество человек для поездки</p><input class='avtorizinp2' name='kol' value='<?php echo $zayavka->kol; ?>' type='text'><p>Телефон</p><input class='avtorizinp2' id='tel' name='tel' value='0<?php echo $zayavka->tel; ?>' type='text'><p>E-mail</p><input name='mail' class='avtorizinp2 readonly' value='<?php $current_user = wp_get_current_user(); echo $current_user->user_email; ?>' type='text' readonly ><p>Ваше ФИО</p><input name='fio' value='<?php echo $zayavka->fio; ?>' class='avtorizinp2' type='text'><p>Область проживания</p><input class='avtorizinp2 readonly' name='oblast' type='text' value='<?php $obl4 = $wpdb->get_row("SELECT * FROM oblast WHERE id='$zayavka->obl'"); echo $obl4->obl; ?>' readonly><input type='hidden' name='idobl' value='<?php echo $zayavka->obl; ?>'><br><input class='submit2' value='Редактировать заявку' align='right' type='submit'><input type='hidden' value='<?php echo $math->id; ?>' name='redzayavka'></form>");	
}


function otziv<?php echo $math->id; ?>() {
$('.vspform').css("display","block");
$('.vspform').html("<a OnClick='closemath(); return false;' href='#'><img style='position:absolute; right:-15px; top:-15px; width:30px; height:30px;' src='<?php echo $url = plugins_url().'/poehali'; ?>/error.png'></a><p align='center' style='font-weight:bold;'>Отправка отзыва</p><br><form class='otpforma' method='POST'><p>Введите текст отзыва</p><textarea name='txtotz' style='width:350px; height:150px; margin-top:10px; resize:none;'></textarea><input class='submit2' value='Отправить отзыв' align='right' type='submit'><input type='hidden' name='mathnumber' value='<?php echo $math->id; ?>'></form>");	
}

</script>
	<?php
	$estcena = $wpdb->get_row( "SELECT * FROM price WHERE idmath='$zayavka->idmath' AND obl='$zayavka->obl'");
	if ($math->archive != 1 and $zayavka->del == 0 ) {
	if ($zayavka->edet == 1 and !$estcena->cena) {$edet = 'Ожидаем цену'; $edit="Ожидайте";}
	if ($zayavka->edet == 1 and $estcena->cena > 0) {$edet = 'Ожидаем оплаты'; $edit="<a href='/oplata/?idzayavka=".$zayavka->id."'>Оплатить</a>";}
	if ($zayavka->edet == 0) {$edet = 'Ожидаем подтверждения'; $edit="<a style='display:inline;' href='#' OnClick='dsp$math->id(); return false;'>Ред.</a> <form style='display:inline;' action='' method='post'><input type='hidden' name='delzayavka' value='$math->id'><input type='submit' onclick='return confirm(\"Удалить заявку?\")' value='Удал.' style='color: #53a1cf; border:none; background:none; text-decoration:underline; font-weight:bold; cursor:pointer;'></form>";}
	
	$payres = $wpdb->get_row("SELECT * FROM payment WHERE idzayavka='$zayavka->id' AND res='success'");
		$payres2 = $wpdb->get_row("SELECT * FROM payment WHERE idzayavka='$zayavka->id' AND res='delayed'");
		if (!empty($payres->id)) { 
			if ($payres->pay == 'card') $cherez = 'карту';
			if ($payres->pay == 'delayed') $cherez = 'наличными';
			if ($payres->pay == 'liqpay') $cherez = 'liqpay';
			$details = 'Оплачено '.$payres->summ.' через '.$cherez;
			$edet = '<a title="'.$details.'">Оплачено</a>';
			$edit = 'Ожидайте смс';
		}
		if (empty($payres->id) and !empty($payres2->id)) { 
			$details = 'Ожидаем оплаты '.$payres2->summ.'грн. через терминал';
			$edet = '<a title="'.$details.'">Наличными</a>';
		}

	
	$obl3 = $wpdb->get_row("SELECT * FROM oblast WHERE id='$zayavka->obl'"); 
	echo "<tr class='strokamath'><td>".$math->team1." - ".$math->team2."</td><td>".$zayavka->podkom."</td><td>".$zayavka->kol."</td><td>".$obl3->obl."</td><td>0".$zayavka->tel."</td><td>".$zayavka->fio."</td><td>".$edet."</td><td>".$edit."</td></tr>";

	}

}
echo "</table><p align='center' style='font-weight:bold; padding-top:25px; padding-bottom:10px;'>Архивные заявки</p>";



$kolmath=$kolle=$kollch=$kolukr=$kolall=0;
$zayavki = $wpdb->get_results( "SELECT * FROM zayavka WHERE mail='$current_user->user_email'" );
echo "<table class='tablemath' width='946'><tr class='tittable'><td width='159'>Название матча</td><td width='88'>Поддержка</td><td width='55'>Кол-во</td><td width='167'>Город</td><td width='85'>Телефон</td><td width='220'>ФИО</td><td>Ездил?</td><td>Отзыв</td></tr>";
foreach ( $zayavki as $zayavka ) {
$math = $wpdb->get_row( "SELECT * FROM math WHERE id='$zayavka->idmath'" );
	if ($zayavka->edet == 1 and $math->archive == 1 or $zayavka->del == 1) {
	$kolmath++;
		if ($math->chemp == 'LE') $kolle++;
		if ($math->chemp == 'LCH') $kollch++;
		if ($math->chemp == 'UKR') $kolukr++;
		if ($math->chemp == 'ALL') $kolall++;
	}
	if ($math->archive == 1 or $zayavka->del == 1) {
	if ($zayavka->edet == 1) {$edet = 'Да'; $otziv="<a href='#' OnClick='otziv$math->id(); return false;'>Оставить отзыв</a>";}
	if ($zayavka->edet == 0) {$edet = 'Нет'; $otziv="Не ездил";}
	
	
	$obl2 = $wpdb->get_row("SELECT * FROM oblast WHERE id='$zayavka->obl'"); 
	echo "<tr class='strokamath'><td>".$math->team1." - ".$math->team2."</td><td>".$zayavka->podkom."</td><td>".$zayavka->kol."</td><td>".$obl2->obl."</td><td>0".$zayavka->tel."</td><td>".$zayavka->fio."</td><td>".$edet."</td><td>$otziv</td></tr>";
	}
}
echo "</table>";
echo "<br><div style='font-size:13px;'>";
echo "С нами вы посетили ".$kolmath." матч.<br>";
echo "Матчи чемпионата украины: ".$kolukr."<br>";
echo "Матчи Лиги чемпионов: ".$kollch."<br>";
echo "Матчи Лиги европы: ".$kolle."<br>";
echo "Матчи сборной Украины и другие: ".$kolall;
echo "<br><br><p>Расшифровка статусов:</p><ul>
<li><b>Ожидаем подтверждения</b> - Наш менеджер свяжется с вами и уточнит всю необхдимую информацию.</li>
<li><b>Ожидаем цену</b> - Ожидайте оглашения полной стоимости тура.</li>
<li><b>Ожидаем оплаты</b> - Менеджер установил стоимость тура и мы ждем оплаты одним из способов.</li>
<li><b>Оплачено</b> - Вы оплатили тур одним из способов картой/наличными/liqpay. Ожидайте смс сообщения, а так же сообщения на e-mail о подробностях выезда.</li>
<li><b>Наличными</b> - Вы выбрали способ оплаты наличными, ожидаем оплаты. Счет будет действителен 168 часов, с момента его выставления. Такой тип оплаты обрабатываеется вручную, после проплаты тура, обратитесь к менеджеру в своем регионе.</li>
</ul></div>";

if (isset($_POST['redzayavka'])) {
$sql = "UPDATE zayavka SET podkom='$_POST[podkom]', kol='$_POST[kol]', tel='$_POST[tel]', fio='$_POST[fio]' WHERE idmath='$_POST[redzayavka]' AND id='$zayavka->id'";
$wpdb->query($sql);
?>
<script>alert('Заявка отредактирована!');</script>
<?php
}

if (isset($_POST['delzayavka'])) {
$sql = "UPDATE zayavka SET del='1' WHERE idmath='$_POST[delzayavka]'";
$wpdb->query($sql);
?>
<script>alert('Заявка удалена!');</script>
<?php
}

} 

add_shortcode('vsezayavki', 'vsezayavki'); 
add_action('wp_ajax_vote3', 'ajax_test3');

?>