<?php
// Conectare la baza de date
$conn = new mysqli("localhost", "root", "", "proiect");

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiune esuata: " . $conn->connect_error);
}

$mesaj_eroare = "";
$mesaj_succes = "";
$film_edit = null;

// Preluam filmele din baza de date
$sql = "SELECT * FROM Filme";
$result = $conn->query($sql);

// Adaugare, editare sau stergere film
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_movie'])) {
        // Adaugare film
        $titlu = $_POST['titlu'];
        $gen = $_POST['gen'];
        $regizor = $_POST['regizor'];
        $an = $_POST['an'];
        $durata_film = $_POST['durata_film'];
        
        if (isset($_FILES['imagine']) && $_FILES['imagine']['error'] == 0) {
            $imagine = $_FILES['imagine'];
            $cale_destinatie = "images/" . basename($imagine['name']);
            if (!file_exists('images')) {
                mkdir('images', 0777, true);
            }
            if (move_uploaded_file($imagine['tmp_name'], $cale_destinatie)) {
                $stmt = $conn->prepare("INSERT INTO Filme (titlu, gen, regizor, an, durata_film, imagine) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssds", $titlu, $gen, $regizor, $an, $durata_film, $cale_destinatie);
                $stmt->execute();
                $mesaj_succes = "Filmul a fost adaugat cu succes!";
                $stmt->close();
            } else {
                $mesaj_eroare = "Eroare la incarcare imagine.";
            }
        } else {
            $mesaj_eroare = "Nu a fost selectata nicio imagine.";
        }
    }

    if (isset($_POST['edit_movie'])) {
        // Editare film
        $id_film = $_POST['id_film'];
        $titlu = $_POST['titlu'];
        $gen = $_POST['gen'];
        $regizor = $_POST['regizor'];
        $an = $_POST['an'];
        $durata_film = $_POST['durata_film'];
        
        // Daca este selectata o imagine, o incarcam
        if (isset($_FILES['imagine']) && $_FILES['imagine']['error'] == 0) {
            $imagine = $_FILES['imagine'];
            $cale_destinatie = "images/" . basename($imagine['name']);
            if (move_uploaded_file($imagine['tmp_name'], $cale_destinatie)) {
                $stmt = $conn->prepare("UPDATE Filme SET titlu=?, gen=?, regizor=?, an=?, durata_film=?, imagine=? WHERE id_film=?");
                $stmt->bind_param("ssssdsi", $titlu, $gen, $regizor, $an, $durata_film, $cale_destinatie, $id_film);
                $stmt->execute();
                $mesaj_succes = "Filmul a fost actualizat cu succes!";
                $stmt->close();
            } else {
                $mesaj_eroare = "Eroare la incarcare imagine.";
            }
        } else {
            // Daca nu este selectata o imagine, actualizam doar celelalte campuri
            $stmt = $conn->prepare("UPDATE Filme SET titlu=?, gen=?, regizor=?, an=?, durata_film=? WHERE id_film=?");
            $stmt->bind_param("ssssdi", $titlu, $gen, $regizor, $an, $durata_film, $id_film);
            $stmt->execute();
            $mesaj_succes = "Filmul a fost actualizat cu succes!";
            $stmt->close();
        }
    }

    if (isset($_POST['delete_movie'])) {
        // Stergere film
        $id_film = $_POST['id_film'];
        $stmt = $conn->prepare("DELETE FROM Filme WHERE id_film=?");
        $stmt->bind_param("i", $id_film);
        $stmt->execute();
        $mesaj_succes = "Filmul a fost sters cu succes!";
        $stmt->close();
    }
}

// Preluam datele filmului pentru editare, daca este cazul
if (isset($_GET['edit_id'])) {
    $id_film = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM Filme WHERE id_film=?");
    $stmt->bind_param("i", $id_film);
    $stmt->execute();
    $result = $stmt->get_result();
    $film_edit = $result->fetch_assoc();
    $stmt->close();
}

// Inchidem conexiunea la baza de date
$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panou Administrare</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2F2F2F;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            overflow: auto; /* Permite derularea pe ambele axe */
            max-width: 90vw; /* Lățimea maximă a containerului */
            max-height: 80vh; /* Înălțimea maximă a containerului */
        }
        .left-panel, .right-panel {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 48%;
        }
        .table-wrapper {
            width: 100%;
            overflow-x: auto; /* Permite derularea orizontală */
            overflow-y: auto; /* Permite derularea verticală */
            -webkit-overflow-scrolling: touch;
            max-height: 400px; /* Setăm o înălțime maximă pentru tabel */
        }
        table {
            width: 100%;
            min-width: 1000px;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        input, button {
            width: 90%;
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
            width: 90%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 10px;
            color: red;
            font-weight: bold;
        }
        .success {
            margin-top: 10px;
            color: green;
            font-weight: bold;
        }
        .back-link {
            margin-top: 10px;
            text-align: center;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
	<div style="text-align: center; margin-bottom: 20px;">
    <a href="proiect_index.php" style="text-decoration: none;">
        <button style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            <- INAPOI
        </button>
    </a>
	</div>
    <div class="container">
        <div class="left-panel">
            <h3>Lista Filmelor</h3>
            <?php if (isset($_GET['edit_id'])): ?>
                <div class="back-link">
                    <a href="proiect_administrare.php">Intoarce-te</a>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Titlu</th>
                            <th>Gen</th>
                            <th>Regizor</th>
                            <th>An</th>
                            <th>Durata (minute)</th>
                            <th>Imagine</th>
                            <th>Actiuni</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id_film']; ?></td>
                                <td><?php echo $row['titlu']; ?></td>
                                <td><?php echo $row['gen']; ?></td>
                                <td><?php echo $row['regizor']; ?></td>
                                <td><?php echo $row['an']; ?></td>
                                <td><?php echo $row['durata_film']; ?></td>
                                <td><img src="<?php echo $row['imagine']; ?>" alt="Imagine film" width="50"></td>
                                <td>
                                    <form method="GET" style="display:inline;">
                                        <input type="hidden" name="edit_id" value="<?php echo $row['id_film']; ?>">
                                        <button type="submit">Editeaza</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id_film" value="<?php echo $row['id_film']; ?>">
                                        <button type="submit" name="delete_movie">Sterge</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <div class="right-panel">
            <h3><?php echo $film_edit ? "Editeaza Filmul" : "Adauga Filmul"; ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="titlu" placeholder="Titlu" value="<?php echo $film_edit['titlu'] ?? ''; ?>" required><br />
                <input type="text" name="gen" placeholder="Gen" value="<?php echo $film_edit['gen'] ?? ''; ?>" required><br />
                <input type="text" name="regizor" placeholder="Regizor" value="<?php echo $film_edit['regizor'] ?? ''; ?>" required><br />
                <input type="number" name="an" placeholder="Anul lansarii" value="<?php echo $film_edit['an'] ?? ''; ?>" required><br />
                <input type="number" name="durata_film" placeholder="Durata (minute)" value="<?php echo $film_edit['durata_film'] ?? ''; ?>" required><br />
                <input type="file" name="imagine" accept="image/*"><br />
                <button type="submit" name="<?php echo $film_edit ? 'edit_movie' : 'add_movie'; ?>"><?php echo $film_edit ? 'Actualizeaza' : 'Adauga'; ?> Film</button>
                <?php if ($film_edit) { ?>
                    <input type="hidden" name="id_film" value="<?php echo $film_edit['id_film']; ?>">
                <?php } ?>
            </form>
            <?php if (!empty($mesaj_eroare)) { ?>
                <div class="message"><?php echo $mesaj_eroare; ?></div>
            <?php } ?>
            <?php if (!empty($mesaj_succes)) { ?>
                <div class="success"><?php echo $mesaj_succes; ?></div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
