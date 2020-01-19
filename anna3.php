<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Демонстрация информации о городах и областях</title>
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
        <h2 class="contacts-title section-title" align="center">Поиск</h2>
<?php
			include("conn.php");//подключаемся к БД
			if (isset($_GET['query']) && !empty(trim($_GET['query'])))//если нажата кнопка поиска и запрос не является пустым, то
			{
				$query = trim($_GET['query']);//убираем из запроса лишние пробелы
				//проверка длины запроса
				if (strlen($query) < 3)
					$result = '<p>Слишком короткий поисковой запрос</p>';
				else if (strlen($query) > 128)
					$result = '<p>Слишком длинный поисковой запрос</p>';
				else
				{
					//получаем список городов и областей из БД, где есть совпадения по запросу
					$data = $conn->prepare('SELECT * FROM `regions` WHERE `name` LIKE :i OR `president` LIKE :i');
					$data->execute(array("i" => '%'.$query.'%'));
					$res = $data->fetchAll();//записываем в переменную список совпадений по областям
					$data = $conn->prepare('SELECT * FROM `city` WHERE `name_city` LIKE :i OR `opisanie` LIKE :i');
					$data->execute(array("i" => '%'.$query.'%'));
					$res2 = $data->fetchAll();//записываем в переменную список совпадений по городам
					$result = '<div>';
					if (count($res) > 0 || count($res2) > 0)//если есть совпадения хотя бы в одной таблице, то
					{
						$count = count($res) + count($res2);//общее число совпадений
						$result .= '<h3>По запросу "'.$query.'" найдено совпадений ('.$count.'):</h3>';
						foreach ($res as $i)//вывод результатов поиска по областям
						{
							$result .= '<div><p>'.$i['name'].' область</p><p>Губернатор - '.$i['president'].'</p></div>';
						}
						foreach ($res2 as $i)//вывод результата поиска по городам
						{
							$result .= '<div><h3>'.$i['name_city'].'</h3><p>'.$i['opisanie'].'</p><img width="40%" height="auto" src='.$i['image'].'></div>';
						}
					}
					else//иначе
						$result .= '<p>По запросу "'.$query.'" ничего не найдено</p>';//сообщение, что ничего не найдено
					$result .= '</div>';
				}
			}
			else//иначе
			{
				//вывод всех областей
				$sql = 'SELECT * FROM `regions`';
				$data = $conn->prepare($sql);
				$data->execute();
				$res = $data->fetchAll();//записываем в переменную список всех областей
				if (count($res) > 0)//если есть записи в таблице, то
				{
					foreach ($res as $i)//в цикле добавляем в переменную все области
					{
						$result .= '<div><h2>'.$i['name'].' область. Губернатор - '.$i['president'].'</h2>';//выводим название области и губернатора
						//проверяем наличие городов в БД, которые относятся к области i
						$data = $conn->prepare('SELECT * FROM `city` WHERE `id_region`=:id');
						$data->execute(array('id' => $i['id']));
						$res2 = $data->fetchAll();//заносим в переменную города данной области i
						if (count($res2) > 0)
						{
							foreach ($res2 as $j)//и для каждой области данные о городах этой области
							{
								$result .= '<div><h3>'.$j['name_city'].'</h3><p>'.$j['opisanie'].'</p><img width="50%" height="auto" src='.$j['image'].'></div>';
							}
						}
						$result .= '</div>';
					}
				}
				else//иначе
					$result = '<p>Нет данных по областям</p>';//выводим отсутствие данных в БД
			}
		?>
		<form id="search" name="search" method="get">
			<input type="search" name="query" placeholder="Поиск.." />
			<input type="submit" value="Найти" />
		</form>
		<div>
			<?=$result/*вывод результата поиска или списка всех областей и городов*/?>
		</div>
		 </div>
</section>
		


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