<?php
require "includes/bdconnect.php";
require "includes/input_type.php";
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экспертная сессия</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container" style="color: black;">
        <?php
        if (!empty($_GET['link'])) {
            $query ="SELECT * FROM `sessions` WHERE `session_link`= '".$_GET['link']."'";
            $result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link));
            if(mysqli_num_rows($result)!=0){
                $session = mysqli_fetch_row($result);
                echo '<h1>Экспертная сессия: "'.$session[2].'"</h1>
                <form action="/?link='.$_GET['link'].'" method="post">';
                if($session[1]=='active'){
                    $questions= utf8_encode($session[3]);
                    $questions = json_decode($session[3], true);
                    foreach ($questions as $key =>$question){
                        if($question['type']!='checkbox'&&$question['type']!='radio'){
                            echo '<label for="question'.$key.'">'.$question['question'].'</label><br>';
                            echo '<input id="question'.$key.'" name="question'.$key.'" '. get_type($question['type']) .'>'.$question[$question].'</input><br><br>';
                        }else{
                            echo '<p>'.$question['question'].'</p>';
                            $radio=explode(',',$question['options']);
                            if($question['type']=='radio') {
                                foreach ($radio as $num => $value) {
                                    echo '<input ' . get_type($question['type']) . ' id="question' . $key . $num . '" name="question' . $key . '" value="' . $value . '">';
                                    echo '<label for="question' . $key . $num . '">' . $value . '</label><br><br>';
                                }
                            }else{
                                foreach ($radio as $num => $value) {
                                    echo '<input ' . get_type($question['type']) . ' id="question' . $key . $num . '" name="question' . $key . '[]" value="' . $value . '">';
                                    echo '<label for="question' . $key . $num . '">' . $value . '</label><br><br>';
                                }
                            }
                        }
                    }
                    echo '<input type="submit" value="Отправить">';
                }else{
                    echo '<h2>Сессия закрыта</h2>';
                }
                
            }else{
                echo '<h1>Что-то пошло не так</h1> <h2> Попробуйте спросить ссылку еще раз</h2>';
            }
        }
        else {
            echo '<a href="admin.php" style="color: red;position: fixed;top: 200px; left: 200px;">Зайти от имени администратора</a>';
        }
        ?>
    <?php
    if (!empty($_POST)){
        $true_answers = 0;
        $answers_count=count($questions);
        $answers=Array();
        for ($i = 0; $i < $answers_count; $i++){
            $answers[$i]['type']=$questions[$i]['type'];
            is_array($_POST['question'.$i])?
            $answers[$i]['answer']=implode(",", $_POST['question'.$i]):
            $answers[$i]['answer']=$_POST['question'.$i];
            if ($answers[$i]['answer']==$questions[$i]['answer']) $true_answers++;
        }
        $answers = json_encode($answers, JSON_UNESCAPED_UNICODE);
        echo "Ответы учтены. Правильных ответов: ".$true_answers;
        $client_ip=get_ip();
        date_default_timezone_set('Europe/Moscow');
        $d = date("d.m.Y");
        $t = date("H-i:s");
        $client_date = $d.' '.$t;
        $client_id = bin2hex(random_bytes(5));
        $answers_query="INSERT INTO `answers` (client_id, session_link, answers, client_ip, client_date)
                        VALUES ('$client_id', '$session[0]', '$answers', '$client_ip', '$client_date')";
        $result = mysqli_query($link, $answers_query) or die("Ошибка " . mysqli_error($link));
    }
    function get_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    mysqli_close($link);
    ?>
  </div>
</body>
</html>
