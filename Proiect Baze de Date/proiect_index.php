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

// Verificăm rolul utilizatorului sau al administratorului
$user_role = 'guest'; // Valoare implicită pentru un utilizator neautentificat

if (isset($_SESSION['id_utilizator'])) {
    $user_role = 'user'; // Utilizator standard
} elseif (isset($_SESSION['admin_nume'])) {
    $user_role = 'admin'; // Administrator
}

// Preluare gen selectat din URL
$gen_selectat = isset($_GET['gen']) ? $_GET['gen'] : null;

// Preluare termen cautare
$cautare_film = isset($_GET['search']) ? $_GET['search'] : null;

// Verifică dacă utilizatorul este autentificat și afișează link-ul de logout
$show_logout = ($user_role !== 'guest');
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proiect - Filme</title>
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
            flex-grow: 1;
            justify-content: space-between;
            padding: 20px;
        }
        .sidebar {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 200px;
            height: fit-content;
        }
        .sidebar-section {
            margin-bottom: 20px;
        }
        .category-link {
            text-decoration: none;
            color: #007bff;
            display: block;
            margin-bottom: 10px;
        }
        .category-link:hover {
            text-decoration: underline;
        }
        .film-slider {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            flex-grow: 1;
            padding: 20px;
        }
        .film-card {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
        }
        .film-image {
            width: 180px;
            height: 250px;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .film-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .film-info {
            font-size: 14px;
        }
        .search-bar {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-bar input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: 300px;
            margin-right: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        .search-bar button {
            padding: 10px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            background-color: #007bff;
            color: white;
        }
        footer {
            text-align: center;
            padding: 10px;
            color: white;
            background-color: #2F2F2F;
        }
        .logout-link {
            display: block;
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: left;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1 class="title">Website Filme - Proiect Baze de Date</h1>
    </header>

    <div class="container">
        <!-- Sidebar pentru categorii -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <b>Meniu</b>
                <hr>
                <a href="proiect_bilete.php" class="category-link">Cumpara Bilete</a>
				<a href="proiect_statistici.php" class="category-link">Statistici</a>
                <a href="proiect_recenzii.php" class="category-link">Reviews</a>
                <?php if ($user_role === 'admin'): ?>
                    <a href="proiect_administrare.php" class="category-link">Panou Admin</a> <!-- Link pentru administratori -->
                <?php endif; ?>
                <?php if ($user_role === 'admin'): ?>
                    <a href="proiect_add_bilete.php" class="category-link">Panou Stoc</a> <!-- Link pentru administratori -->
                <?php endif; ?>				
                <?php if ($show_logout): ?>
                    <a href="logout.php" class="logout-link">Deconectează-te</a> <!-- Link de logout -->
                <?php endif; ?>
                <hr>
                <a href="?" class="category-link">Toate</a> <!-- Categoria 'Toate' -->
                <?php
                // Selectare categorii distincte din tabelul filme
                $sql = "SELECT DISTINCT gen FROM filme ORDER BY gen ASC";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_array($result)) {
                    echo '<a href="?gen=' . urlencode($row['gen']) . '" class="category-link">' . htmlspecialchars($row['gen']) . '</a>';
                }
                ?>
            </div>
        </aside>

        <!-- Sectiunea de filme și căutare -->
        <div style="flex-grow: 1;">
            <!-- Caseta de căutare -->
            <div class="search-bar">
                <form action="" method="GET">
                    <input type="text" name="search" placeholder="Cauta un film..." value="<?php echo htmlspecialchars($cautare_film); ?>">
                    <button type="submit">Cauta</button>
                </form>
            </div>

            <div class="film-slider">
                <?php
                // Selectare filme în funcție de gen sau de termenul căutat
                if ($cautare_film) {
                    // Căutare după titlul filmului
                    $sql = "SELECT id_film, titlu, regizor, an, gen, durata_film, imagine FROM filme WHERE titlu LIKE ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    $cautare_film = '%' . $cautare_film . '%';
                    mysqli_stmt_bind_param($stmt, 's', $cautare_film);
                } elseif ($gen_selectat) {
                    // Filtrare după gen
                    $sql = "SELECT id_film, titlu, regizor, an, gen, durata_film, imagine FROM filme WHERE gen = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, 's', $gen_selectat);
                } else {
                    // Afișare toate filmele fără filtrare
                    $sql = "SELECT id_film, titlu, regizor, an, gen, durata_film, imagine FROM filme ORDER BY an DESC LIMIT 10";
                    $stmt = mysqli_prepare($conn, $sql);
                }

                // Executare interogare și afișare rezultate
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<div class="film-card">';
                        if (!empty($row['imagine']) && file_exists($row['imagine'])) {
                            echo '<img src="' . htmlspecialchars($row['imagine']) . '" alt="Coperta ' . htmlspecialchars($row['titlu']) . '" class="film-image">';
                        } else {
                            echo '<div class="film-image">Fara Imagine</div>';
                        }
                        echo '<h3 class="film-title">' . htmlspecialchars($row['titlu']) . '</h3>';
                        echo '<p class="film-info">Regizor: ' . htmlspecialchars($row['regizor']) . '</p>';
                        echo '<p class="film-info">An: ' . $row['an'] . '</p>';
                        echo '<p class="film-info">Durata: ' . $row['durata_film'] . ' min</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p style="color: white;">Niciun film gasit.</p>';
                }

                mysqli_stmt_close($stmt);
                ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 - Website Filme - Proiecte Baze de Date. </p>
    </footer>
</body>
</html>

<?php
mysqli_close($conn);
?>
