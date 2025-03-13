<?php
include("../DB-Connection/DB_Crimson.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $klant_naam = $_POST['klant_naam'];
    $klant_email = $_POST['klant_email'];
    $lening_bedrag = $_POST['lening_bedrag'];
    $lening_duur = $_POST['lening_duur'];
    $lening_status = $_POST['lening_status'];

    // First, find the klant_id based on the provided name and email
    $find_klant = "SELECT klant_id FROM klanten WHERE klant_naam = ? AND klant_email = ?";
    $stmt_find = $conn->prepare($find_klant);
    $stmt_find->bind_param("ss", $klant_naam, $klant_email);
    $stmt_find->execute();
    $result_klant = $stmt_find->get_result();
    
    if($result_klant->num_rows > 0) {
        $klant_row = $result_klant->fetch_assoc();
        $klant_id = $klant_row['klant_id'];
        
        // Now insert the loan with the found klant_id
        $sql = "INSERT INTO leningen (klant_id, lening_bedrag, lening_duur, lening_status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $klant_id, $lening_bedrag, $lening_duur, $lening_status);
        
        if ($stmt->execute()){
            header("Location: Bank_Leningen.php");
        } else {
            echo "Lening is niet toegevoegd: " . $conn->error;
        }
        
        $stmt->close();
    } else {
        echo "Klant niet gevonden. Controleer naam en email.";
    }
    $stmt_find->close();


//Searchbar
if(isset($_POST['search'])) {
    $search = $_POST['search'];
    $sql = "SELECT klanten.klant_naam, klanten.klant_email, leningen.* 
            FROM leningen 
            JOIN klanten ON leningen.klant_id = klanten.klant_id
            WHERE klanten.klant_naam LIKE '%$search%'";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT klanten.klant_naam, klanten.klant_email, leningen.* 
            FROM leningen 
            JOIN klanten ON leningen.klant_id = klanten.klant_id";
    $result = $conn->query($sql);
}




   
}

// Correct query to fetch both client and loan information
$sql = "SELECT klanten.klant_naam, klanten.klant_email, leningen.* 
        FROM leningen 
        JOIN klanten ON leningen.klant_id = klanten.klant_id";
$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Leningen</title>
    <link rel="stylesheet" href="Bank_leningen.css"> 
</head>
<body>
    <div class="sidebar">
        <h2>Hoofdmenu</h2>
        <ul>
            <a href="../Voegklanten/Bank_Voegklant.php">Klanten</a>
            <a href="Bank_Leningen.php">Leningen</a>
        </ul>
    </div>
    <div class="main-content">
        <header>
            <h1>Welkom Terug👋</h1>
            <span class="user">P.Diddy</span>
        </header>
        <div class="cards">
            <div class="card purple">
                <h2>450 Leningen</h2>
                <p>Totaal Lening Bedrag</p>
                <span>30 April 2022</span>
            </div>
            <div class="card red">
                <h2>310 Leningen</h2>
                <p>In Behandeling</p>
                <span>30 April 2022</span>
            </div>
            <div class="card yellow">
                <h2>560 Leningen</h2>
                <p>Actieve Leningen</p>
                <span>30 April 2022</span>
            </div>
        </div>
        <h2>Nieuwe Lening Toevoegen</h2>
        
        <form action="" method="post">
            <label for="klant_naam">Klant Naam:</label>
            <input type="text" id="klant_naam" name="klant_naam" placeholder="Naam" required> 

            <label for="klant_email">Klant Email:</label>
            <input type="email" id="klant_email" name="klant_email" placeholder="Email" required> 

            <label for="lening_bedrag">Lening Bedrag:</label>
            <input type="number" id="lening_bedrag" name="lening_bedrag" required><br>

            <label for="lening_duur">Lening Duur (Maanden):</label>
            <input type="number" id="lening_duur" name="lening_duur" required> <br>

            <label for="lening_status">Lening Status:</label>
            <select id="lening_status" name="lening_status" required> 
                <option value="In behandeling">In behandeling</option>
                <option value="Goedgekeurd">Goedgekeurd</option>
                <option value="Afgekeurd">Afgekeurd</option>
                <option value="Afgerond">Afgerond</option>
            </select> <br>

            <button type="submit" value="Submit">Voeg Lening Toe</button>
        </form>


        <h2>Alle Leningen</h2>
        <form action="" method="GET">
            <input id="search" type="text" name="search" placeholder="Zoek op naam">
            <button  class="Search-button" type="submit" value="Submit">Zoek</button>
</form>
        <table>
            <thead>
                <tr>
                    <th>Naam</th>
                    <th>E-mail</th>
                    <th>Lening Bedrag</th>
                    <th>Lening Duur</th>
                    <th>Lening Status</th>
                    <th>Acties</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Haal leningen op en toon ze uit de database
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["klant_naam"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row['klant_email']) . "</td>";
                        echo "<td>SRD " . htmlspecialchars($row['lening_bedrag']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['lening_duur']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['lening_status']) . "</td>";
                        echo "<td>
                                <a href='../Lening_Update&Delete/Lening_Delete.php?lening_id=" . $row['lening_id'] . "'>
                                    <button class='Delete-Button' type='button'>Verwijder</button>
                                </a>
                                <a href='../Lening_Update&Delete/Lening_Update.php?lening_id=" . $row['lening_id'] . "'>
                                    <button class='Update-Button' type='button'>Update</button>
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Geen leningen gevonden</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>