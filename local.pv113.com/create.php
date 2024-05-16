<?php global $dbh; ?>
<?php include_once $_SERVER["DOCUMENT_ROOT"] . "/connection_database.php"; ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $datepublish = $_POST['datepublish'];
    $description = $_POST['description'];
    $stmt = $dbh->prepare("INSERT INTO news (name, datepublish, description) VALUES (?, ?, ?)");
    $stmt->execute([$name, $datepublish, $description]);
    $lastInsertedId = $dbh->lastInsertId();
    header("Location: /?id=".$lastInsertedId);
    exit();
}?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Сенонд хенд</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
<div class="container py-3">
    <?php include_once $_SERVER["DOCUMENT_ROOT"] . "/_header.php"; ?>

    <h1 class="text-center">Додати новину</h1>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Назва</label>
            <input type="text" class="form-control" id="name" name="name">
        </div>

        <div class="mb-3">
            <label for="datepublish" class="form-label">Дата публікації</label>
            <input type="datetime-local" class="form-control" id="datepublish" name="datepublish">
        </div>

        <div class="mb-3">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Вкажіть опис тут" name="description" id="description"
                          style="height: 100px"></textarea>
                <label for="description">Опис</label>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Додати</button>
        </div>

    </form>


</div>

<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>