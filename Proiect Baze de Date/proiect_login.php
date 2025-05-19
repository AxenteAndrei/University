<?php
// Conectarea la baza de date
$conn = new mysqli("localhost", "root", "", "proiect");

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Conexiune esuata: " . $conn->connect_error);
}

$mesaj_eroare = ""; // Variabila pentru mesajul de eroare

// Daca formularul de autentificare este trimis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = $_POST['email_or_username']; // Nume de utilizator sau email
    $parola = $_POST['parola'];

    // Verificarea utilizatorului in tabelul `utilizatori`
    $stmt = $conn->prepare("SELECT id_utilizator, nume, email, parola FROM utilizatori WHERE email = ?");
    $stmt->bind_param("s", $email_or_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Utilizator gasit
        $stmt->bind_result($id_utilizator, $nume_utilizator, $email_utilizator, $parola_hash);
        $stmt->fetch();

        // Verificarea parolei utilizatorului
        if (password_verify($parola, $parola_hash)) {
            // Autentificare reusita pentru utilizator
            session_start();
            $_SESSION['id_utilizator'] = $id_utilizator;
            $_SESSION['nume'] = $nume_utilizator;
            $_SESSION['email'] = $email_utilizator;

            // Redirectionare la pagina principala
            header("Location: proiect_index.php");
            exit;
        } else {
            $mesaj_eroare = "Parola incorecta.";
        }
    } else {
        // Verificare administrator in tabelul `admin`
        $stmt = $conn->prepare("SELECT admin_nume, admin_parola FROM admin WHERE admin_nume = ?");
        $stmt->bind_param("s", $email_or_username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Administrator gasit
            $stmt->bind_result($admin_nume, $admin_parola_hash);
            $stmt->fetch();

            // Verificarea parolei administratorului
            if (password_verify($parola, $admin_parola_hash)) {
                // Autentificare reusita pentru administrator
                session_start();
                $_SESSION['admin_nume'] = $admin_nume;

                // Redirectionare la pagina principala pentru admin
                header("Location: proiect_index.php");
                exit;
            } else {
                $mesaj_eroare = "Parola incorecta pentru administrator.";
            }
        } else {
            $mesaj_eroare = "Email-ul sau numele de utilizator nu exista in baza de date.";
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2F2F2F; /* Gri spre negru */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 280px; /* Ajustare lățime pentru a încadra câmpurile mai bine */
        }
        form {
            margin: 0;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Pentru a nu depăși dimensiunea containerului */
        }
        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 10px;
            color: red;
            font-weight: bold;
        }
        .signup-link {
            margin-top: 10px;
        }
        .signup-link a {
            color: #007bff;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-link">
            <a href="proiect_signup.php">Nu ai un cont? Inregistrare</a>
        </div>
        <form method="POST" action="">
            <h2>Autentificare</h2>
            <input type="text" name="email_or_username" placeholder="Email" required><br />
            <input type="password" name="parola" placeholder="Parola" required><br />
            <button type="submit">Autentificare</button>
        </form>
        <?php if (!empty($mesaj_eroare)) { ?>
            <div class="message"><?php echo $mesaj_eroare; ?></div>
        <?php } ?>
    </div>
</body>
</html>
