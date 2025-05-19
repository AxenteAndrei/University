<?php
// Conectarea la baza de date
$conn = mysqli_connect("localhost", "root", "", "proiect");

// Verificarea conexiunii
if (!$conn) {
    die("Conexiune esuata: " . mysqli_connect_error());
}

// Seteaza un mesaj gol pentru succes sau eroare
$mesaj = "";

// RegExp pentru validarea adresei de email
$regexp_mail = '/^([a-zA-Z0-9]+[a-zA-Z0-9._%-]*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4})$/';

// Verifica daca datele din formular sunt trimise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Elimina tag-urile si spatiile goale de la inceput si sfarsit
    $nume = trim(strip_tags($_POST['nume']));
    $parola = trim($_POST['parola']);
    $email = trim(strip_tags($_POST['email']));

    // Seteaza un array pentru erori
    $erori = array();

    // Validarea campurilor din formular
    if (!preg_match($regexp_mail, $email)) {
        $erori[] = 'Adresa de e-mail este incorecta';
    }
    if (strlen($nume) < 3) {
        $erori[] = 'Numele trebuie sa contina minim 3 caractere';
    }
    if (strlen($parola) < 6) {
        $erori[] = 'Parola trebuie sa contina minim 6 caractere';
    }

    // Daca nu exista erori
    if (empty($erori)) {
        // Criptarea parolei
        $parola_hash = password_hash($parola, PASSWORD_DEFAULT);

        // Interogarea SQL pentru a introduce utilizatorul in tabel
        $sql = "INSERT INTO utilizatori (nume, parola, email) VALUES ('$nume', '$parola_hash', '$email')";

        if (mysqli_query($conn, $sql)) {
            $mesaj = 'Contul a fost creat cu succes!';
        } else {
            $mesaj = 'Eroare la crearea contului: ' . mysqli_error($conn);
        }
    } else {
        // Daca exista erori, le concatenam intr-un mesaj
        $mesaj = implode('<br />', $erori);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Inregistrare Utilizatori</title>
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
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 10px;
            color: green;
        }
        .login-link {
            margin-top: 10px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-link">
            <a href="proiect_login.php">Ai un cont deja? Login</a>
        </div>
        <form action="" method="post">
            <h2>Inregistrare</h2>
            <input type="text" name="nume" placeholder="Nume" required /><br />
            <input type="password" name="parola" placeholder="Parola" required /><br />
            <input type="text" name="email" placeholder="E-mail" required /><br />
            <input type="submit" value="Creeaza cont" />
        </form>
        <?php if (!empty($mesaj)) { ?>
            <div class="message"><?php echo $mesaj; ?></div>
        <?php } ?>
    </div>
</body>
</html>
