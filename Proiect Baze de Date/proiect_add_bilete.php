<?php
// Conectare la baza de date
$conn = new mysqli("localhost", "root", "", "proiect");

// Verificare conexiune
if ($conn->connect_error) {
    die("Conexiune esuata: " . $conn->connect_error);
}

$mesaj_eroare = "";
$mesaj_succes = "";
$bilet_edit = null;

// Preluam biletele din baza de date
$sql_bilete = "SELECT B.id_bilet, B.numar_bilete, B.pret, F.titlu FROM Bilete B JOIN Filme F ON B.id_film = F.id_film";
$result_bilete = $conn->query($sql_bilete);

// Gestionare bilete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_ticket'])) {
        // Adaugare bilet
        $id_film = $_POST['id_film'];
        $numar_bilete = $_POST['numar_bilete'];
        $pret = $_POST['pret'];

        $stmt = $conn->prepare("INSERT INTO Bilete (numar_bilete, pret, id_film) VALUES (?, ?, ?)");
        $stmt->bind_param("idi", $numar_bilete, $pret, $id_film);
        $stmt->execute();
        $mesaj_succes = "Biletul a fost adaugat cu succes!";
        $stmt->close();
    }

    if (isset($_POST['edit_ticket'])) {
        // Editare bilet
        $id_bilet = $_POST['id_bilet'];
        $numar_bilete = $_POST['numar_bilete'];
        $pret = $_POST['pret'];

        $stmt = $conn->prepare("UPDATE Bilete SET numar_bilete=?, pret=? WHERE id_bilet=?");
        $stmt->bind_param("idi", $numar_bilete, $pret, $id_bilet);
        $stmt->execute();
        $mesaj_succes = "Biletul a fost actualizat cu succes!";
        $stmt->close();
    }

    if (isset($_POST['delete_ticket'])) {
        // Stergere bilet
        $id_bilet = $_POST['id_bilet'];
        $stmt = $conn->prepare("DELETE FROM Bilete WHERE id_bilet=?");
        $stmt->bind_param("i", $id_bilet);
        $stmt->execute();
        $mesaj_succes = "Biletul a fost sters cu succes!";
        $stmt->close();
    }
}

// Preluam datele biletului pentru editare, daca este cazul
if (isset($_GET['edit_ticket_id'])) {
    $id_bilet = $_GET['edit_ticket_id'];
    $stmt = $conn->prepare("SELECT * FROM Bilete WHERE id_bilet=?");
    $stmt->bind_param("i", $id_bilet);
    $stmt->execute();
    $result = $stmt->get_result();
    $bilet_edit = $result->fetch_assoc();
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
        /* Stilurile existente */
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
            flex-direction: column;
            gap: 20px;
            max-width: 90vw;
            max-height: 80vh;
            overflow: auto;
        }
        .panel {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            max-height: 400px;
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
        input, button, select {
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
        <div class="panel">
            <h3>Adauga sau Editeaza Bilete</h3>
            <form method="POST">
                <select name="id_film" required>
                    <option value="">Selecteaza un film</option>
                    <?php
                    $conn = new mysqli("localhost", "root", "", "proiect");
                    $sql_filme = "SELECT id_film, titlu FROM Filme";
                    $result_filme = $conn->query($sql_filme);
                    while ($row = $result_filme->fetch_assoc()) {
                        $selected = $bilet_edit && $bilet_edit['id_film'] == $row['id_film'] ? 'selected' : '';
                        echo "<option value='" . $row['id_film'] . "' $selected>" . $row['titlu'] . "</option>";
                    }
                    $conn->close();
                    ?>
                </select>
                <input type="number" name="numar_bilete" placeholder="Numar bilete" value="<?php echo $bilet_edit['numar_bilete'] ?? ''; ?>" required><br />
                <input type="number" step="0.01" name="pret" placeholder="Pret bilet" value="<?php echo $bilet_edit['pret'] ?? ''; ?>" required><br />
                <button type="submit" name="<?php echo $bilet_edit ? 'edit_ticket' : 'add_ticket'; ?>">
                    <?php echo $bilet_edit ? 'Actualizeaza' : 'Adauga'; ?> Bilet
                </button>
                <?php if ($bilet_edit) { ?>
                    <input type="hidden" name="id_bilet" value="<?php echo $bilet_edit['id_bilet']; ?>">
                <?php } ?>
            </form>
        </div>

        <div class="panel">
            <h3>Lista Bilete</h3>
            <div class="table-wrapper">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Titlu Film</th>
                        <th>Numar Bilete</th>
                        <th>Pret</th>
                        <th>Actiuni</th>
                    </tr>
                    <?php while ($row = $result_bilete->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['id_bilet']; ?></td>
                            <td><?php echo $row['titlu']; ?></td>
                            <td><?php echo $row['numar_bilete']; ?></td>
                            <td><?php echo $row['pret']; ?></td>
                            <td>
                                <form method="GET" style="display:inline;">
                                    <input type="hidden" name="edit_ticket_id" value="<?php echo $row['id_bilet']; ?>">
                                    <button type="submit">Editeaza</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id_bilet" value="<?php echo $row['id_bilet']; ?>">
                                    <button type="submit" name="delete_ticket">Sterge</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
