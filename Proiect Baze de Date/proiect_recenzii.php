<?php
session_start();

// Conectare la baza de date
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'proiect';
$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Conectare esuata: " . mysqli_connect_error());
}

// Verificare dacă utilizatorul este admin sau nu
$user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest'; // 'guest' dacă nu este setată sesiunea

// Verifică dacă utilizatorul este admin
$is_admin = ($user_role === 'admin');

// Obținere id utilizator (pentru useri)
$id_utilizator = isset($_SESSION['id_utilizator']) ? $_SESSION['id_utilizator'] : null;

// Adăugare recenzie
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $id_film = $_POST['id_film'];
    $text_recenzie = $_POST['text_recenzie'];
    $rating = $_POST['rating'];

    $sql_insert = "INSERT INTO recenzii (text_recenzie, rating, id_film, id_utilizator) VALUES (?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, 'siii', $text_recenzie, $rating, $id_film, $id_utilizator);
    mysqli_stmt_execute($stmt_insert);
    mysqli_stmt_close($stmt_insert);
}

// Stergere recenzie (doar pentru admini)
if ($is_admin && isset($_GET['delete_recenzie'])) {
    $id_recenzie = $_GET['delete_recenzie'];
    $sql_delete = "DELETE FROM recenzii WHERE id_recenzie = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, 'i', $id_recenzie);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);
}

// Selectare filme pentru slider
$sql_filme = "SELECT id_film, titlu FROM filme ORDER BY titlu ASC";
$result_filme = mysqli_query($conn, $sql_filme);

// Selectare toate recenziile
$sql_reviews = "
    SELECT recenzii.id_recenzie, recenzii.text_recenzie, recenzii.rating, recenzii.data_recenzie, 
           utilizatori.nume, filme.titlu 
    FROM recenzii 
    JOIN utilizatori ON recenzii.id_utilizator = utilizatori.id_utilizator
    JOIN filme ON recenzii.id_film = filme.id_film
    ORDER BY recenzii.data_recenzie DESC";
$result_reviews = mysqli_query($conn, $sql_reviews);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recenzii Filme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2F2F2F;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        header {
            text-align: center;
            color: white;
            padding: 20px;
        }
        .title {
            font-size: 24px;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
            padding: 20px;
        }
        .btn-back {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
        .review-form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            margin-bottom: 40px;
        }
        .review-form h2 {
            margin-bottom: 20px;
        }
        .review-form textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .review-form input[type="number"] {
            width: 60px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .review-form button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background-color: #007bff;
            color: white;
            font-size: 16px;
        }
        .review-list {
            width: 600px;
            margin-top: 20px;
        }
        .review-card {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .review-card .review-header {
            display: flex;
            justify-content: space-between;
        }
        .review-card .review-info {
            font-size: 14px;
            color: #555;
        }
        .review-card .review-text {
            margin-top: 10px;
        }
        .review-card .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1 class="title">Recenzii Filme</h1>
    </header>

    <div class="container">
        <!-- Buton pentru pagina principala -->
        <a href="proiect_index.php" class="btn-back">Inapoi la pagina principala</a>

        <!-- Formular pentru adăugarea unei recenzii (doar pentru utilizatori autentificați) -->
        <?php if ($is_admin || $id_utilizator): ?>
        <div class="review-form">
            <h2>Lasa o recenzie</h2>
            <form action="" method="POST">
                <label for="id_film">Selecteaza filmul:</label>
                <select name="id_film" id="id_film" required>
                    <?php
                    while ($row = mysqli_fetch_assoc($result_filme)) {
                        echo '<option value="' . $row['id_film'] . '">' . htmlspecialchars($row['titlu']) . '</option>';
                    }
                    ?>
                </select>

                <textarea name="text_recenzie" id="text_recenzie" required></textarea>

                <label for="rating">Numar de stele (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" required>

                <button type="submit" name="submit_review">Trimite recenzia</button>
            </form>
        </div>
        <?php else: ?>
        <p style="color: white;">Trebuie sa fii autentificat pentru a lasa o recenzie.</p>
        <?php endif; ?>

        <!-- Lista de recenzii -->
        <div class="review-list">
            <?php
            if (mysqli_num_rows($result_reviews) > 0) {
                while ($row = mysqli_fetch_assoc($result_reviews)) {
                    echo '<div class="review-card">';
                    echo '<div class="review-header">';
                    echo '<p class="review-info"><strong>' . htmlspecialchars($row['nume']) . '</strong> despre <strong>' . htmlspecialchars($row['titlu']) . '</strong></p>';
                    echo '<p class="review-info">' . htmlspecialchars($row['rating']) . ' stele</p>';
                    echo '</div>';
                    echo '<p class="review-text">' . htmlspecialchars($row['text_recenzie']) . '</p>';
                    echo '<p class="review-info">Data: ' . $row['data_recenzie'] . '</p>';
                    if ($is_admin) {
                        echo '<form action="" method="GET" style="margin-top: 10px;">';
                        echo '<button type="submit" class="delete-btn" name="delete_recenzie" value="' . $row['id_recenzie'] . '">Sterge recenzia</button>';
                        echo '</form>';
                    }
                    echo '</div>';
                }
            } else {
                echo '<p style="color: white;">Nu exista recenzii disponibile momentan.</p>';
            }
            ?>
        </div>
    </div>

</body>
</html>

<?php
// Inchiderea conexiunii la baza de date
mysqli_close($conn);
?>
