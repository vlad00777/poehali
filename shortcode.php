<?PHP header("Content-Type: text/html; charset=utf-8");
require_once( ABSPATH . "wp-includes/pluggable.php" );
show_admin_bar( false );

//редирект после логина на главную
add_filter("login_redirect", "sp_login_redirect", 10, 3);
function sp_login_redirect($redirect_to, $request, $user)
	{
    if(is_array($user->roles))
        if(in_array('administrator', $user->roles))
            return home_url('/wp-admin/');
    return home_url();
	}
///////////////////////////////////////////////////
$id = get_current_user_id();
$gorod = get_user_meta($id, 'gorod');
global $gorodobl;
$gorodobl = $gorod[0];

if (isset($_POST['gorod'])) 
	{
	if ($_POST['gorod']==1) $gorod="АР Крым";
	if ($_POST['gorod']==2) $gorod="Винницкая область";
	if ($_POST['gorod']==3) $gorod="Волынская область";
	if ($_POST['gorod']==4) $gorod="Днепропетровская область";
	if ($_POST['gorod']==5) $gorod="Донецкая область";
	if ($_POST['gorod']==6) $gorod="Житомирская область";
	if ($_POST['gorod']==7) $gorod="Закарпатская область";
	if ($_POST['gorod']==8) $gorod="Запорожская область";
	if ($_POST['gorod']==9) $gorod="Ивано-Франковская область";
	if ($_POST['gorod']==10) $gorod="Киевская область";
	if ($_POST['gorod']==11) $gorod="Кировоградская область";
	if ($_POST['gorod']==12) $gorod="Луганская область";
	if ($_POST['gorod']==13) $gorod="Львовская область";
	if ($_POST['gorod']==14) $gorod="Николаевская область";
	if ($_POST['gorod']==15) $gorod="Одесская область";
	if ($_POST['gorod']==16) $gorod="Полтавская область";
	if ($_POST['gorod']==17) $gorod="Ровенская область";
	if ($_POST['gorod']==18) $gorod="Сумская область";
	if ($_POST['gorod']==19) $gorod="Тернопольская область";
	if ($_POST['gorod']==20) $gorod="Харьковская область";
	if ($_POST['gorod']==21) $gorod="Херсонская область";
	if ($_POST['gorod']==22) $gorod="Хмельницкая область";
	if ($_POST['gorod']==23) $gorod="Черкасская область";
	if ($_POST['gorod']==24) $gorod="Черниговская область";
	if ($_POST['gorod']==25) $gorod="Черновицкая область";

	if (isset($_POST['mailtorss'])) 
		{
		$mail = $_POST['mailtorss'];
		$user_id = username_exists($mail);
		if ( !$user_id and email_exists($mail) == false ) 
			{
			$ex = 1;
			$random_password = wp_generate_password( $length=7, $include_standard_special_chars=false );
			$user_id = wp_create_user( $mail, $random_password, $mail );
			$headers = 'От: Поехали на футбол <robot@poehalinafootball.com.ua>';
			wp_mail($mail, "Вы успешно зарегистрированы!", "Ваш логин для входа - $mail, пароль - $random_password. Поехали на футбол - http://poehalinafootball.com.ua", $headers);
			add_user_meta( $user_id, 'gorod', $gorod ); 
			echo "<script>alert('Вы успешно зарегистрированы! Данные для входа отправленны вам на e-mail.');</script>";
			} 
			if ($user_id and email_exists($mail) == true )
			{
			$ex = 0;
			echo "<script>alert('Данный пользователь существует! Если вы забыли пароль, перейдите по ссылке Забыли пароль? в левой части сайта.');</script>";
			}

		if (isset($_POST['rssmail']) and $ex == 1) 
			{	
			$cat = '';
			if (isset($_POST['ukr'])) {$cat.=$_POST['ukr'].',';}
			if (isset($_POST['lch'])) {$cat.=$_POST['lch'].',';}
			if (isset($_POST['le'])) {$cat.=$_POST['le'].',';}
			if (isset($_POST['all'])) {$cat.=$_POST['all'].',';}
			$cat = chop($cat, ',');
			$mail = $_POST['mailtorss'];	
			$sql = "SELECT * FROM rss WHERE mail='$mail'";
			$result = $wpdb->query($sql);
			if ($result == 0) 
				{
				$sql = "INSERT INTO rss (mail, category) VALUES ('$mail', '$cat')";
				$wpdb->query($sql);
				}
			}

		}
		else 
		{
		update_user_meta( $id, 'gorod', $gorod );
		}
	}
$k=0;
function math($attr){
global $wpdb, $k, $gorodobl;
?>
			<script>
			function closemath() {
			$('.vspform').css("display","none");
			}

			function closeobl() {
			$('.popup').css("display","none");
			}

			function validate_email(strEmail){
			validRegExp = /^[^@]+@[^@]+.[a-z]{2,}$/i;
			if ( strEmail.search(validRegExp) == -1 ){
			alert('Указанный e-mail адрес некорректен!');
			return false;
			}
			return true;
			}

			function get_id(id, chemp) {
			var data = {  
			action: 'vote',  
			id: ''+id
			};  
	  
	  $.ajax({
	  type: 'GET',
	  url: '/wp-admin/admin-ajax.php', 
	  data: data,
	  success: function(response) {  
		$('.loading').css("display","none");
		$('.vspform').css("display","block");
		var math = response.split(" - ");
		$('.vspform').html("<a OnClick='closemath(); return false;' href='#'><img style='position:absolute; right:-15px; top:-15px; width:30px; height:30px;' src='<?php echo $url = plugins_url().'/poehali'; ?>/error.png'></a><p align='center' style='font-weight:bold;'>Отправка заявки на матч</p><br><form class='otpforma' method='POST'><p>Название матча</p><input class='avtorizinp2 readonly' name='nmath' type='text' readonly value='"+response+"'><p>Поддержать команду</p><select name='podkom' class='avtorizinp2'><option>"+math[0]+"</option><option>"+math[1]+"</option></select><p>Количество человек для поездки</p><input class='avtorizinp2' name='kol' type='text'><p>Телефон</p><input class='avtorizinp2' id='tel' value='0' name='tel' maxlength='10' type='text'><p>E-mail</p><input name='mail' class='avtorizinp2 readonly' value='<?php $current_user = wp_get_current_user(); echo $current_user->user_email; ?>' type='text' readonly ><p>Ваше ФИО</p><input name='fio' class='avtorizinp2' type='text'><p>Область проживания</p><input class='avtorizinp2 readonly' name='oblast' type='text' value='<?php echo $gorodobl; ?>' readonly><input type='hidden' name='idobl' value='<?php $obl2 = $wpdb->get_row("SELECT * FROM oblast WHERE obl='$gorodobl'"); echo $obl2->id; ?>'><br><input class='submit2' value='Отправить заявку' align='right' type='submit'><input type='hidden' value='"+id+"' name='otpzayavka'><input type='hidden' value='"+chemp+"' name='chempmath'></form><p>Все поля обязательные для заполнения!<br><b style='font-size:11px;'>Не правильно заполненая заявка может быть отклонена в любой момент</b>!</p>");	
		},
		beforeSend : function(){
				$('.loading').css("display","block");
				$('.loading').html('<img src="<?php echo $url = plugins_url()."/poehali"; ?>/1.gif"><br>Загрузка формы...');
				}
		});  

	}
			</script>
<?php
$maths = $wpdb->get_results( "SELECT * FROM math WHERE chemp='$attr[math]' AND archive <> '1' ORDER BY time" );
$nowtime = time();
$imglch = get_template_directory_uri().'/images/img_03.jpg';
$imgle = get_template_directory_uri().'/images/img_14.jpg';
$imgukr = get_template_directory_uri().'/images/img_16.jpg';
$imgall = get_template_directory_uri().'/images/img_18.jpg';

if (count($maths)>0) 
	{
	if ($attr['math'] == 'lch') {$img = "<img alt='Матчи лиги чемпионов' style='display:block;' src='$imglch'>";} 
	if ($attr['math'] == 'le') {$img = "<img alt='Матчи лиги европы' style='display:block;' src='$imgle'>";} 
	if ($attr['math'] == 'ukr') {$img= "<img alt='Матчи чемпионата Украины' style='display:block;' src='$imgukr'>";} 
	if ($attr['math'] == 'all') {$img= "<img alt='Матчи сборной' style='display:block;' src='$imgall'>";} 
		echo "<div class='table_math'>$img<table class='tablemath' >";
	echo "<tr class='tittable'>";
	echo "<td style='width:12%;'>Дата матча</td>";
	echo "<td style='width:5%;'>День недели</td>";
	echo "<td style='width:25%;'>Название матча</td>";
	echo "<td style='width:15%;'>Город</td>";
	echo "<td style='width:8%;'>Цена*</td>";
	echo "<td style='width:18%;'>Отправить заявку</td>";
	echo "</tr>";
	foreach ( $maths as $math ) 
		{
		$k++;
		if ($nowtime-strtotime($math->time)>0) 
			{
			$wpdb->query("UPDATE math SET archive= '1' WHERE id='$math->id' ");  
			}
		if ($nowtime-strtotime($math->time)<0) 
			{
			$time = date('d.m.Y' ,strtotime($math->time));
			$nedelya = date('w' ,strtotime($math->time));
			if ($nedelya == 0) $nedelya = "ВС";
			if ($nedelya == 1) $nedelya = "ПН";
			if ($nedelya == 2) $nedelya = "ВТ";
			if ($nedelya == 3) $nedelya = "СР";
			if ($nedelya == 4) $nedelya = "ЧТ";
			if ($nedelya == 5) $nedelya = "ПТ";
			if ($nedelya == 6) $nedelya = "СБ";
			$path_array  = wp_upload_dir(); 
			$path = str_replace('\\', '/', $path_array['baseurl']."/emblems");
			$icon = $wpdb->get_row("SELECT * FROM image WHERE team='$math->team1'");
			$icon2 = $wpdb->get_row("SELECT * FROM image WHERE team='$math->team2'");
			if (!$gorodobl) 
				{
				echo "<tr class='strokamath'>";
				echo "<td>$time</td>";
				echo "<td>$nedelya</td>";
				echo "<td><img alt='$math->team1' src='$path/$icon->url'>$math->team1 - $math->team2<img alt='$math->team2' src='$path/$icon2->url'></td>";
				echo "<td>$math->city</td>";
				echo "<td><a href='#' OnClick='popup(); return false;'>узнать</a></td>";
				echo "<td><a href='#' OnClick='popup(); return false;'>Отправить заявку</a></td>";
				echo "</tr>"; 
				}
			else {
				echo "<tr class='strokamath'>";
				echo "<td>$time</td>";
				echo "<td>$nedelya</td>";
				echo "<td><img alt='$math->team1' src='$path/$icon->url'>$math->team1 - $math->team2 <img alt='$math->team2' src='$path/$icon2->url'></td>";
				echo "<td>$math->city</td>";
				echo "<td id='t$k'>"; 
				$idobl = $wpdb->get_row("SELECT * FROM oblast WHERE obl='$gorodobl'"); 
				$okonprice = $wpdb->get_row("SELECT * FROM price WHERE obl='$idobl->id' AND idmath='$math->id'"); 
				//echo $okonprice->cena;
				if (!$okonprice->cena) {
					$results = $wpdb->get_row("SELECT * FROM ceni WHERE kuda='$math->city' AND otkuda='$gorodobl'"); 
					$km = round($results->cena/1000); 
					$km = $km + 150;  
					if ($math->chemp == 'LE') {$stoim = ($km * 2 * 15)/48 + 100;}
					if ($math->chemp == 'LCH') {$stoim = ($km * 2 * 15)/48 + 200;}
					if ($math->chemp == 'UKR') {$stoim = ($km * 2 * 12)/49 + 50;}
					if ($math->chemp == 'ALL') {$stoim = (($km * 2 * 12)/49 + 50)* 1.2;}
					echo round($stoim)." грн.";
					}
				else {
					echo $okonprice->cena." грн.";
					}
				echo "</td>";
				echo "<td><a href='#' onClick='get_id($math->id, \"$math->chemp\"); return false;'>Отправить заявку</a></td>";
				echo "</tr>";
				}
		
			}
		}
	echo "</table></div><br>";
	}

	if (isset($_POST['otpzayavka']) and isset($_POST['chempmath'])) 
	{
	$podkom = $_POST['podkom'];
	$kol = $_POST['kol'];
	$tel = $_POST['tel'];
	$mail = $_POST['mail'];
	$fio = $_POST['fio'];
	$obl = $_POST['oblast'];
	$idobl = $_POST['idobl'];
	$idmath = $_POST['otpzayavka'];
	$nmath = $_POST['nmath'];
	$leage = $_POST['chempmath'];

	if ($attr['math'] == strtolower($leage)) 
		{
		if(!$_POST['tel'] or !$_POST['mail'] or !$_POST['fio'] or $_POST['tel'] == 0 or $_POST['tel'] == 0 ) 
			{
			echo "<script>alert('Вы не заполнили одно из полей.');</script>"; 
			}
		else 
			{
			$ok = $wpdb->get_row("SELECT * FROM zayavka WHERE idmath='$idmath' AND mail='$mail'");
			if (!$ok) 
				{
				$sql = "INSERT INTO zayavka (idmath, podkom, kol, obl, tel, mail, fio, del) VALUES ('$idmath', '$podkom', '$kol', '$idobl', '$tel', '$mail', '$fio', '0')";
				$wpdb->query($sql);
				$nazv = $wpdb->get_row("SELECT * FROM math WHERE id='$idmath'");
				wp_mail("zayavki.poehali@gmail.com", "Новая заявка", "Матч: $nazv->team1 - $nazv->team2<br> Поддержка команды: $podkom<br> Кол-во: $kol<br> Область: $obl<br> Телефон: $tel<br> Почта: $mail<br> ФИО: $fio");
				?>
				<script>
				$(document).ready(function() {
				$('.vspform').css("display","block");
				$('.vspform').html("<a OnClick='closemath(); return false;' href='#'><img style='position:absolute; right:-15px; top:-15px; width:30px; height:30px;' src='<?php echo $url = plugins_url().'/poehali'; ?>/error.png'></a>Заявка отправлена!<br> Предположительно за неделю до матча, наш менеджер свяжется с вами.");
				});
				</script>
				<?php
				}
			else 
				{
				?>
				<script>
				$(document).ready(function() {
				$('.vspform').css("display","block");
				$('.vspform').html("<a OnClick='closemath(); return false;' href='#'><img style='position:absolute; right:-15px; top:-15px; width:30px; height:30px;' src='<?php echo $url = plugins_url().'/poehali'; ?>/error.png'></a>Вы уже отправляли заявку на этот матч!");
				});
				</script>
				<?php
				}
			}
		}
	}
}

add_action('wp_ajax_vote', 'ajax_test');
add_action( 'wp_ajax_nopriv_vote', 'ajax_test' );

function ajax_test () {
$id = (int)$_GET['id'];
global $wpdb;
$r = $wpdb->get_row( "SELECT * FROM math WHERE id = '$id'" );
ob_clean();
echo $r->team1." - ".$r->team2;
die();
}

add_shortcode('shortcode', 'math');
?>
