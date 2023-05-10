<?php

//подключение к базе данных
    $host = 'localhost';
    $dbname = 'score';
    $user = 'postgres';
    $password = '123';

    $dbconn = pg_connect("host=$host dbname=$dbname user=$user password=$password")
    or die('Could not connect: ' . pg_last_error());

    //сохраняю максимальный user_id для цикла, id обычно начинается с 0, поэтому добавляю единицу
    $query = "SELECT max(user_id) FROM users";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $max_id = pg_fetch_result($result, 0, 0) + 1;

    //у нас 3 задания, пусть id заданий начинается с 0, поэтому положим task_max_id == 3
    $task_max_id = 3;

    for ($i = 0; $i < $task_max_id; $i++) {
      $query = "SELECT avgscore FROM tasks WHERE task_id = '$i'";
          $result = pg_query($query) or die('Query failed: ' . pg_last_error());
          if ($i == 0) {
            $avgscore1 = $result;
          }
          if ($i == 1) {
            $avgscore2 = $result;
          }
          if ($i == 2) {
            $avgscore3 = $result;
          }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Скорборд пользователей</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }

    th, td {
      text-align: left;
      padding: 8px;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>
  <h1>Скорборд пользователей</h1>
  <table>
    <thead>
      <tr>
        <th>Пользователь</th>
        <th>Категория 1</th>
        <th>Категория 2</th>
        <th>Категория 3</th>
      </tr>
    </thead>
    <tbody>
      <?php
        for ($i = 0; $i < $max_id; $i++) {
          $query = "SELECT login FROM users WHERE user_id = '$i'";
          $result = pg_query($query) or die('Query failed: ' . pg_last_error());
          $scoreLogin = $result;
        
          if (pg_num_rows($result) < 1) {
              echo "Ни один из пользователей ещё не прошёл тестирование";
              exit();
          }

          $query = "SELECT score1 FROM users WHERE user_id = '$i'";
          $result = pg_query($query) or die('Query failed: ' . pg_last_error());
          $score1 = $result;

          $query = "SELECT score2 FROM users WHERE user_id = '$i'";
          $result = pg_query($query) or die('Query failed: ' . pg_last_error());
          $score2 = $result;

          $query = "SELECT score3 FROM users WHERE user_id = '$i'";
          $result = pg_query($query) or die('Query failed: ' . pg_last_error());
          $score3 = $result;

          $personalPercent1 = $score1 * ($avgscore1 / 100);
          $personalPercent2 = $score2 * ($avgscore2 / 100);
          $personalPercent3 = $score3 * ($avgscore3 / 100);

          print 
          "<tr>
            <td>$scoreLogin</td>
            <td>Счёт: $score1 Рейтинг: $personalPercent1</td>
            <td>Счёт: $score2 Рейтинг: $personalPercent2</td>
            <td>Счёт: $score3 Рейтинг: $personalPercent3</td>
          </tr>"
        }
      ?>
    </tbody>
  </table>
</body>
</html>
