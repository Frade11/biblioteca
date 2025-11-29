<?php
include 'conexiune.php';

// cautare dupa pret
$carti_scumpe = [];
if (isset($_POST['cauta_carti_scumpe'])) {
    $pret_minim = $_POST['pret_minim'];
    $carti_scumpe = $conn->query("
        SELECT * FROM carti 
        WHERE Pret > $pret_minim 
        ORDER BY Pret DESC
    ");
}

// ne returnate
$cititori_neterurnate = $conn->query("
    SELECT c.*, carti.Titlu 
    FROM cititori c 
    JOIN carti ON c.CodCarte = carti.CodCarte 
    WHERE c.DataReturnare IS NULL 
    AND c.DataImprumut < CURDATE()
    ORDER BY c.DataImprumut
");

// Raport edituri
$rapoarte_edituri = $conn->query("
    SELECT Editura, COUNT(*) as NumarCarti, AVG(Pret) as PretMediu 
    FROM carti 
    GROUP BY Editura 
    ORDER BY NumarCarti DESC
");
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapoarte - Biblioteca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Biblioteca</a>
            <ul class="nav-links">
                <li><a href="carti.php">Carti</a></li>
                <li><a href="cititori.php">Cititori</a></li>
                <li><a href="rapoarte.php" class="active">Rapoarte</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Rapoarte și Cautari</h2>

        <!-- Căutare cărți scumpe -->
        <div class="card">
            <div class="card-header">
                <h3>Cautare carti scumpe</h3>
            </div>
            <div class="card-body">
                <form method="post" class="form-row">
                    <div class="form-group">
                        <label>Pret minim:</label>
                        <input type="number" step="0.01" class="form-control" name="pret_minim" 
                               placeholder="Introduceți prețul minim" required>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" name="cauta_carti_scumpe" class="btn btn-primary d-block">Cauta</button>
                    </div>
                </form>

                <?php if (isset($_POST['cauta_carti_scumpe'])): ?>
                    <div class="mt-2">
                        <h4>Carti cu pret peste <?php echo $_POST['pret_minim']; ?> Lei:</h4>
                        <?php if ($carti_scumpe->num_rows > 0): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Titlu</th>
                                        <th>Autor</th>
                                        <th>Editura</th>
                                        <th>Pret</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($carte = $carti_scumpe->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($carte['Titlu']); ?></td>
                                        <td><?php echo htmlspecialchars($carte['Autor']); ?></td>
                                        <td><?php echo htmlspecialchars($carte['Editura']); ?></td>
                                        <td><strong><?php echo $carte['Pret']; ?> Lei</strong></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">Nu s-au gasit carti cu pretul peste valoarea introdusa</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cititori care nu au returnat cartile -->
        <div class="card">
            <div class="card-header">
                <h3>Cititori care nu au returnat cartile</h3>
            </div>
            <div class="card-body">
                <?php if ($cititori_neterurnate->num_rows > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nume</th>
                                <th>Prenume</th>
                                <th>Telefon</th>
                                <th>Carte imprumutată</th>
                                <th>Data împrumut</th>
                                <th>Zile intarziere</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($cititor = $cititori_neterurnate->fetch_assoc()): 
                                $zile_intarziere = floor((time() - strtotime($cititor['DataImprumut'])) / (60 * 60 * 24));
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cititor['Nume']); ?></td>
                                <td><?php echo htmlspecialchars($cititor['Prenume']); ?></td>
                                <td><?php echo htmlspecialchars($cititor['Telefon']); ?></td>
                                <td><?php echo htmlspecialchars($cititor['Titlu']); ?></td>
                                <td><?php echo $cititor['DataImprumut']; ?></td>
                                <td><span class="badge badge-danger"><?php echo $zile_intarziere; ?> zile</span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Toti cititorii au returnat cartile la timp</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Raport edituri -->
        <div class="card">
            <div class="card-header">
                <h3>Raport edituri</h3>
            </div>
            <div class="card-body">
                <?php if ($rapoarte_edituri->num_rows > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Editura</th>
                                <th>Numar carti</th>
                                <th>Pret mediu</th>
                                <th>Procent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_carti = $conn->query("SELECT COUNT(*) as total FROM carti")->fetch_assoc()['total'];
                            while($raport = $rapoarte_edituri->fetch_assoc()): 
                                $procent = round(($raport['NumarCarti'] / $total_carti) * 100, 1);
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($raport['Editura']); ?></td>
                                <td><?php echo $raport['NumarCarti']; ?></td>
                                <td><?php echo round($raport['PretMediu'], 2); ?> Lei</td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?php echo $procent; ?>%">
                                            <?php echo $procent; ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Nu exista date pentru raport</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>