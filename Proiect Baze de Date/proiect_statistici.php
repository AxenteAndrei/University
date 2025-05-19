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

// Interogare pentru primii 3 utilizatori cu cele mai multe bilete rezervate
$sql_top_utilizatori = "SELECT id_utilizator, nume, bilete_cumparate FROM utilizatori ORDER BY bilete_cumparate DESC LIMIT 3";
$result_top_utilizatori = mysqli_query($conn, $sql_top_utilizatori);

// Interogare pentru numarul de filme existente
$sql_numar_filme = "SELECT COUNT(*) AS numar_filme FROM filme";
$result_numar_filme = mysqli_query($conn, $sql_numar_filme);
$row_numar_filme = mysqli_fetch_assoc($result_numar_filme);
$numar_filme = $row_numar_filme['numar_filme'];

// Interogare pentru numarul de conturi create
$sql_numar_utilizatori = "SELECT COUNT(*) AS numar_utilizatori FROM utilizatori";
$result_numar_utilizatori = mysqli_query($conn, $sql_numar_utilizatori);
$row_numar_utilizatori = mysqli_fetch_assoc($result_numar_utilizatori);
$numar_utilizatori = $row_numar_utilizatori['numar_utilizatori'];

// Interogare pentru filmul cu cele mai multe recenzii
$sql_film_cu_most_reviews = "SELECT id_film, COUNT(*) AS numar_recenzii FROM recenzii GROUP BY id_film ORDER BY numar_recenzii DESC LIMIT 1";
$result_film_cu_most_reviews = mysqli_query($conn, $sql_film_cu_most_reviews);
$row_film_cu_most_reviews = mysqli_fetch_assoc($result_film_cu_most_reviews);
$film_cu_most_reviews_id = $row_film_cu_most_reviews['id_film'];

// Interogare pentru detalii despre filmul cu cele mai multe recenzii
$sql_film_details = "SELECT titlu FROM filme WHERE id_film = $film_cu_most_reviews_id";
$result_film_details = mysqli_query($conn, $sql_film_details);
$row_film_details = mysqli_fetch_assoc($result_film_details);
$film_cu_most_reviews = $row_film_details['titlu'];

// Interogare pentru numarul total de bilete rezervate
$sql_total_bilete_rezervate = "SELECT SUM(bilete_rezervate) AS total_bilete_rezervate FROM bilete";
$result_total_bilete_rezervate = mysqli_query($conn, $sql_total_bilete_rezervate);
$row_total_bilete_rezervate = mysqli_fetch_assoc($result_total_bilete_rezervate);
$total_bilete_rezervate = $row_total_bilete_rezervate['total_bilete_rezervate'];

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistici</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2F2F2F;
            color: white;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 32px;
        }
        .statistic-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 30px;
        }
        .statistic-item {
            background-color: #444;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            width: 80%;
            text-align: center;
        }
        .statistic-item h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .inapoi {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .inapoi:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Statistici</h1>
        <a class="inapoi" href="proiect_index.php"><- INAPOI</a>
    </header>

    <div class="statistic-container">
        <div class="statistic-item">
            <h3>Primii 3 utilizatori cu cele mai multe bilete rezervate</h3>
            <ul>
                <?php while ($row = mysqli_fetch_assoc($result_top_utilizatori)): ?>
                    <li><?= htmlspecialchars($row['nume']) ?> - <?= htmlspecialchars($row['bilete_cumparate']) ?> bilete</li>
                <?php endwhile; ?>
            </ul>
        </div>

        <div class="statistic-item">
            <h3>Numarul de filme existente pe site</h3>
            <p><?= htmlspecialchars($numar_filme) ?></p>
        </div>

        <div class="statistic-item">
            <h3>Numarul de conturi create</h3>
            <p><?= htmlspecialchars($numar_utilizatori) ?></p>
        </div>

        <div class="statistic-item">
            <h3>Filmul cu cele mai multe recenzii</h3>
            <p><?= htmlspecialchars($film_cu_most_reviews) ?></p>
        </div>

        <div class="statistic-item">
            <h3>Numarul total de bilete rezervate</h3>
            <p><?= htmlspecialchars($total_bilete_rezervate) ?> bilete</p>
        </div>
    </div>
</body>
</html>

<?php
// Închide conexiunea la baza de date
mysqli_close($conn);
?>
