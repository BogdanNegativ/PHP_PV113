<?php global $dbh; ?>
<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/config/constants.php"); ?>
<?php include_once $_SERVER["DOCUMENT_ROOT"] . "/connection_database.php"; ?>

<?php
$editMode = false;
$error = '';
$name = $datepublish = $description = '';

if(isset($_GET['id'])){
    $editMode = true;
    $id = $_GET['id'];
    $stmt = $dbh->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        header("Location: /");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $datepublish = $_POST['datepublish'];
    $description = $_POST['description'];
    $image = $_FILES["image"];

    $stmt = $dbh->prepare("SELECT * FROM news WHERE name = ?");
    $stmt->execute([$name]);
    $existingNews = $stmt->fetch();
    if ($existingNews && (!$editMode || $existingNews['id'] != $_POST['edit_id'])) {
        $error = "Новина з такою назвою вже існує.";
    } else {
        $folderName = $_SERVER['DOCUMENT_ROOT'] . '/' . UPLOADING;
        if (!file_exists($folderName)) {
            mkdir($folderName, 0777);
        }
        $image_save = "";
        if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image_save = uniqid() . '.' . pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $path_save = $folderName.'/'.$image_save;
            move_uploaded_file($_FILES['image']['tmp_name'], $path_save);
        } elseif ($editMode && !empty($row['image'])) {
            $image_save = $row['image'];
        }
        if(isset($_POST['edit_id'])) {
            $edit_id = $_POST['edit_id'];
            $stmt = $dbh->prepare("UPDATE news SET name = ?, datepublish = ?, description = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $datepublish, $description, $image_save, $edit_id]);
        } else {
            $stmt = $dbh->prepare("INSERT INTO news (name, datepublish, description, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $datepublish, $description, $image_save]);
        }
        $lastInsertedId = $dbh->lastInsertId();
        header("Location: /?id=".$lastInsertedId);
        exit();
    }
}
?>

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

    <h1 class="text-center"><?php echo $editMode ? 'Редагувати новину' : 'Додати новину'; ?></h1>

    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Назва</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($editMode ? $row['name'] : $name); ?>">
        </div>

        <div class="mb-3">
            <label for="datepublish" class="form-label">Дата публікації</label>
            <input type="datetime-local" class="form-control" id="datepublish" name="datepublish" value="<?php echo htmlspecialchars($editMode ? $row['datepublish'] : $datepublish); ?>">
        </div>

        <div class="mb-3">
            <div class="form-floating">
                <textarea class="form-control" placeholder="Вкажіть опис тут" name="description" id="description"
                          style="height: 100px"><?php echo htmlspecialchars($editMode ? $row['description'] : $description); ?></textarea>
                <label for="description">Опис</label>
            </div>
        </div>

        <div class="mb-3">
            <label for="formFile" class="form-label">Оберіть фото</label>
            <input class="form-control" type="file" id="image" name="image" accept="image/*">
            <?php if ($editMode && !empty($row['image'])): ?>
                <img src="/images/<?php echo $row['image']; ?>" alt="Попереднє фото" width="150">
            <?php endif; ?>
        </div>

        <?php if ($editMode): ?>
            <input type="hidden" name="edit_id" value="<?php echo $id; ?>">
        <?php endif; ?>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary"><?php echo $editMode ? 'Зберегти зміни' : 'Додати'; ?></button>
        </div>
    </form>
</div>

<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
