<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta charset="UTF-8">
<title>Установка "Учёт материальных средств"</title>
<link href="resources/css/main.css" rel="stylesheet">
</head>
<body>
<?php
extract($_GET, EXTR_OVERWRITE && EXTR_PREFIX_ALL);
extract($_POST, EXTR_OVERWRITE && EXTR_PREFIX_ALL);
extract($_SERVER);

include "dbkey.inc.php";
// доступность модуля: 0 - недоступен, 1 - доступен
// device_view.php qualifier.php
$mv1 = 0; // Компания поставщик				(device_view.php)
$mv2 = 1; // Местоположение				(device_view.php,qualifier.php)
$mv3 = 1; // Статус					(device_view.php)
$mv4 = 1; // Классификация согласно Паспорта ИКТ	(device_view.php,qualifier.php)
$mv5 = 1; // Установить расходный материал		(device_view.php)
$mv6 = 1; // Сетевое устройство				(device_view.php)
$mv7 = 1; // Ответственное лицо				(device_view.php)
$mv8 = 1; // Количество копий				(device_view.php)
$mv9 = 1;

// Отчёты
$ov1 = 1; // Количество копий
$ov2 = 1; // Расход картриджей
$ov3 = 1; // Технические средства согласно паспорта ИКТ
$ov4 = 1; // Техническая оснащенность сотрудников средствами ИКТ
$ov5 = 1; // Сведения о передвижке техники
$ov6 = 1; // Перечень устройств по подсетям
$ov7 = 1; // Отчёт об соответствии расходных материалов
$ov8 = 1; // Отчёт об оставленных комментариях
$ov9 = 1; // Сведения о передаче SIM-карт
$ov10 = 1; // Сведения о печатающих устройствах 
echo $step.' шаг<br>';

switch ($step) {
default:{
echo 
'<form action="" method="POST" role="form" >
<b>Начало</b>
<br>
Начинаем установку программного комплекса "Учёт материальных средств"
<input name="step" type="text" value="1" hidden>
<br>
<button class="btn btn-success" type="submit">Дальше</button>
</form>';}; break;
case '1':{
echo '<b>Создание баз на основе заданных потребностей</b>';
$sql = "
CREATE TABLE `".$dbpref."Rec` (`ID` int(11) NOT NULL,
`SN` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Серийный номер',
`InvN` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Учётный номер',
`Nakl` set('0','1') CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
`InvBuh` varchar(15) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Инвентарный номер',
`Nakl_Buh` set('0','1') CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0',
`Type` tinyint(4) NOT NULL DEFAULT '34',
`Company` int(11) DEFAULT NULL,
`Location` int(11) NOT NULL DEFAULT '12' COMMENT 'Местоположение',
`Status` tinyint(4) NOT NULL DEFAULT '1',
`Employee` int(11) NOT NULL DEFAULT '1000',
`Description` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Описание',
`Date_Price` date DEFAULT '2021-01-01',
`Price` float(9,2) DEFAULT NULL,
`Lan` tinyint(1) DEFAULT NULL COMMENT 'Сетевое устройство'
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COLLATE=cp1251_bin";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>1. Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>1. Основная база создана';}

$sql = "ALTER TABLE `".$dbpref."Rec` ADD PRIMARY KEY (`ID`), ADD KEY `Status` (`Status`), ADD KEY `Type` (`Type`), ADD KEY `Employee` (`Employee`)";
$stmt = $db->prepare($sql);
$stmt->execute();

$sql = "ALTER TABLE `".$dbpref."Rec` MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `".$dbpref."Rec`
ADD CONSTRAINT `".$dbpref."Rec_ibfk_1` FOREIGN KEY (`Type`) REFERENCES `".$dbpref."Type` (`ID`),
ADD CONSTRAINT `".$dbpref."Rec_ibfk_2` FOREIGN KEY (`Employee`) REFERENCES `people` (`id`),
ADD CONSTRAINT `".$dbpref."Rec_ibfk_3` FOREIGN KEY (`Status`) REFERENCES `".$dbpref."Status` (`ID`)";
$stmt = $db->prepare($sql);
//if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>2. Ошибка запроса: ' . $errorMsg;}
//			   else {echo '<br>2. База '.$dbpref.'Device_buildings создана';}
//echo '<br>1. Основная база создана';
//$sql = "";
//$stmt = $db->prepare($sql);
//$stmt->execute();
$sql = " CREATE TABLE ` ".$dbpref."buildings` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(50) COLLATE utf16_bin NOT NULL,
 `address` text COLLATE utf16_bin NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf16 COLLATE=utf16_bin COMMENT='Здания' ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>2. База Device_buildings создана';}

$sql = " CREATE TABLE ` ".$dbpref."Cartridges` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
 `quantity` int(11) NOT NULL,
 `lifetime` varchar(25) COLLATE utf16_bin DEFAULT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `name` (`name`)
 ) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf16 COLLATE=utf16_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>3. База Device_Cartridges создана';}

$sql = " CREATE TABLE ` ".$dbpref."Cartridge_Dev` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `id_device` int(11) NOT NULL,
 `id_cartridges` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `val1` (`id_device`,`id_cartridges`)
 ) ENGINE=InnoDB AUTO_INCREMENT=5713 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>4. База Device_Cartridge_Dev создана';}

$sql = " CREATE TABLE ` ".$dbpref."Company` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `Name` varchar(20) NOT NULL,
 `Phone` varchar(10) NOT NULL,
 `Address` text NOT NULL,
 `PS` text NOT NULL,
 PRIMARY KEY (`ID`),
 UNIQUE KEY `Name` (`Name`)
 ) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>5. База Device_Company создана';}

$sql = " CREATE TABLE ` ".$dbpref."Copy` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `copy` int(11) NOT NULL,
 `date_moving` date NOT NULL,
 `id_ustr` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `Duble_copy` (`copy`,`id_ustr`) USING BTREE
 ) ENGINE=InnoDB AUTO_INCREMENT=1358 DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>6. База Device_Copy создана';}

$sql = " CREATE TABLE ` ".$dbpref."Country` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `Address` text NOT NULL,
 `Department` text NOT NULL,
 `N` varchar(4) NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=MyISAM DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>7. База Device_Country создана';}

$sql = " CREATE TABLE ` ".$dbpref."Description` (
 `ID` bigint(11) NOT NULL AUTO_INCREMENT,
 `id_hard` int(11) NOT NULL,
 `Description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
 `Data_rec` date NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1020 DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>8. База Device_Description создана';}

$sql = " CREATE TABLE ` ".$dbpref."IKT` (
 `id` tinyint(4) NOT NULL AUTO_INCREMENT,
 `Name` varchar(50) NOT NULL,
 `IKT` char(10) NOT NULL,
 PRIMARY KEY (`id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>9. База Device_IKT создана';}

$sql = " CREATE TABLE ` ".$dbpref."IKTtoREC` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `Num_IKT` tinyint(4) DEFAULT NULL,
 `Num_Rec` int(11) NOT NULL,
 PRIMARY KEY (`id`,`Num_Rec`),
 UNIQUE KEY `Num_Rec` (`Num_Rec`)
 ) ENGINE=InnoDB AUTO_INCREMENT=1063 DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>10. База Device_IKTtoREC создана';}

$sql = " CREATE TABLE ` ".$dbpref."Lan` (
 `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
 `ID_ustr` int(11) NOT NULL,
 `IP` text COLLATE utf8_bin NOT NULL,
 `MAC` text COLLATE utf8_bin NOT NULL,
 `date_rec_lan` date NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>11. База Device_Lan создана';}

$sql = " CREATE TABLE ` ".$dbpref."Lan_Edit` (
 `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
 `ID_rec` mediumint(9) NOT NULL,
 `ID_ustr` int(11) NOT NULL,
 `IP` text COLLATE utf8_bin NOT NULL,
 `MAC` text COLLATE utf8_bin NOT NULL,
 `date_rec_lan` date NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>12. База Device_Lan_Edit создана';}

$sql = " CREATE TABLE ` ".$dbpref."Level` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `FAM_Id` int(11) NOT NULL DEFAULT '0',
 `z1` int(11) NOT NULL DEFAULT '0',
 `z2` tinyint(4) NOT NULL DEFAULT '0',
 `lev` char(2) NOT NULL DEFAULT 'n',
 PRIMARY KEY (`id`),
 KEY `FAM_Id` (`FAM_Id`),
 CONSTRAINT `".$dbpref."Level_ibfk_2` FOREIGN KEY (`FAM_Id`) REFERENCES `PC_FIO` (`Serial`) ON DELETE CASCADE
 ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>13. База Device_Level создана';}

$sql = " CREATE TABLE ` ".$dbpref."Location` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `Location` tinytext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
 `DateRec` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`ID`),
 KEY `ID` (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>14. База Device_Location создана';}

$sql = " CREATE TABLE ` ".$dbpref."Moving` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `from_id` int(11) DEFAULT NULL,
 `where_id` int(11) NOT NULL DEFAULT '0',
 `date_moving` date NOT NULL,
 `id_ustr` int(11) DEFAULT NULL,
 `Naim` tinyint(4) DEFAULT '0',
 PRIMARY KEY (`id`,`where_id`),
 KEY `where_id` (`where_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=2076 DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>15. База Device_Moving создана';}

$sql = "
-- --------------------------------------------------------
-- Структура таблицы `Device_Moving_Cartridges`
 
CREATE TABLE `".$dbpref."Moving_Cartridges` (
  `id` int(11) NOT NULL,
  `id_device` int(11) NOT NULL DEFAULT '0',
  `id_cartridges` int(11) DEFAULT NULL,
  `date_moving` date NOT NULL,
  `Naim` tinyint(4) DEFAULT '0'
   ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Индексы таблицы `Device_Moving_Cartridges`
ALTER TABLE `".$dbpref."Moving_Cartridges`
 ADD PRIMARY KEY (`id`,`id_device`),
 ADD KEY `id_device` (`id_device`),
 ADD KEY `id_cartridges` (`id_cartridges`);

-- AUTO_INCREMENT для таблицы `Moving_Cartridges`
ALTER TABLE `".$dbpref."Moving_Cartridges`
 MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- Ограничения внешнего ключа таблицы `Device_Moving_Cartridges`
ALTER TABLE `".$dbpref."Moving_Cartridges`
 ADD CONSTRAINT `".$dbpref."Moving_Cartridges_ibfk_1` FOREIGN KEY (`id_device`) REFERENCES `Device_Rec` (`ID`),
 ADD CONSTRAINT `".$dbpref."Moving_Cartridges_ibfk_2` FOREIGN KEY (`id_cartridges`) REFERENCES `".$dbpref."Cartridges` (`id`);
";

//echo $sql;
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>16. База Device_Moving_Cartridges создана';}

$sql = " CREATE TABLE ` ".$dbpref."Moving_Sim` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `from_id` int(11) DEFAULT NULL,
 `where_id` int(11) NOT NULL DEFAULT '0',
 `date_moving` date NOT NULL,
 `id_sim` int(11) DEFAULT NULL,
 `Naim` tinyint(4) DEFAULT '0',
 PRIMARY KEY (`id`,`where_id`),
 KEY `where_id` (`where_id`)
 ) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>17. База Device_Moving_Sim создана';}

$sql = " CREATE TABLE ` ".$dbpref."NameLan` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `ID_ustr` int(11) NOT NULL,
 `Name_Lan` text COLLATE utf8_bin NOT NULL,
 `date_rec_namelan` date NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>18. База Device_NameLan создана';}

$sql = " CREATE TABLE ` ".$dbpref."NameLan_Edit` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `ID_rec` mediumint(9) NOT NULL,
 `ID_ustr` int(11) NOT NULL,
 `Name_Lan` text COLLATE utf8_bin NOT NULL,
 `date_rec_namelan` date NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>19. База Device_NameLan_Edit создана';}

$sql = " CREATE TABLE ` ".$dbpref."Pass` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `ID_ustr` int(11) NOT NULL,
 `Login_Lan` text COLLATE utf8_bin NOT NULL,
 `Pass_Lan` text COLLATE utf8_bin NOT NULL,
 `date_rec_pass` date NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>20. База Device_Pass создана';}

$sql = " CREATE TABLE ` ".$dbpref."Pass_Edit` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `ID_rec` mediumint(9) NOT NULL,
 `ID_ustr` int(11) NOT NULL,
 `Login_Lan` text COLLATE utf8_bin NOT NULL,
 `Pass_Lan` text COLLATE utf8_bin NOT NULL,
 `date_rec_pass` date NOT NULL,
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>21. База Device_Pass_Edit создана';}

$sql = " CREATE TABLE ` ".$dbpref."PRN` (
 `ID` tinyint(4) NOT NULL AUTO_INCREMENT,
 `Type_1` varchar(7) NOT NULL COMMENT 'Тип печати',
 `Type_2` varchar(5) NOT NULL COMMENT 'Цветность',
 `Type_3` varchar(2) NOT NULL COMMENT 'Формат',
 PRIMARY KEY (`ID`),
 KEY `ID` (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>22. База Device_PRN создана';}

$sql = " CREATE TABLE ` ".$dbpref."PRNtoREC` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `Num_PRN` tinyint(4) DEFAULT NULL,
 `Num_Rec` int(11) NOT NULL,
 PRIMARY KEY (`id`,`Num_Rec`),
 UNIQUE KEY `Num_Rec` (`Num_Rec`)
 ) ENGINE=InnoDB AUTO_INCREMENT=294 DEFAULT CHARSET=latin1 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>23. База Device_PRNtoREC создана';}

$sql = " CREATE TABLE ` ".$dbpref."rooms` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `building_id` int(11) NOT NULL,
 `number` int(11) NOT NULL,
 `text_number` varchar(30) COLLATE utf16_bin DEFAULT NULL,
 `description` varchar(100) COLLATE utf16_bin NOT NULL,
 PRIMARY KEY (`id`),
 KEY `".$dbpref."building` (`building_id`) USING BTREE,
 CONSTRAINT ` ".$dbpref."building` FOREIGN KEY (`building_id`) REFERENCES `".$dbpref."buildings` (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf16 COLLATE=utf16_bin COMMENT='Помещения' ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>24. База Device_rooms создана';}

$sql = " CREATE TABLE ` ".$dbpref."Sim` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `SN` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
 `Number` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
 `Employee` int(11) NOT NULL DEFAULT '1000',
 `Description` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
 `Date_Rec` date NOT NULL,
 `sost` tinyint(4) NOT NULL DEFAULT '0',
 PRIMARY KEY (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=cp1251 COLLATE=cp1251_bin ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>25. База Device_Sim создана';}

$sql = " CREATE TABLE ` ".$dbpref."Status` (
 `ID` tinyint(4) NOT NULL AUTO_INCREMENT,
 `Status` tinytext CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
 `DateRec` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `Color` char(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
 `Vid` tinyint(1) DEFAULT NULL,
 PRIMARY KEY (`ID`),
 KEY `ID` (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>26. База Device_Status создана';}

$sql = " CREATE TABLE ` ".$dbpref."Type` (
 `ID` tinyint(4) NOT NULL AUTO_INCREMENT,
 `Type_n` tinyint(4) NOT NULL DEFAULT '1',
 `Type` varchar(30) NOT NULL,
 `Copy` tinyint(1) NOT NULL,
 `IKT` text NOT NULL,
 PRIMARY KEY (`ID`),
 UNIQUE KEY `Type` (`Type`),
 KEY `ID` (`ID`)
 ) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 ";
$stmt = $db->prepare($sql);
if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>27. База Device_Type создана';}

$sql = "
CREATE TABLE `".$dbpref."people` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(70) CHARACTER SET utf8 DEFAULT NULL,
                      `phone` varchar(50) COLLATE utf16_bin NOT NULL,
                      `m_phone` varchar(50) COLLATE utf16_bin DEFAULT NULL,
                      `mail` varchar(40) COLLATE utf16_bin DEFAULT NULL,
                      `birthday` date DEFAULT '1800-01-01',
                      `sost` tinyint(4) NOT NULL DEFAULT '1',
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;";

if ($stmt->execute() === false) {list(, ,$errorMsg) = $stmt->errorInfo(); echo '<br>Ошибка запроса: ' . $errorMsg;}
			   else {echo '<br>28. База people создана';}

}; break;
case '6':{}; break;
}

?>
</body>
</html>
