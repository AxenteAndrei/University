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

// Daca formularul a fost trimis pentru rezervare
if (isset($_POST['film_id']) && isset($_POST['utilizator_id'])) {
    $film_id = $_POST['film_id'];
    $utilizator_id = $_POST['utilizator_id'];

    // Incepe tranzactia pentru a actualiza ambele tabele
    mysqli_begin_transaction($conn);

    try {
        // Creste numarul de bilete rezervate
        $sql_update_bilete = "UPDATE bilete SET bilete_rezervate = bilete_rezervate + 1 WHERE id_film = ?";
        $stmt = mysqli_prepare($conn, $sql_update_bilete);
        mysqli_stmt_bind_param($stmt, 'i', $film_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Creste numarul de bilete cumparate al utilizatorului
        $sql_update_utilizator = "UPDATE utilizatori SET bilete_cumparate = bilete_cumparate + 1 WHERE id_utilizator = ?";
        $stmt = mysqli_prepare($conn, $sql_update_utilizator);
        mysqli_stmt_bind_param($stmt, 'i', $utilizator_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Confirma tranzactia
        mysqli_commit($conn);

        // Redirectioneaza inapoi pe aceeasi pagina cu un mesaj de succes
        header('Location: proiect_bilete.php?success=1');
        exit();

    } catch (Exception $e) {
        // Daca apare o eroare, face rollback la tranzactie
        mysqli_rollBack($conn);
        die("Eroare la procesarea rezervarii: " . $e->getMessage());
    }
}

// Interogare pentru a obtine filmele si detalii despre bilete
$sql = "SELECT filme.id_film, filme.titlu, filme.imagine, bilete.numar_bilete, bilete.pret, bilete.bilete_rezervate 
        FROM filme 
        INNER JOIN bilete ON filme.id_film = bilete.id_film";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Eroare la interogare: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filme disponibile</title>
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
        .film-slider {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }
        .film-card {
            background-color: white;
            color: black;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            width: 200px;
        }
        .film-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }
        .film-card h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        .film-card p {
            margin: 5px 0;
            font-size: 14px;
        }
        button {
            padding: 10px;
            margin-top: 10px;
            font-size: 14px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .inapoi {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Filme disponibile</h1>
        <?php if (isset($_SESSION['id_utilizator'])): ?>
            <a class="inapoi" href="proiect_index.php"><- Inapoi</a>
        <?php endif; ?>
    </header>

    <div class="film-slider">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="film-card">
                <img src="<?= htmlspecialchars($row['imagine']) ?>" alt="Imagine film">
                <h3><?= htmlspecialchars($row['titlu']) ?></h3>
                <p>Bilete existente: <?= htmlspecialchars($row['numar_bilete']) ?></p>
                <p>Bilete rezervate: <?= htmlspecialchars($row['bilete_rezervate']) ?></p>
                <p>Pret: <?= htmlspecialchars($row['pret']) ?> lei</p>
                <?php if ($row['numar_bilete'] > 0): ?>
                    <form action="proiect_bilete.php" method="POST">
                        <input type="hidden" name="film_id" value="<?= $row['id_film'] ?>">
                        <input type="hidden" name="utilizator_id" value="<?= $_SESSION['id_utilizator'] ?>">
                        <button type="submit">Rezerva biletul</button>
                    </form>
                <?php else: ?>
                    <p style="color: red;">Stoc epuizat</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

    <?php
    // Afiseaza un mesaj de succes dupa rezervare
    if (isset($_GET['success']) && $_GET['success'] == 1) {
        echo '<p style="color: green; text-align: center;">Rezervarea a fost realizata cu succes!</p>';
    }
    ?>
</body>
</html>

<?php
// Inchide conexiunea la baza de date
mysqli_close($conn);
?>
