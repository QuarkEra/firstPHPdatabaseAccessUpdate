<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Farmers Forms & PHP</title>
   
</head>
<body>

    <?php 
    $servername = "localhost";
    $username = "user";
    $password = "1234";
    $dbname = "farmers_log";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected to database";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    $eggsCollectedError = $eggsUsedError = "";

    echo "<h1>Farmers Log</h1><br>";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["eggscollected"])) {
            $sql = "INSERT INTO eggs (eggsCollected) VALUES ('0')";
            $conn->exec($sql);
        } else {
            if (!is_numeric($_POST["eggscollected"])) {
                $eggsCollectedError = "numbers only please.";
            } else {
                $eggsCollected = cleanData($_POST["eggscollected"]);

                echo "<br>Last entry of eggs collected: " . $eggsCollected;
                try {
                    $sql = "INSERT INTO eggs (eggsCollected) VALUES ('$eggsCollected')";
                    $conn->exec($sql);
                } catch(PDOException $e) {
                    echo $sql . "<br>" . $e->getMessage();
                }
            }
        }
        if (empty($_POST["eggsused"])) {
            $sqlEggsUsed = "INSERT INTO eggs (eggsUsed) VALUES ('0')";
            $conn->exec($sqlEggsUsed);
        } else {
            if (!is_numeric(cleanData($_POST["eggsused"]))) {
                $eggsUsedError = "Numbers only please.";
            } else {
                $eggsUsed = cleanData($_POST["eggsused"]);

                echo "<br>Last entry of eggs used (sold/eaten): " . $eggsUsed;
                try {
                    $sqlEggsUsed = "INSERT INTO eggs (eggsUsed) VALUES ('$eggsUsed')";
                    $conn->exec($sqlEggsUsed);
                } catch (PDOException $e) {
                    echo "<br>" . $sqlEggsUsed . "<br>" . $e->getMessage();
                }
            }
        }
    }

    $sql = $conn->prepare("SELECT SUM(eggsCollected) AS totEggs FROM eggs");
    $sql->execute();
    $eggsTotal = 0;
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    $eggsTotal += $row['totEggs'];
    $prevEggsTotal = $eggsTotal;
    $sqlEggsUsed = $conn->prepare("SELECT SUM(eggsUsed) AS totEggsUsed FROM eggs");
    $sqlEggsUsed->execute();
    $eggsUsedTotal = 0;
    $rowOfUsed = $sqlEggsUsed->fetch(PDO::FETCH_ASSOC);
    $eggsUsedTotal += $rowOfUsed['totEggsUsed'];
    $eggs = $eggsTotal - $eggsUsedTotal;
    echo "<br> Total eggs: " . $eggs;
    echo "<br> Previous Total eggs: " . $prevEggsTotal;


    function cleanData($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $conn = null;
    ?>

    <div class="page-content">

        <section class="PHP-form-content">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="row-wrapper">
                    <div class="row-title">
                        <h2>Eggs</h2>
                    </div>
                    <div class="row-contents">
                                   
                        <h3>Newly Laid</h3>
                            Eggs collected today:<input type="text" name="eggscollected"><br>
                            <span><?php echo $eggsCollectedError;?></span>
                        <h3>Used (sold/eaten)</h3>
                            Eggs sold today:<input type="text" name="eggsused"><br>
                            <span><?php echo $eggsUsedError;?></span>
     
                        <h3>Brought Forward</h3>
                        <input type="submit">
                        
                    </div>
                </div>
            </form>
        </section>  
    </div>
</body>
</html>