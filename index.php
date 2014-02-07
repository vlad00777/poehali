<?php
/*
Plugin Name: Поехали на футбол
Plugin URI: http://poehalinafootball.com.ua
Description: Плагин добавления, редактирования матчей, а так же для вывода матчей в виде таблиц. 
Version: 1.0.0
Author: Vlad00777
Author URI: http://#
*/
/*  Copyright 2013  Vlad00777  (email : vlad00777@gmail.com)
	Все права защищены. 
*/

class Model_Vk {
    private $access_token;
    private $url = "https://api.vk.com/method/";

    public function __construct($access_token) {

        $this->access_token = $access_token;
    }
    public function method($method, $params = null) {

        $p = "";
        if( $params && is_array($params) ) {
            foreach($params as $key => $param) {
                $p .= ($p == "" ? "" : "&") . $key . "=" . urlencode($param);
            }
        }
        $response = file_get_contents($this->url . $method . "?" . ($p ? $p . "&" : "") . "access_token=" . $this->access_token);

        if( $response ) {
            return json_decode($response);
        }
        return false;
    }
}
// Конец класса публикаций вконтакт


class picture {
	
	private $image_file;
	
	public $image;
	public $image_type;
	public $image_width;
	public $image_height;
	
	
	public function __construct($image_file) {
		$this->image_file=$image_file;
		$image_info = getimagesize($this->image_file);
		$this->image_width = $image_info[0];
		$this->image_height = $image_info[1];
		switch($image_info[2]) {
			case 1: $this->image_type = 'gif'; break;//1: IMAGETYPE_GIF
			case 2: $this->image_type = 'jpeg'; break;//2: IMAGETYPE_JPEG
			case 3: $this->image_type = 'png'; break;//3: IMAGETYPE_PNG
			case 4: $this->image_type = 'swf'; break;//4: IMAGETYPE_SWF
			case 5: $this->image_type = 'psd'; break;//5: IMAGETYPE_PSD
			case 6: $this->image_type = 'bmp'; break;//6: IMAGETYPE_BMP
			case 7: $this->image_type = 'tiffi'; break;//7: IMAGETYPE_TIFF_II (порядок байт intel)
			case 8: $this->image_type = 'tiffm'; break;//8: IMAGETYPE_TIFF_MM (порядок байт motorola)
			case 9: $this->image_type = 'jpc'; break;//9: IMAGETYPE_JPC
			case 10: $this->image_type = 'jp2'; break;//10: IMAGETYPE_JP2
			case 11: $this->image_type = 'jpx'; break;//11: IMAGETYPE_JPX
			case 12: $this->image_type = 'jb2'; break;//12: IMAGETYPE_JB2
			case 13: $this->image_type = 'swc'; break;//13: IMAGETYPE_SWC
			case 14: $this->image_type = 'iff'; break;//14: IMAGETYPE_IFF
			case 15: $this->image_type = 'wbmp'; break;//15: IMAGETYPE_WBMP
			case 16: $this->image_type = 'xbm'; break;//16: IMAGETYPE_XBM
			case 17: $this->image_type = 'ico'; break;//17: IMAGETYPE_ICO
			default: $this->image_type = ''; break;
		}
		$this->fotoimage();
	}
	
	private function fotoimage() {
		switch($this->image_type) {
			case 'gif': $this->image = imagecreatefromgif($this->image_file); break;
			case 'jpeg': $this->image = imagecreatefromjpeg($this->image_file); break;
			case 'png': $this->image = imagecreatefrompng($this->image_file); break;
		}
	}
	
	public function autoimageresize($new_w, $new_h) {
		$difference_w = 0;
		$difference_h = 0;
		if($this->image_width < $new_w && $this->image_height < $new_h) {
			$this->imageresize($this->image_width, $this->image_height);
		}
		else {
			if($this->image_width > $new_w) {
				$difference_w = $this->image_width - $new_w;
			}
			if($this->image_height > $new_h) {
				$difference_h = $this->image_height - $new_h;
			}
				if($difference_w > $difference_h) {
					$this->imageresizewidth($new_w);
				}
				elseif($difference_w < $difference_h) {
					$this->imageresizeheight($new_h);
				}
				else {
					$this->imageresize($new_w, $new_h);
				}
		}
	}
	
	public function percentimagereduce($percent) {
		$new_w = $this->image_width * $percent / 100;
		$new_h = $this->image_height * $percent / 100;
		$this->imageresize($new_w, $new_h);
	}
	
	public function imageresizewidth($new_w) {
		$new_h = $this->image_height * ($new_w / $this->image_width);
		$this->imageresize($new_w, $new_h);
	}
	
	public function imageresizeheight($new_h) {
		$new_w = $this->image_width * ($new_h / $this->image_height);
		$this->imageresize($new_w, $new_h);
	}
	
	public function imageresize($new_w, $new_h) {
		$new_image = imagecreatetruecolor($new_w, $new_h);
		imageAlphaBlending($new_image, false);
		imageSaveAlpha($new_image, true);
		
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_w, $new_h, $this->image_width, $this->image_height);
		$this->image_width = $new_w;
		$this->image_height = $new_h;
		$this->image = $new_image;
	}
	
	public function imagesave($image_type='jpeg', $image_file=NULL, $image_compress=100, $image_permiss='') {
		if($image_file==NULL) {
			switch($this->image_type) {
				case 'gif': header("Content-type: image/gif"); break;
				case 'jpeg': header("Content-type: image/jpeg"); break;
				case 'png': header("Content-type: image/png"); break;
			}
		}
		switch($this->image_type) {
			case 'gif': imagegif($this->image, $image_file); break;
			case 'jpeg': imagejpeg($this->image, $image_file, $image_compress); break;
			case 'png': imagepng($this->image, $image_file); break;
		}
		if($image_permiss != '') {
			chmod($image_file, $image_permiss);
		}
	}
	
	public function imageout() {
		imagedestroy($this->image);
	}
	
	public function __destruct() {
		
	}
	
}

function rus2translit($text)
{
    $rus_alphabet = array(
        'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
        'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
        'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
        'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
        'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
    );
    
    $rus_alphabet_translit = array(
        'A', 'B', 'V', 'G', 'D', 'E', 'IO', 'ZH', 'Z', 'I', 'I',
        'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
        'H', 'C', 'CH', 'SH', 'SH', '`', 'Y', '`', 'E', 'IU', 'IA',
        'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'i',
        'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
        'h', 'c', 'ch', 'sh', 'sh', '`', 'y', '`', 'e', 'iu', 'ia'
    );
    
    return str_replace($rus_alphabet, $rus_alphabet_translit, $text);
}
//Конец функции транслита

function poehali_activation() {
global $wpdb;
$sql = "CREATE TABLE
    `math` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `chemp` VARCHAR(10) NOT NULL,
        `team1` VARCHAR(20) NOT NULL,
		`team2` VARCHAR(20) NOT NULL,
		`time` TIMESTAMP NOT NULL,
		`city` VARCHAR(40) NOT NULL,
		`archive` INT(1) NOT NULL,
        PRIMARY KEY(`id`)
    )
	CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$sql = "CREATE TABLE
    `image` (
        `team` VARCHAR(20) NOT NULL,
        `url` VARCHAR(50) NOT NULL,
        PRIMARY KEY(`team`)
    )
	CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$sql = "CREATE TABLE `oblast` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `obl` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$sql = "INSERT INTO `oblast` (`id`, `obl`) VALUES
(1, 'АР Крым'),
(2, 'Винницкая область'),
(3, 'Волынская область'),
(4, 'Днепропетровская область '),
(5, 'Донецкая область'),
(6, 'Житомирская область'),
(7, 'Закарпатская область'),
(8, 'Запорожская область'),
(9, 'Ивано-Франковская область'),
(10, 'Киевская область'),
(11, 'Кировоградская область'),
(12, 'Луганская область'),
(13, 'Львовская область'),
(14, 'Николаевская область'),
(15, 'Одесская область'),
(16, 'Полтавская область'),
(17, 'Ровенская область'),
(18, 'Сумская область'),
(19, 'Тернопольская область'),
(20, 'Харьковская область'),
(21, 'Херсонская область'),
(22, 'Хмельницкая область'),
(23, 'Черкасская область'),
(24, 'Черниговская область'),
(25, 'Черновицкая область');";
$wpdb->query($sql);


$sql = "CREATE TABLE `zayavka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmath` int(11) NOT NULL,
  `podkom` varchar(15) NOT NULL,
  `kol` int(11) NOT NULL,
  `obl` varchar(60) NOT NULL,
  `tel` int(11) NOT NULL,
  `mail` varchar(120) NOT NULL,
  `fio` varchar(200) NOT NULL,
  `del` int(1) NOT NULL,
  `edet` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$sql = "CREATE TABLE `ceni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cena` int(11) NOT NULL,
  `otkuda` varchar(120) NOT NULL,
  `kuda` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$sql = "CREATE TABLE `rss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(120) NOT NULL,
  `category` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$sql = "CREATE TABLE `text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmath` int(11) NOT NULL,
  `obl` varchar(100) NOT NULL,
  `mailtxt` varchar(900) NOT NULL,
  `teltxt` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idzayavka` int(11) NOT NULL,
  `idmath` int(11) NOT NULL,
  `res` varchar(12) NOT NULL,
  `summ` varchar(5) NOT NULL,
  `desc` text NOT NULL,
  `trans` int(11) NOT NULL,
  `pay` varchar(10) NOT NULL,
  `mob` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `price` (
  `idmath` int(11) NOT NULL AUTO_INCREMENT,
  `obl` varchar(60) NOT NULL,
  `cena` int(11) NOT NULL,
  PRIMARY KEY (`idmath`)
) CHARACTER SET utf8 COLLATE utf8_general_ci";
$wpdb->query($sql);

$path_array  = wp_upload_dir();
mkdir($path_array['basedir']."\emblems", 0700);
}
 
function poehali_deactivation() {
global $wpdb;
}
//Конец функции деактивации плагина

/*начало главной формы*/
function poehali_form() {
echo "<p>Hello</p>";
 }
/*конец главной формы*/

/*начало формы добавления*/ 
function poehali_add_math() {

if (!isset($_POST['team1'])) {
?>
<script>
function readURL(input, nomer)
{
      if (input.files && input.files[0])
              {
                    var reader = new FileReader();
                   reader.onload = function (e)
                                          {
                                                jQuery('#im'+nomer)
                                                .attr('src',e.target.result)
                                                .height(100);
                                          };
                   reader.readAsDataURL(input.files[0]);
                   }
}


function get_img(im) {
jQuery(document).ready(function ($) {
id = document.getElementById(im).value;
    var data = {  
        action: 'vote2',  
        id: ''+id  
    };  
  
  $.ajax({
  type: 'GET',
  url: '/wp-admin/admin-ajax.php', 
  data: data,
  success: function(response) 
	{  
	jQuery('#i'+im).attr('src',"<?php $path_array  = wp_upload_dir(); echo $path = str_replace('\\', '/', $path_array['baseurl']."/emblems/"); ?>"+response).height(100);
    }
	});  
});
}
</script>

<form  action="" method="POST" enctype="multipart/form-data">
<p>Выберите тип матча</p>
<select name="chemp">
<option value="UKR">Чемпионат и кубок Украины</option>
<option value="LCH">Лига чемпионов</option>
<option value="LE">Лига европы</option>
<option value="ALL">Другое</option>
</select>
<p>Введите команду хозяев и команду гостей</p>
<input type="text" id="m1" onChange="get_img('m1');" name="team1"> - <input id="m2" onChange="get_img('m2');" type="text" name="team2">
<p>Введите дату матча (хх.хх.хххх)</p>
<input type="text" name="date">
<p>Введите время матча</p>
<input type="text" name="time">
<p>Введите город проведения матча</p>
<input type="text" name="city">
<p>Загрузите логотип команды 1</p>
<input type="file" id="imgInp1" name="image1" onchange="readURL(this, 1);" accept="image/png">
<img id="im1" src="#">
<p>Загрузите логотип команды 2</p>
<input type="file" id="imgInp2" name="image2" onchange="readURL(this, 2);" accept="image/png">
<img id="im2" src="#">
<br><br>
<input type="submit" value="Добавить матч">
</form>

<?php 
}
if (isset($_POST['team1'])) {
global $wpdb;
$date_elements  = explode(".",$_POST['date']);
$date_elements2  = explode(":",$_POST['time']);
$time = "$date_elements[2]-$date_elements[1]-$date_elements[0] $date_elements2[0]:$date_elements2[1]:00"; 
$sql = "INSERT INTO math (chemp, team1, team2, time, city, archive) VALUES ('$_POST[chemp]', '$_POST[team1]', '$_POST[team2]', '$time', '$_POST[city]', '0')";
$wpdb->query($sql);

$ligamath = $_POST['chemp'];
if ($ligamath == 'UKR') $lma = 1;
if ($ligamath == 'LCH') $lma = 2;
if ($ligamath == 'LE') $lma = 3;
if ($ligamath == 'ALL') $lma = 4;
//Начало выбора пользователей и рассылки писем о новом матче
$mails = $wpdb->get_results("SELECT * FROM rss"); 
foreach ( $mails as $mail ) 
{
	$m = explode(",", $mail->category);
	if ($m[0]==$lma or $m[1]==$lma or $m[2]==$lma or $m[3]==$lma) 
		{
		wp_mail($mail->mail, 'Добавлен новый матч!', '<img src="http://poehalinafootball.com.ua/uploads/posts/2012-03/1331726853_poehali-na-futbol-na-ukr.jpg" width="250"><br>Поехали на футбол!<br>Добавлен новый матч: '.$_POST['team1'].' - '.$_POST['team2'].', который состоится '.$_POST['date'].'.<br> Отправьте заявку на сайте: http://poehalinafootball.com.ua');
		}
}
//Конец рассылки о новом матче

//Подготовка областей для гугл матриц
$arr = array();
$results = $wpdb->get_results("SELECT * FROM oblast"); 
foreach ( $results as $res ) 
	{
	$arr[] .= str_replace(" ", "%20", $res->obl ); 
	}
//Конец подготовки

//Получаем данные о расстояниях между областями и городом проведения
foreach ($arr as $value) {
$val = str_replace("%20", " ", $value);
$results = $wpdb->get_row("SELECT * FROM ceni WHERE kuda='$_POST[city]' and otkuda='$val'");	

if (!$results or $results<1) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
		CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
    );
 
    $tuCurl = curl_init();
    $params = "origins=$value&destinations=$_POST[city]&mode=driving&language=ru-RU&sensor=false";
    curl_setopt($tuCurl, CURLOPT_URL, "http://maps.googleapis.com/maps/api/distancematrix/json?".$params);
    curl_setopt_array( $tuCurl, $options );
    $tuData = curl_exec($tuCurl);
    curl_close($tuCurl); 
    $json = json_decode($tuData);
    $distance = $json->rows[0]->elements[0]->distance->value;    
    $sql = "INSERT INTO ceni (cena, otkuda, kuda) VALUES ('".$distance."', '".str_replace("%20", " ", $value)."', '".$_POST['city']."')";
	$wpdb->query($sql);
	}
}
//Конец получения данных

//Публикуем новость вконтакте
$access_token = "6508ea584f3afd5c0af21cea74dfef3dbf699ab60d7e6aff98d90897f556e5933aae1c602fe69424c43c8";
$user_id = "15684561";
$vk = new Model_Vk($access_token);
$params = array(
    "owner_id" => "-49142975",
    "message" => "Добавлен новый матч!\n $_POST[team1] - $_POST[team2].\n Дата и место проведения матча - $date_elements[0].$date_elements[1].$date_elements[2] $_POST[city].\n Подробности на сайте: http://poehalinafootball.com.ua"
);
$post = $vk->method("wall.post", $params);

echo "Матч добавлен и опубликован Вконтакте!";
}

//Проверяем замену первой картинки
if ($_FILES['image1']['name'] != '') {
global $wpdb;
$path_array  = wp_upload_dir();
$path = str_replace('\\', '/', $path_array['basedir']."/emblems");
$old_name1 = $_FILES["image1"]["name"];
$old_name1 = rus2translit($old_name1);

iconv('UTF-8', 'windows-1251', $old_name1);
//move_uploaded_file($_FILES["image1"]["tmp_name"],$path. "/" . $old_name1);

$new_image = new picture($_FILES["image1"]["tmp_name"]);
$new_image->imageresizeheight(15);
$new_image->imagesave($new_image->image_type, $path. "/" . $old_name1);
$new_image->imageout();

$sql = "INSERT INTO image (team, url) VALUES ('$_POST[team1]', '$old_name1')";
$wpdb->query($sql);

}

//Проверяем замену второй картинки
if ($_FILES['image2']['name'] != '') {
global $wpdb;
$path_array  = wp_upload_dir();
$path = str_replace('\\', '/', $path_array['basedir']."/emblems");
$old_name2 = $_FILES["image2"]["name"];
$old_name2 = rus2translit($old_name2);

iconv('UTF-8', 'windows-1251', $old_name2);
//move_uploaded_file($_FILES["image2"]["tmp_name"],$path. "/" . $old_name2);

$new_image = new picture($_FILES["image2"]["tmp_name"]);
$new_image->imageresizeheight(15);
$new_image->imagesave($new_image->image_type, $path. "/" . $old_name2);
$new_image->imageout();

$sql = "INSERT INTO image (team, url) VALUES ('$_POST[team2]', '$old_name2')";
$wpdb->query($sql);
}

}
/*конец формы добавления*/

/*начало формы просмотра*/
function poehali_view_math() {
global $wpdb;
$ids = $wpdb->get_results( "SELECT * FROM math WHERE archive <> '1' ORDER BY time" );
echo "<h3>Текущие матчи</h3>";
echo "<table style='border-collapse:collapse; width:850px;' border='1'>";
echo "<tr style='line-height:25px; text-align:center; background:#bacad0; font-weight:bold;'>";
echo "<td>№</td>";
echo "<td>Хозяева</td>";
echo "<td>Гости</td>";
echo "<td>Чемпионат</td>";
echo "<td>Дата и время</td>";
echo "<td>Город</td>";
echo "<td>Редактировать</td>";
echo "<td>Удалить</td>";
echo "</tr>";
//Не архивные матчи
if (!isset($_GET['archive'])) 
	{
	foreach ( $ids as $id ) 
		{
		$time = date('d.m.Y H:i' ,strtotime($id->time));
		echo "<tr style='line-height:25px; text-align:center;'>";
		echo "<td>".$id->id."</td>";
		echo "<td>".$id->team1."</td>";
		echo "<td>".$id->team2."</td>";
		echo "<td>".$id->chemp."</td>";
		echo "<td>".$time."</td>";
		echo "<td>".$id->city."</td>";
		echo "<td><a href='admin.php?page=edit&id=$id->id'>Редактировать</a></td>";
		echo "<td><a href='admin.php?page=delete&id=$id->id'>Удалить</a></td>";
		echo "</tr>";
		}
	echo "</table>";
	echo "<h4><a href='admin.php?page=prosmotr&archive=1'>Архивные матчи</a></h4>";
	
	
	}
//Аривные матчи
else 
	{
	echo "<h3>Архивные матчи</h3>";
	$ids = $wpdb->get_results( "SELECT * FROM math WHERE archive = '1' ORDER BY time" );
	foreach ( $ids as $id ) 
		{
		$time = date('d.m.Y H:i' ,strtotime($id->time));
		echo "<tr style='line-height:25px; text-align:center;'>";
		echo "<td>".$id->id."</td>";
		echo "<td>".$id->team1."</td>";
		echo "<td>".$id->team2."</td>";
		echo "<td>".$id->chemp."</td>";
		echo "<td>".$time."</td>";
		echo "<td>".$id->city."</td>";
		echo "<td></td>";
		echo "<td></td>";
		echo "</tr>";
		}
	echo "</table>";
	}
}
/*конец формы просмотра*/
  
/*начало формы редактирования*/
function poehali_edit_math() {
global $wpdb;
//Проверка перехода со страницы просмотра
	if (isset($_GET['id'])) 
		{
		$id = $wpdb->get_row( "SELECT * FROM math WHERE id=$_GET[id]" );
		$time = date('H:i' ,strtotime($id->time));
		$date = date('d.m.Y' ,strtotime($id->time));
		$path_array  = wp_upload_dir(); 
		$path = str_replace('\\', '/', $path_array['baseurl']."/emblems");
		$t1 = $id->team1;
		$t2 = $id->team2;
		if (!isset($_POST['red'])) 
			{
		?>
<form  action="" method="POST" enctype="multipart/form-data">
<p>Выберите тип матча</p>
<select name="chemp">
<option <?php if ($id->chemp=='UKR') echo "selected"; ?> value="UKR">Чемпионат и кубок Украины</option>
<option <?php if ($id->chemp=='LCH') echo "selected"; ?> value="LCH">Лига чемпионов</option>
<option <?php if ($id->chemp=='LE') echo "selected"; ?> value="LE">Лига европы</option>
<option <?php if ($id->chemp=='ALL') echo "selected"; ?> value="ALL">Другое</option>
</select>
<p>Введите команду хозяев и команду гостей</p>
<input type="text" value="<?php echo $id->team1; ?>" onchange="image23();" name="team1"> - <input value="<?php echo $id->team2; ?>" type="text" name="team2">
<p>Введите дату матча (хх.хх.хххх)</p>
<input type="text" value="<?php echo $date; ?>" name="date">
<p>Введите время матча</p>
<input type="text" value="<?php echo $time; ?>" name="time">
<p>Введите город проведения матча</p>
<input type="text" value="<?php echo $id->city; ?>" name="city">
<p>Логотип команды 1</p>
<img height="100" id="im1" src="<?php $image = $wpdb->get_row("SELECT * FROM image WHERE team='$t1'"); echo $path."/".$image->url; ?>">
<p>Логотип команды 2</p>
<img height="100" id="im2" src="<?php $image = $wpdb->get_row("SELECT * FROM image WHERE team='$t2'"); echo $path."/".$image->url; ?>">
<input name="red" value="1" type="hidden">
<br><br>
<input type="submit" value="Редактировать матч">
</form>
		<?php
			}
		if (isset($_POST['red'])) 
			{
			global $wpdb;
			$date_elements  = explode(".",$_POST['date']);
			$date_elements2  = explode(":",$_POST['time']);
			$time = "$date_elements[2]-$date_elements[1]-$date_elements[0] $date_elements2[0]:$date_elements2[1]:00";
			$sql = "UPDATE math SET chemp='$_POST[chemp]', team1='$_POST[team1]', team2='$_POST[team2]', time='$time', city='$_POST[city]' WHERE id=$id->id";
			$wpdb->query($sql);
			echo "Матч отредактирован!";
			}
	
	}
//Если перешли на страницу напрямую	
else 
	{
	echo "<h4>Выберите название матча, для редактирования</h4>";
	echo "<form action=\"\" method=\"GET\">";
	echo "<input type=\"hidden\" name=\"page\" value=\"edit\">";
	echo "<select name=\"id\">";
	$ids = $wpdb->get_results("SELECT * FROM math WHERE archive='0'");
	foreach ( $ids as $id ) 
		{
		echo "<option value=\"$id->id\">".$id->team1." - ".$id->team2." ".$id->chemp."</option>";
		}
	echo "</select>";
	echo "<input type=\"submit\" value=\"Редактировать\">";
	echo "</form>";
	}
}
/*конец формы редактирования*/  

//начало формы удаления
function poehali_delete_math() {
global $wpdb;
	if (isset($_GET['id'])) 
		{
		$wpdb->query("DELETE FROM math WHERE id='$_GET[id]'");
		echo "Матч удален!";
		}
	else 
		{
		echo "Для удаления матча, перейдите в раздел Просмотра матчей.";
		}
}
//Конец формы удаления

//начало функции просмотра заявок
function poehali_view_zayavka() {
global $wpdb;
//начало вывода селекта с матчами
$ids = $wpdb->get_results("SELECT * FROM math WHERE archive='0'");
echo "<p><b>Форма поиска заявок</b></p><form action='' method='GET'><p style='margin:0; padding:5px 0px;'>Название матча</p><select name='math'>";
foreach ( $ids as $id ) 
	{
	echo "<option value=\"$id->id\">".$id->team1." - ".$id->team2." ".$id->chemp."</option>";
	}
echo "</select><select name='oblzayavka'><option value='0'>Все регионы</option>";
//Конец вывода селекта с матчами

//начало вывода селекта с областями
$obls = $wpdb->get_results("SELECT * FROM oblast");
foreach ( $obls as $obl ) 
	{
	echo "<option value='$obl->id'>$obl->obl</option>";
	}
echo "</select><input type='hidden' name='page' value='view'><input value='Поиск' type='submit'></form>";
//конец вывода селекта с областями

//проверка на выбор конкретного матча с формы
if (isset($_GET['math']) and isset($_GET['oblzayavka'])) 
	{
	?>
	<script>
function updzayavka(id) {
if (jQuery("#change"+id).attr("checked") == "checked") {
edet = 1;
}
else {edet=0;}
  var data = {  
        action: 'vote3',  
        id: id,
		edet: edet	
    };  
  
  jQuery.ajax({
  type: 'GET',
  url: '/wp-admin/admin-ajax.php', 
  data: data,
  success: function(response) {  
	alert('Обновлено');
    }
	});  
}
</script>
	<?php
	//вывод ccылок на отчеты и заголовок таблицы
	echo "<a class='download' href='/?otchet=1&create=$_GET[math]'>Скачать отчет</a><a class='download' href='/?otchet=edet&create=$_GET[math]'>Скачать отчет, тех кто едет</a>";
	echo "<br><table style='border-collapse:collapse; width:900px;' border='1'><tr style='line-height:25px; text-align:center; background:#bacad0; font-weight:bold;'><td>Название матча</td><td>Поддержка команды</td><td>Количество</td><td>Область проживания</td><td>Телефон</td><td>Почта</td><td>ФИО</td><td>Едет?</td><td>Оплата</td></tr>";
	///////////////////////////////////////////////////////////////
	//проверка выбора определенной области
	$obok = $wpdb->get_row("SELECT * FROM oblast WHERE id='$_GET[oblzayavka]'");
	if ($_GET['oblzayavka'] > 0) 
		{
		$zayavki = $wpdb->get_results("SELECT * FROM zayavka WHERE idmath='$_GET[math]' AND obl='$obok->obl' AND del='0'");
		}
	//конец проверки на конкретную область 
	//начало проверки на все области
	else 
		{
		$zayavki = $wpdb->get_results("SELECT * FROM zayavka WHERE idmath='$_GET[math]' AND del='0'");
		}
	//////////////////////////////////////////////
	$kolchel=0;
	$krim=$vin=$vol=$dne=$don=$zhi=$zak=$zap=$ivf=$kie=$kir=$lug=$lvo=$nik=$ode=$pol=$rov=$sum=$ter=$har=$her=$hme=$chk=$chr=$chrno=0;
	$skrim=$svin=$svol=$sdne=$sdon=$szhi=$szak=$szap=$sivf=$skie=$skir=$slug=$slvo=$snik=$sode=$spol=$srov=$ssum=$ster=$shar=$sher=$shme=$schk=$schr=$schrno=0;

	foreach ( $zayavki as $zayavka ) 
		{
		//выводим таблицу с заявками
		$mathname = $wpdb->get_row("SELECT * FROM math WHERE id='$zayavka->idmath'");	
		if ($zayavka->edet == 1) $cheked="checked";
		if ($zayavka->edet == 0) $cheked="";
		echo "<tr style='line-height:25px; text-align:center;'>";
		echo "<td>".$mathname->team1." - ".$mathname->team2."</td>";
		echo "<td>".$zayavka->podkom."</td>";
		echo "<td>".$zayavka->kol."</td>";
		$obl2 = $wpdb->get_row("SELECT * FROM oblast WHERE id='$zayavka->obl'"); 
		echo "<td>".$obl2->obl."</td>";
		echo "<td>0".$zayavka->tel."</td>";
		echo "<td>".$zayavka->mail."</td>";
		echo "<td>".$zayavka->fio."</td>";
		echo "<td><input type='checkbox' id='change".$zayavka->id."' name='edet' value='".$zayavka->id."' OnChange='updzayavka(".$zayavka->id.");' ".$cheked."></td>";
		$payres = $wpdb->get_row("SELECT * FROM payment WHERE idzayavka='$zayavka->id' AND res='success'");
		$payres2 = $wpdb->get_row("SELECT * FROM payment WHERE idzayavka='$zayavka->id' AND res='delayed'");
		if (!empty($payres->id)) { 
			$sost = 'Оплачено';
			if ($payres->pay == 'card') $cherez = 'карту';
			if ($payres->pay == 'delayed') $cherez = 'наличными';
			if ($payres->pay == 'liqpay') $cherez = 'liqpay';
			$details = 'Оплачено '.$payres->summ.' через '.$cherez.' транзакция - '.$payres->trans;
		}
		if (empty($payres->id) and !empty($payres2->id)) { 
		$sost = 'Ждем оплаты';
		$details = 'Ожидаем оплаты '.$payres2->summ.' через терминал транзакция - '.$payres2->trans;
		$str = "
		<request>
		<version>1.2</version>
		<action>view_transaction</action>
		<merchant_id>i7825555227</merchant_id>
		<transaction_id>".$payres2->trans."</transaction_id> 
		<transaction_order_id>".$zayavka->id."</transaction_order_id>
		</request>
		";

		}
		if (empty($payres->id) and empty($payres2->id)) { 
		$sost = 'Не оплачено';
		$details = 'Ожидаем оплаты';
		}
		
		echo "<td><a href='#' title='".$details."'>".$sost."</a></td>";
		echo "</tr>";
		
		$nazvobl = $wpdb->get_row("SELECT * FROM oblast WHERE id='$zayavka->obl'");
		//начало подсчета стоимости
		$viruchka = $wpdb->get_row("SELECT * FROM ceni WHERE otkuda='$nazvobl->obl' AND kuda='$mathname->city'");
		
		$km = $viruchka->cena;
		$km = round($km/1000); 
		$km = $km + 150;  
		
		if ($mathname->chemp == 'LE') {$stoim = ($km * 2 * 15)/48 + 100;}
		if ($mathname->chemp == 'LCH') {$stoim = ($km * 2 * 15)/48 + 200;}
		if ($mathname->chemp == 'UKR') {$stoim = ($km * 2 * 12)/49 + 50;}
		if ($mathname->chemp == 'ALL') {$stoim = (($km * 2 * 12)/49 + 50)* 1.2;}
		$kolchel += $zayavka->kol;
		$stoim = $stoim * $zayavka->kol;
		$stoim = round($stoim);
		$dengi += round($stoim);
		
		//Подсчет по областям и помещение в переменные
		if ($zayavka->obl == 1) { $krim += $zayavka->kol; $skrim +=$stoim; }
		if ($zayavka->obl == 2) { $vin+= $zayavka->kol; $svin+=$stoim; }
		if ($zayavka->obl == 3) { $vol+= $zayavka->kol; $svol+=$stoim;}
		if ($zayavka->obl == 4) { $dne+= $zayavka->kol; $sdne+=$stoim;}
		if ($zayavka->obl == 5) { $don+= $zayavka->kol; $sdon+=$stoim;}
		if ($zayavka->obl == 6) { $zhi+= $zayavka->kol; $szhi+=$stoim; }
		if ($zayavka->obl == 7) { $zak+= $zayavka->kol; $szak+=$stoim;}
		if ($zayavka->obl == 8) { $zap+= $zayavka->kol; $szap+=$stoim;}
		if ($zayavka->obl == 9) { $ivf+= $zayavka->kol; $sivf+=$stoim; }
		if ($zayavka->obl == 10) { $kie+= $zayavka->kol; $skie+=$stoim;}
		if ($zayavka->obl == 11) { $kir+= $zayavka->kol; $skir+=$stoim; }
		if ($zayavka->obl == 12) { $lug+= $zayavka->kol; $slug+=$stoim;}
		if ($zayavka->obl == 13) { $lvo+= $zayavka->kol; $slvo+=$stoim;}
		if ($zayavka->obl == 14) { $nik+= $zayavka->kol; $snik+=$stoim;}
		if ($zayavka->obl == 15) { $ode+= $zayavka->kol;  $sode+=$stoim;}
		if ($zayavka->obl == 16) { $pol+= $zayavka->kol; $spol+=$stoim;}
		if ($zayavka->obl == 17) { $rov+= $zayavka->kol; $srov+=$stoim;}
		if ($zayavka->obl == 18) { $sum+= $zayavka->kol; $ssum+=$stoim;}
		if ($zayavka->obl == 19) { $ter+= $zayavka->kol; $ster+=$stoim; }
		if ($zayavka->obl == 20) { $har+= $zayavka->kol; $shar+=$stoim;}
		if ($zayavka->obl == 21) { $her+= $zayavka->kol; $sher+=$stoim; }
		if ($zayavka->obl == 22) { $hme+= $zayavka->kol; $shme+=$stoim;}
		if ($zayavka->obl == 23) { $chk+= $zayavka->kol; $schk+=$stoim;}
		if ($zayavka->obl == 24) { $chr+= $zayavka->kol; $schr+=$stoim;}
		if ($zayavka->obl == 25) { $chrno+= $zayavka->kol; $schrno+=$stoim;}
		}
		//конец цикла вывода заявки
	echo "</table>";
	//вывод нижней таблицы с ценами и людьми
	echo "<br><table style='border-collapse:collapse;  ' border='1'><tr class='tittable'>";	
	echo "<td>Крым</td>";
	echo "<td>Винница</td>";
	echo "<td>Волынь</td>";
	echo "<td>Днепр</td>";
	echo "<td>Донецк</td>";
	echo "<td>Житомир</td>";
	echo "<td>Закарпатье</td>";
	echo "<td>Запорожье</td>";
	echo "<td>Ив-Фр</td>";
	echo "<td>Киев</td>";
	echo "<td>Кировоград</td>";
	echo "<td>Луганск</td>";
	echo "<td>Львов</td>";
	echo "<td>Николаев</td>";
	echo "<td>Одесса</td>";
	echo "<td>Полтава</td>";
	echo "<td>Ровно</td>";
	echo "<td>Суммы</td>";
	echo "<td>Тернополь</td>";
	echo "<td>Харьков</td>";
	echo "<td>Херсон</td>";
	echo "<td>Хмельницк</td>";
	echo "<td>Черкассы</td>";
	echo "<td>Чернигов</td>";
	echo "<td>Черновцы</td>";
	echo "</tr><tr class='ludi'>";
	echo "<td>$krim</td>";
	echo "<td>$vin</td>";
	echo "<td>$vol</td>";
	echo "<td>$dne</td>";
	echo "<td>$don</td>";
	echo "<td>$zhi</td>";
	echo "<td>$zak</td>";
	echo "<td>$zap</td>";
	echo "<td>$ivf</td>";
	echo "<td>$kie</td>";
	echo "<td>$kir</td>";
	echo "<td>$lug</td>";
	echo "<td>$lvo</td>";
	echo "<td>$nik</td>";
	echo "<td>$ode</td>";
	echo "<td>$pol</td>";
	echo "<td>$rov</td>";
	echo "<td>$sum</td>";
	echo "<td>$ter</td>";
	echo "<td>$har</td>";
	echo "<td>$her</td>";
	echo "<td>$hme</td>";
	echo "<td>$chk</td>";
	echo "<td>$chr</td>";
	echo "<td>$chrno</td>";
	echo "</tr>";
	echo "<tr class='ludi'>";
	echo "<td>$skrim</td>";
	echo "<td>$svin</td>";
	echo "<td>$svol</td>";
	echo "<td>$sdne</td>";
	echo "<td>$sdon</td>";
	echo "<td>$szhi</td>";
	echo "<td>$szak</td>";
	echo "<td>$szap</td>";
	echo "<td>$sivf</td>";
	echo "<td>$skie</td>";
	echo "<td>$skir</td>";
	echo "<td>$slug</td>";
	echo "<td>$slvo</td>";
	echo "<td>$snik</td>";
	echo "<td>$sode</td>";
	echo "<td>$spol</td>";
	echo "<td>$srov</td>";
	echo "<td>$ssum</td>";
	echo "<td>$ster</td>";
	echo "<td>$shar</td>";
	echo "<td>$sher</td>";
	echo "<td>$shme</td>";
	echo "<td>$schk</td>";
	echo "<td>$schr</td>";
	echo "<td>$schrno</td>";
	echo "</tr></table><br>";
	echo "<b style='display:block; padding:5px; font-size:15px;'>Расчетный заработок: ".$dengi." грн.</b>";
	echo "<b style='display:block; padding:5px; font-size:15px;'>Количество людей: ".$kolchel."</b>";
	}
	//конец для определенного матча
	//вывод на главной функции просмотра
	else 
		{
		$zayavki = $wpdb->get_results("SELECT * FROM zayavka WHERE del='0' ORDER BY id DESC LIMIT 0,10 ");
		echo "<a class='download' href='/wp-admin/admin.php?page=view&otchet=vse'>Скачать отчет</a><br><b>Показаны последние 10 заявок</b>";
		echo "<table style='border-collapse:collapse; width:950px; font-size:13px;' border='1'>";
		echo "<tr style='line-height:25px; text-align:center; background:#bacad0; font-weight:bold;'>";
		echo "<td>Название матча</td>";
		echo "<td>Поддержка команды</td>";
		echo "<td>Количество</td>";
		echo "<td>Область проживания</td>";
		echo "<td>Телефон</td>";
		echo "<td>Почта</td>";
		echo "<td>ФИО</td>";
		echo "</tr>";
		foreach ( $zayavki as $zayavka ) 
			{
			$mathname = $wpdb->get_row("SELECT * FROM math WHERE id='$zayavka->idmath'");	
			echo "<tr style='line-height:25px; text-align:center;'><td>".$mathname->team1." - ".$mathname->team2."</td>";
			$obl2 = $wpdb->get_row("SELECT * FROM oblast WHERE id='$zayavka->obl'");
			echo "<td>".$zayavka->podkom."</td><td>".$zayavka->kol."</td><td>".$obl2->obl."</td><td>0".$zayavka->tel."</td><td>".$zayavka->mail."</td><td>".$zayavka->fio."</td></tr>";
			}
		}	
}
//конец функции просмотра заявок
//начало расчета денег за прошедшие матчи
function poehali_zar_math() {
global $wpdb;
echo "<h4>Выберите посещенный матч</h4>";
echo "<form action=\"\" method=\"GET\">";
echo "<input type=\"hidden\" name=\"page\" value=\"zap\">";
echo "<select name=\"id\">";
$ids = $wpdb->get_results("SELECT * FROM math WHERE archive='1' ORDER BY time");
foreach ( $ids as $id ) 
	{
	echo "<option value=\"$id->id\">".$id->team1." - ".$id->team2." ".$id->chemp."</option>";
	}
echo "</select>";
echo "<input type=\"submit\" value=\"Узнать\">";
echo "</form>";

if ($_GET['id']) 
	{
		$zayavki = $wpdb->get_results("SELECT * FROM zayavka WHERE del='0' AND idmath='$_GET[id]' AND edet='1'");
		foreach ( $zayavki as $zayavka ) 
			{
			$viruchka = $wpdb->get_row("SELECT * FROM ceni WHERE otkuda='$zayavka->obl' AND kuda='$id->city'");
			$km = $viruchka->cena;
			$km = round($km/1000); 
			$km = $km + 150;  
			if ($id->chemp == 'LE') {$stoim = ($km * 2 * 15)/48 + 100;}
			if ($id->chemp == 'LCH') {$stoim = ($km * 2 * 15)/48 + 200;}
			if ($id->chemp == 'UKR') {$stoim = ($km * 2 * 12)/49 + 50;}
			if ($id->chemp == 'ALL') {$stoim = (($km * 2 * 12)/49 + 50)* 1.2;}
			$kolchel += $zayavka->kol;
			$stoim = $stoim * $zayavka->kol;

			$dengi += round($stoim);
			echo "<b style='display:block; padding:5px; font-size:15px;'>Расчетный заработок: ".$dengi." грн.</b>";
			echo "<b style='display:block; padding:5px; font-size:15px;'>Количество людей: ".$kolchel."</b>";
			}
	}
}
//конец функции расчета полученных денег

//начало добавление заявки
function poehali_add_zayavka() {
global $wpdb;
?>
<form class='otpforma' method='POST'>
<p>Название матча</p>
<select name='nmath'>
<?php 
$ids = $wpdb->get_results("SELECT * FROM math WHERE archive='0' ORDER BY time");
foreach ( $ids as $id ) 
	{
	echo "<option value=\"$id->id\">".$id->team1." - ".$id->team2." ".$id->chemp."</option>";
	}
?>
</select>
<p>Поддержать команду</p>
<input type="text" name='podkom' class='avtorizinp2'>
<p>Количество человек для поездки</p>
<input class='avtorizinp2' name='kol' type='text'>
<p>Телефон</p>
<input class='avtorizinp2' id='tel' name='tel' type='text'>
<p>ФИО</p>
<input name='fio' class='avtorizinp2' type='text'>
<p>Область проживания</p>
<input class='avtorizinp2' name='oblast' type='text' value=''><br><br>
<input class='submit2' value='Отправить заявку' align='right' type='submit'>
</form>
<?php

if(isset($_POST['nmath'])) 
	{
	$sql = "INSERT INTO zayavka (idmath, podkom, kol, tel, fio, obl) VALUES ('$_POST[nmath]', '$_POST[podkom]', '$_POST[kol]', '$_POST[tel]', '$_POST[fio]', '$_POST[oblast]')";
	$wpdb->query($sql);
	echo "<script>alert('Заявка добавлена!')</script>";
	}
}
// конец добавления заявки

//функция выбора картинок
function ajax_test2 () {
$id = $_GET['id'];
global $wpdb;
$r = $wpdb->get_row( "SELECT * FROM image WHERE team = '$id'" );
echo $r->url;
die();
}
//конец картинок

//функция обновления сосотояние заявки поездки\непоездка
function ajax_test3 () {
$id = $_GET['id'];
$checked = $_GET['edet'];
global $wpdb;
if ($checked == 1) {$sql = "UPDATE zayavka SET edet='1' WHERE id='$id'"; $wpdb->query($sql);}
else {$sql = "UPDATE zayavka SET edet='0' WHERE id='$id'"; $wpdb->query($sql);}

die();
}
//конец обновления

//начало функция рассылки информации
function poehali_viezd_math() {
global $wpdb;
//начало вывода формы
echo "<h4>Выберите матч и введите информацию о выезде</h4>";
echo "<form action=\"\" method=\"POST\">";
echo "<select name=\"id\">";
$ids = $wpdb->get_results("SELECT * FROM math WHERE archive='0' ORDER BY time");
foreach ( $ids as $id ) 
	{
	echo "<option value=\"$id->id\">".$id->team1." - ".$id->team2." ".$id->chemp."</option>";
	}
echo "</select>";
echo "<select name='oblzayavka'>";
$obls = $wpdb->get_results("SELECT * FROM oblast");
foreach ( $obls as $obl ) 
	{
	echo "<option value='$obl->id'>$obl->obl</option>";
	}
echo "</select>";
echo "<p>Введите текст для раздела заявок и рассылки по почте</p><textarea name='mailtxt' style='width:350px; height:120px;'></textarea><br>";
echo "<p>Введите текст для рассылки на телефон</p><textarea name='teltxt' style='width:350px; height:120px;'></textarea><br>";
echo "<input type=\"submit\" value=\"Отправить\">";
echo "</form>";
//конец выводов селектов матча и области

//проверка отправки данных
if (isset($_POST['id'])) 
	{
	$iset = $wpdb->get_row("SELECT * FROM text WHERE idmath='$_POST[id]'");
	//поверка на наличие в базе уже текста
	if (!$iset) 
		{
		$idobl = $wpdb->get_row("SELECT * FROM oblast WHERE id='$_POST[oblzayavka]'");
		$sql = "INSERT INTO text (idmath,obl,mailtxt,teltxt) VALUES ('$_POST[id]','$idobl->obl','$_POST[mailtxt]','$_POST[teltxt]')";
		$wpdb->query($sql);
		//подключение к шлюзу
		$con = mysql_connect("77.120.116.10","vlad00777","vlad31415926535") or die (mysql_error());
		mysql_select_db('users',$con) or die (mysql_error()); 
		echo "<p>E-mail рассылка произведена. SMS рассылка успешно произведена по номерам: </p>";
		$smsi = $wpdb->get_results("SELECT * FROM zayavka WHERE del='0' and idmath='$_POST[id]' and edet='1' and obl='$idobl->obl'");
		//вставка данных в базу шлюза 
		foreach ( $smsi as $sms ) 
			{
			$number = "380".$sms->tel;
			$otvet = mysql_query ("INSERT INTO vlad00777 (number,sign,message) VALUES ('$number','Na-football','$_POST[teltxt]')",$con); 
			if (!$otvet) { die('Неверный запрос: ' . mysql_error());}
			echo "+380".$sms->tel."<br>";
			$to[] = $sms->mail;
			}
			
			/*$all = mysql_query ("SELECT * FROM vlad00777",$con);
			while($all2 = mysql_fetch_array($all)) {
			print_r($all2);
			}*/
		
		mysql_close($con);
		//конец рассылки смс
		//рассылка почты
		$headers[] = 'From: Поехали на футбол <robot@poehalinafootball.com.ua>';
		$subject = 'Информация о выезде!';
		$sendmail = wp_mail($to, $subject, $_POST['mailtxt'], $headers);
		}
		else 
			{
			echo "<script>alert('Вы уже отправляли информацию об этом матче.');</script>";
			}
	}
}
//конец функции рассылки инфо


//начало формы ввода цены матча
function poehali_cena() {
global $wpdb;
	//начало вывода селекта с матчами
$ids = $wpdb->get_results("SELECT * FROM math WHERE archive='0'");
echo "<p><b>Форма добавления точной стоимости поездки.</b></p><form action='' method='GET'><p style='margin:0; padding:5px 0px;'>Название матча</p><select name='math'>";
foreach ( $ids as $id ) 
	{
	echo "<option value=\"$id->id\">".$id->team1." - ".$id->team2." ".$id->chemp."</option>";
	}
echo "</select><br><br>Выберите область<br><select name='oblzayavka'>";
//Конец вывода селекта с матчами

//начало вывода селекта с областями
$obls = $wpdb->get_results("SELECT * FROM oblast");
foreach ( $obls as $obl ) 
	{
	echo "<option value='$obl->id'>$obl->obl</option>";
	}
echo "</select><br><br>Введите цену для оплаты пользователями!!!<br><input type='text' name='price'><br><br><input type='hidden' name='page' value='cena'><input value='Изменить цену' type='submit'></form>";
//конец вывода селекта с областями

if (isset($_GET['math']) and isset($_GET['oblzayavka']) and isset($_GET['price'])) 
	{
	// находим объект
	$cena = $wpdb->get_row("SELECT * FROM price WHERE idmath='$_GET[math]' AND obl='$_GET[oblzayavka]'");
	// проверяем есть ли такой объект
	if ($cena) {
		$result = $wpdb->query("UPDATE price SET cena='$_GET[price]' WHERE idmath='$_GET[math]' AND obl='$_GET[oblzayavka]'");
	}
	else {
		$result = $wpdb->query("INSERT INTO price (idmath, obl,cena) VALUES ('$_GET[math]', '$_GET[oblzayavka]', '$_GET[price]')");
	}
	if ($result) echo "<b>Цена добавлена или изменена.</b><br>"; 
	}
echo "<br><b>Установленные цены:</b><br>";
$prices = $wpdb->get_results("SELECT * FROM price");
echo "<table border='1' style='border-collapse: collapse; width:600px;'><tr style='line-height: 25px;text-align: center;background: #bacad0;font-weight: bold;'><td>Матч</td><td>Область</td><td>Цена</td></tr>";
foreach ($prices as $price) 
	{
	$idmath = $wpdb->get_row("SELECT * FROM math WHERE id='$price->idmath'");
	$obl = $wpdb->get_row("SELECT * FROM oblast WHERE id='$price->obl'");
	echo "<tr><td>".$idmath->team1." - ".$idmath->team2."</td><td>".$obl->obl."</td><td>".$price->cena."</td></tr>";
	} 
}
//конец формы ввода цены


//проверка получения айди матча для отчета
if ($_GET['otchet']) {
require_once 'Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$active_sheet = $objPHPExcel->getActiveSheet();
$objPHPExcel->createSheet();
//Ориентация страницы и  размер листа
$active_sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$active_sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
//Поля документа
$active_sheet->getPageMargins()->setTop(1);
$active_sheet->getPageMargins()->setRight(0.5);
$active_sheet->getPageMargins()->setLeft(0.5);
$active_sheet->getPageMargins()->setBottom(1);
//Название листа
$active_sheet->setTitle("Поехали на футбол");
//Настройки шрифта
$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
//Создаем шапку таблички данных
$active_sheet->setCellValue('A2','Название матча');
$active_sheet->setCellValue('B2','Поддержка команды');
$active_sheet->setCellValue('C2','Количество');
$active_sheet->setCellValue('D2','Область проживания');
$active_sheet->setCellValue('E2','Телефон');
$active_sheet->setCellValue('F2','ФИО');
$active_sheet->getColumnDimension('A')->setWidth(40);
$active_sheet->getColumnDimension('B')->setWidth(20);
$active_sheet->getColumnDimension('C')->setWidth(10);
$active_sheet->getColumnDimension('D')->setWidth(30);
$active_sheet->getColumnDimension('E')->setWidth(15);
$active_sheet->getColumnDimension('F')->setWidth(70);
//объединение колонок
$active_sheet->mergeCells('A1:F1');
$active_sheet->getRowDimension('1')->setRowHeight(20);
$active_sheet->setCellValue('A1','Поехали на футбол!');
//Стили для верхней надписи строка 1
$style_header = array(
	//Шрифт
	'font'=>array(
		'bold' => true,
		'name' => 'Times New Roman',
		'size' => 16
	),
//Выравнивание
	'alignment' => array(
		'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
	)

);
$active_sheet->getStyle('A1:D1')->applyFromArray($style_header);
//отчет всех областей и всех людей и всех матчей
if ($_GET['otchet'] == 'vse') 
	{
	$math = $wpdb->get_row("SELECT * FROM math");
	$zayavki = $wpdb->get_results("SELECT * FROM zayavka");
	}
//отчет на матч всех людей
if ($_GET['otchet'] == 1) 
	{
	$math = $wpdb->get_row("SELECT * FROM math WHERE id='$_GET[create]'");
	$zayavki = $wpdb->get_results("SELECT * FROM zayavka WHERE idmath='$_GET[create]' AND del='0'");
	}
//отчет на матч тех кто едет
if ($_GET['otchet'] == 'edet') 
	{
	$math = $wpdb->get_row("SELECT * FROM math WHERE id='$_GET[create]'");
	$zayavki = $wpdb->get_results("SELECT * FROM zayavka WHERE idmath='$_GET[create]' AND del='0' AND edet='1'");
	}

$row_start = 3;
$i = 0;
foreach($zayavki as $item) 
	{
	$row_next = $row_start + $i;
	$active_sheet->setCellValue('A'.$row_next,$math->team1." - ".$math->team2);
	$active_sheet->setCellValue('B'.$row_next,$item->podkom);
	$active_sheet->setCellValue('C'.$row_next,$item->kol);
	$active_sheet->setCellValue('D'.$row_next,$item->obl);
	$active_sheet->setCellValue('E'.$row_next,"0".$item->tel);
	$active_sheet->setCellValue('F'.$row_next,$item->fio);
	$i++;
	}
header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename='poehali.xls'");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit();
}
//конец функции формирования отчета

//хуки и меню
register_activation_hook(__FILE__, 'poehali_activation');
register_deactivation_hook(__FILE__, 'poehali_deactivation');
add_action('admin_menu', 'mt_add_pages');
add_action('wp_ajax_vote2', 'ajax_test2');
add_action( 'wp_ajax_nopriv_vote2', 'ajax_test2' );
add_action('wp_ajax_vote3', 'ajax_test3');
add_action( 'wp_ajax_nopriv_vote3', 'ajax_test3' );
add_action('admin_head', 'custom_admin_css');
add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));

function mt_add_pages() {
add_menu_page('Поехали на футбол', 'Поехали на футбол', 8, __FILE__, 'poehali_form');
add_submenu_page(__FILE__, 'Добавить матч', 'Добавить матч', 8, 'addmath', 'poehali_add_math');
add_submenu_page(__FILE__, 'Просмотреть матчи', 'Просмотреть матчи', 8, 'prosmotr', 'poehali_view_math');
add_submenu_page(__FILE__, 'Редактировать матч', 'Редактировать матч', 8, 'edit', 'poehali_edit_math');
add_submenu_page(__FILE__, 'Удалить матч', 'Удалить матч', 8, 'delete', 'poehali_delete_math');
add_submenu_page(__FILE__, 'Добавить заявку', 'Добавить заявку', 8, 'addzayavka', 'poehali_add_zayavka');
add_submenu_page(__FILE__, 'Просмотр заявок', 'Просмотр заявок', 8, 'view', 'poehali_view_zayavka');
add_submenu_page(__FILE__, 'Добавить цену выезда', 'Добавить цену выезда', 8, 'cena', 'poehali_cena');
add_submenu_page(__FILE__, 'Добавить инфо о выезде', 'Добавить инфо о выезде', 8, 'viezd', 'poehali_viezd_math');
add_submenu_page(__FILE__, 'Заработок за матч', 'Заработок за матч', 8, 'zap', 'poehali_zar_math');
}
////конец хуков

//начало шорт кода
require_once ('shortcode.php');
require_once ('zayavki.php');
require_once ('pay.php');
require_once ('rssout.php');
//конец шорт кода

function custom_admin_css() {
print "
<style>
.download{
margin:5px 5px 10px 5px;
display:inline-block;
font-weight:bold;
} 

table{
font-size:13px !important; 
font-weight:normal; 
}

.tittable{
text-align: center;
background: #bacad0;
word-break:break-all;
}

.tittable td{
padding:3px;
font-size:12px;
}

.ludi td{
text-align:center;
}

</style>";
}

?>