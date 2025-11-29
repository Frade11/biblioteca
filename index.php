<?php
include 'conexiune.php';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Biblioteca</a>
            <ul class="nav-links">
                <li><a href="carti.php">Carti</a></li>
                <li><a href="cititori.php">Cititori</a></li>
                <li><a href="rapoarte.php">Rapoarte</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h1 class="text-center mb-4">Bine ati venit în Sistemul Bibliotecii</h1>
        
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h3>Carti</h3>
                        <p>Gestionează catalogul de carti</p>
                        <a href="carti.php" class="btn btn-success">Acceseaza</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h3>Cititori</h3>
                        <p>Gestioneaza cititorii si imprumuturile</p>
                        <a href="cititori.php" class="btn btn-primary">Acceseaza</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h3>Rapoarte</h3>
                        <p>Vezi rapoarte si statistici</p>
                        <a href="rapoarte.php" class="btn btn-warning">Acceseaza</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-6">
                <h3>Statistici rapide</h3>
                <?php
                $total_carti = $conn->query("SELECT COUNT(*) as total FROM carti")->fetch_assoc()['total'];
                $total_cititori = $conn->query("SELECT COUNT(*) as total FROM cititori")->fetch_assoc()['total'];
                $imprumuturi_active = $conn->query("SELECT COUNT(*) as total FROM cititori WHERE DataReturnare IS NULL")->fetch_assoc()['total'];
                ?>
                <ul class="list-group">
                    <li class="list-group-item">Total carti: <strong><?php echo $total_carti; ?></strong></li>
                    <li class="list-group-item">Total cititori: <strong><?php echo $total_cititori; ?></strong></li>
                    <li class="list-group-item">Imprumuturi active: <strong><?php echo $imprumuturi_active; ?></strong></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>