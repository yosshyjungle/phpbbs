<?php

date_default_timezone_set("Asia/Tokyo");

// 配列を用意
$comment_array = array();

$pdo = null;
$stmt = null;
$error_messages = array();


// DB接続
try {
    $pdo = new PDO('mysql:host=localhost;charset=UTF8;dbname=phpbbs', 'root', '');
} catch(PDOException $e) {
    echo $e->getMessage();
}

// フォームを打ち込んだ時
if(!empty($_POST["submitButton"])){

    $username = $_POST["username"];
    $comment = $_POST["comment"];

    $username = htmlspecialchars($username);
    $comment = htmlspecialchars($comment);

 
    // 名前のチェック
    if(empty($username)){
        echo "名前を入力してください";
        // error_messagesの連想配列に値を格納していきます。
        $error_messages["username"] = "名前を入力してください";
    }
    // コメントのチェック
    if(empty($comment)){
        echo "コメントを入力してください";
        // error_messagesの連想配列に値を格納していきます。
        $error_messages["comment"] = "コメントを入力してください";
    }
    
    if(empty($error_messages)){
        // error_messagesが空の場合、データを投稿する
        //　時間の設定
        $postDate = date("Y-m-d H:i:s");

        try{
            // 値のセット
            $stmt = $pdo->prepare("INSERT INTO `bbs-table` (`username`, `comment`, `postDate`) VALUES (:username, :comment, :postDate);");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':postDate', $postDate, PDO::PARAM_STR);

            // 実行
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

}

// DBからコメントデータを取得する
$sql = "SELECT * FROM `bbs-table`";
$comment_array = $pdo->query($sql);

// DBの接続を閉じる
$pdo = null;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2ch風掲示板</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1 class="title">2ch風掲示板</h1>
    <hr>
    <div class="boardWrapper">
        <section>
            <?php foreach($comment_array as $comment): ?>
            <article>
                <div class="wrapper">
                    <div class="nameArea">
                        <span>名前：</span>
                        <p class="username"><?php echo $comment["username"]; ?></p>
                        <time> : <?php echo $comment["postDate"]; ?></time>
                    </div>
                    <p class="comment"><?php echo $comment["comment"]; ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </section>
        <form method="POST" action="" class="formWrapper">
            <div>
                <input type="submit" value="書き込む" name="submitButton">
                <label for="usernameLabel">名前：</label>
                <input type="text" name="username">
            </div>
            <div>
                <textarea name="comment" class="commentTextArea"></textarea>
            </div>
        </form>
    </div>

</body>

</html>