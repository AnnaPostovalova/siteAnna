<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Добавление информации о городах и областях</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="style.css">
	<meta name="keywords" content="город, область, Россия, регион">
	<meta name="description" content="Города России">
</head>
<body>
<div id="front">
    <div class="site-header"> 
                    <div class="main-menu"> <!-- Главное меню -->
                        <ul>
                            <li><a href="anna1.php">ГЛАВНАЯ</a></li>
                            <li><a href="anna2.php">Добавление</a></li>
                            <li><a href="anna3.php">Просмотр</a></li>
                        </ul>
                    </div> <!--  Главное меню -->
    </div> 
</div> <!-- front -->
<section class="contacts" id="call"> <!-- ДОБАВЛЕНИЕ ИНФ.-->
      <div class="container">
        <h2 class="contacts-title section-title" align="center">Добавьте информацию</h2>
        <form class="contacts-form" action="anna2.php" method="POST">
          <table>
            <tr>
              <td class="title">
                <label for="name">Название области:</label>
              </td>
              <td class="field">
                <input type="text" id="name" name="name" placeholder="Область">
              </td>
				
            </tr>
            <tr>
              <td class="title">
			    <label for="president">ФИО губернатора:</label>
			  </td>
              <td class="field">
                <input type="text" id="president" name="president" placeholder="Губернатор">
              </td>
			  
            </tr>
            <tr>
              <td class="title">
                <label for="name_city">Название города:</label>
              </td>
              <td class="field">
                <input type="text" id="name_city" name="name_city" placeholder="Город">
              </td>
			  
            </tr>	
            <tr>
              <td class="title">
                <label for="opisanie">Краткое описание города:</label>
              </td>
              <td class="field">
                <input type="text" id="opisanie" name="opisanie" placeholder="Основная информация">
              </td>
			  
            </tr>
			<tr>
			    <td class="title">
				  <label for="image">Изображение города:</label>
				</td>
				<td class="filed">
				<input type="file" name="image" accept="image/*">
				</td>
			</tr>
            <tr>
              <td class="title"></td>
              <td class="field">
                <input type="submit" name ="submit" class="btn btn-green" value="Отправить">
              </td>
            </tr>
          </table>
        </form>
	  </div>
</section> <!-- ДОБАВЛЕНИЕ ИНФ.-->
<?php
//===============================================================================================================================================
/*INSERT INTO `city` (`name_city`, `opisanie`, `image`) VALUES ('JOKF', 'LALALA', 'afopqjgp');
INSERT INTO `regions` (`name`, `president`) VALUES ('Obama', 'nigger');*/
/*
INSERT INTO `regions` (`name`, `president`) VALUES ('Obamaaaa', 'nigger');
INSERT INTO `city` (id_region, `name_city`, `opisanie`, `image`) 
SELECT id, 'JOKF', 'LALALA', 'afopqjgp' FROM regions WHERE name = 'Obamaaaa' AND president = 'nigger';
*/


print_r($_FILES);
print_r($_FILES['image']);
print_r($_FILES['userfile']['error']);

$uploaddir = '/images/city/';
$uploadfile = $uploaddir . basename($_FILES['image']['name']);//вырезает имя файла из пути на компьютере клиента и добавляет к пути, который будет на сервере

echo "<pre>";
if (move_uploaded_file($_FILES['image']['name'], $uploadfile)) {
    echo "Файл корректен и был успешно загружен.\n";
} else {
    echo "Возможная атака с помощью файловой загрузки!\n";
}
echo "</pre>";
//сохранение картинки на сервере

include("conn.php");//подключаемся к БД (подключаем файл conn.php, в котором прописан код для подключения к БД)

if (isset($_POST['submit']))//проверяем, нажата ли кнопка отправки формы для добавления данных о городе
{
//-----------------------------------------------------------------------------------------------------------------------------------------------
	if (!empty($_POST['name_city']) && !empty($_POST['opisanie']) && !empty($_POST['image']) && !empty($_POST['name']) && !empty($_POST['president']) && !empty($_POST['image']))
	//если данные по области, названию города и описанию не пустые, то	
	{
		//ищем, существует ли такая область в БД
		$data = $conn->prepare('SELECT * FROM `regions` WHERE `name` LIKE :name');
		$data->execute(["name" => $_POST['name']]);
		$res = $data->fetchAll();
		if (count($res) > 0)//если найдена запись в БД, то
		{
			$data = $conn->prepare('SELECT * FROM `city` WHERE `name_city` LIKE :name_city');// вкл запрос на поиск введённого города в введённой области
			$data->execute(["name_city" => $_POST['name_city']]);//исполняем подготовленные данные
			$res = $data->fetchAll();//результат запроса записываем в переменную
			if (count($res) > 0)//если совпадения найдены, то
			{
				$err = '<p>ОШИБКА: уже есть такой город и область!</p>';//записываем в переменную ошибку для последующего вывода
			}
			else {			
			$data = $conn->prepare('INSERT INTO `city` (`id_region`, `name_city`, `opisanie`, `image`) 
			SELECT id, :name_city, :opisanie, :image FROM `regions` WHERE `name` LIKE :name AND `president` LIKE :president;');
			//подготовка
			$data->execute(array(
				"name" => $_POST['name'], 
				"president" => $_POST['president'],
				"name_city" => $_POST['name_city'], 
				"opisanie" => $_POST['opisanie'], 
				"image" => 'images/city/'.$_FILES['image']['name']
			));//исполнение
			$count = $data->rowCount();//записываем в переменную кол-во изменённых строк
			if ($count == 1)//если кол-во этих строк равно 1, то
			{
				$err = '<p>Успешно добавлен новый город!</p>';//записываем в переменную положительный результат для последующего вывода
			}
			else//иначе
			{
				$err = '<p>ОШИБКА: неудачная попытка добавления новой записи (города) в БД!</p>';//записываем в переменную ошибку для последующего вывода
			}
			
			//$err = '<p>ОШИБКА: уже есть такая область в БД!</p>';//записываем в переменную ошибку для последующего вывода
			}
		}
		else//иначе
		{
			//добавляем запись об области в БД
			$sql = 'INSERT INTO `regions` (`name`, `president`)
			VALUES (:name, :president)';
			$data = $conn->prepare($sql);
			$data->execute(array(
				"name" => $_POST['name'], 
				"president" => $_POST['president']
			));
			$count = $data->rowCount();//записываем в переменную кол-во изменённых строк
			if ($count == 1)//если кол-во этих строк равно 1, то
			{
				$err = '<p>Успешно добавлена новая область!</p>';//записываем в переменную положительный результат для последующего вывода
			}
			else//иначе
			{
				$err = '<p>ОШИБКА: неудачная попытка добавления новой записи (области) в БД!</p>';//записываем в переменную ошибку для последующего вывода
			}
			//добавляем город в БД
			$data = $conn->prepare('INSERT INTO `city` (`id_region`, `name_city`, `opisanie`, `image`) 
			SELECT id, :name_city, :opisanie, :image FROM `regions` WHERE `name` LIKE :name AND `president` LIKE :president;');
			//подготовка
			$data->execute(array(
				"name" => $_POST['name'], 
				"president" => $_POST['president'],
				"name_city" => $_POST['name_city'], 
				"opisanie" => $_POST['opisanie'], 
				"image" => 'images/city/'.$_FILES['image']['name']
			));//исполнение
			$count = $data->rowCount();//записываем в переменную кол-во изменённых строк
			if ($count == 1)//если кол-во этих строк равно 1, то
			{
				$err = '<p>Успешно добавлен новый город!</p>';//записываем в переменную положительный результат для последующего вывода
			}
			else//иначе
			{
				$err = '<p>ОШИБКА: неудачная попытка добавления новой записи (города) в БД!</p>';//записываем в переменную ошибку для последующего вывода
			}
		}		
	}	
	else//если поля пустые, то
	{
		$err = '<p>ОШИБКА: пустые поля при добавлении области!</p>';//записываем в переменную ошибку для последующего вывода
	}
}
/*else//иначе
		{
			//вставляем данные о новом городе в БД
			$sql = 'INSERT INTO `city` (`name_city`, `opisanie`, `image`)
			VALUES (:name_city, :opisanie, :image)';//запрос на вставку данных
			$data = $conn->prepare($sql);
			$data->execute(array(
			
				"name_city" => $_POST['name_city'], 
				"opisanie" => $_POST['opisanie'], 
				"image" => 'images/city/'.$_FILES['image']['name']
			));//исполнение
			$count = $data->rowCount();//записываем в переменную кол-во изменённых строк
			if ($count == 1)//если кол-во этих строк равно 1, то
			{
				$err = '<p>Успешно добавлен новый город!</p>';//записываем в переменную положительный результат для последующего вывода
			}
			else//иначе
			{
				$err = '<p>ОШИБКА: неудачная попытка добавления новой записи (города) в БД!</p>';//записываем в переменную ошибку для последующего вывода
			}
		}*/
//===============================================================================================================================================
?>
		<div>
			<?php 
				echo $content;//вывод 
				echo '<div id="errors">'.$err.'</div>';//вывод полученного результата (ошибки или подтверждение добавления данных в БД)
			?>
		</div>
					



<div class="site-footer"> <!-- Подвал  -->
    <div class="container">
                <span id="copyright">
                   Постовалова Анна Сергеевна &copy; 2019 <a href="#">Company Name</a>
               </span>
            <ul class="social">
                <li><a href="https://vk.com/id134455699" class="vk"><img src="images/vk.png"></a></li>
                <li><a href="https://instagram.com/p_a_sv" class="instagram"><img src="images/inst.png"></a></li>
                <li><a href="https://twitter.com" class="twit"><img src="images/twit.png"></a></li>
            </ul>
    </div> <!-- /.container -->
</div> <!-- /.site-footer --> <!--  Подвал -->
<script src="new 1.js"></script>
</body>

</html>