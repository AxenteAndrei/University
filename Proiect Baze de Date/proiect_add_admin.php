<html>
<body>
<?php
$conn = @new mysqli('localhost', 'root', '', 'proiect'); // Asigură-te că folosești baza de date corectă

if (mysqli_connect_errno()) {
    exit('Conectare nereusita: '.mysqli_connect_error());
} else {
    echo 'Conectare reusita';
}

// Setează valorile pentru admin
$admin_nume = 'contadmin';
$admin_parola = password_hash('admin123', PASSWORD_DEFAULT); // CRIPTARE PAROLA

// Comanda SQL pentru inserarea datelor în tabela 'admin'
$sql = "INSERT INTO `admin` (`admin_nume`, `admin_parola`) VALUES ('$admin_nume', '$admin_parola')";

if (mysqli_query($conn, $sql) === TRUE) {
    echo '</br> Datele au fost adaugate';
} else {
    echo 'Error: ' . $conn->error;
}

$conn->close();
?>
</body>
</html>
