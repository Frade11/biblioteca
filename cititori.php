<?php
session_start();
include 'conexiune.php';
$mesaj = '';
$tip_mesaj = 'info';

// Adaugare cititor
if (isset($_POST['adauga_cititor'])) {
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $adresa = $_POST['adresa'];
    $telefon = $_POST['telefon'];
    $cod_carte = $_POST['cod_carte'] ?: NULL;
    $data_imprumut = $_POST['data_imprumut'] ?: NULL;
    $data_returnare = $_POST['data_returnare'] ?: NULL;

    $sql = "INSERT INTO cititori (Nume, Prenume, Adresa, Telefon, CodCarte, DataImprumut, DataReturnare) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiss", $nume, $prenume, $adresa, $telefon, $cod_carte, $data_imprumut, $data_returnare);
    
    if ($stmt->execute()) {
        $_SESSION['mesaj'] = "Cititorul a fost adăugat cu succes!";
        $_SESSION['tip_mesaj'] = "success";
        header('Location: cititori.php');
        exit();
    } else {
        $mesaj = "Eroare la adăugare: " . $conn->error;
        $tip_mesaj = "danger";
    }
}

// stergere cititor
if (isset($_GET['sterge_cititor'])) {
    $cod = $_GET['sterge_cititor'];
    $sql = "DELETE FROM cititori WHERE CodCititor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cod);
    
    if ($stmt->execute()) {
        $_SESSION['mesaj'] = "Cititorul a fost șters cu succes!";
        $_SESSION['tip_mesaj'] = "success";
        header('Location: cititori.php');
        exit();
    } else {
        $mesaj = "Eroare la ștergere: " . $conn->error;
        $tip_mesaj = "danger";
    }
}

// Modificare cititor
if (isset($_POST['modifica_cititor'])) {
    $cod = $_POST['cod_cititor'];
    $nume = $_POST['nume'];
    $prenume = $_POST['prenume'];
    $adresa = $_POST['adresa'];
    $telefon = $_POST['telefon'];
    $cod_carte = $_POST['cod_carte'] ?: NULL;
    $data_imprumut = $_POST['data_imprumut'] ?: NULL;
    $data_returnare = $_POST['data_returnare'] ?: NULL;

    $sql = "UPDATE cititori SET Nume=?, Prenume=?, Adresa=?, Telefon=?, CodCarte=?, DataImprumut=?, DataReturnare=? 
            WHERE CodCititor=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissi", $nume, $prenume, $adresa, $telefon, $cod_carte, $data_imprumut, $data_returnare, $cod);
    
    if ($stmt->execute()) {
        $_SESSION['mesaj'] = "Cititorul a fost actualizat cu succes!";
        $_SESSION['tip_mesaj'] = "success";
        header('Location: cititori.php');
        exit();
    } else {
        $mesaj = "Eroare la actualizare: " . $conn->error;
        $tip_mesaj = "danger";
    }
}

if (isset($_SESSION['mesaj'])) {
    $mesaj = $_SESSION['mesaj'];
    $tip_mesaj = $_SESSION['tip_mesaj'];
    unset($_SESSION['mesaj']);
    unset($_SESSION['tip_mesaj']);
}

$cititori = $conn->query("
    SELECT c.*, carti.Titlu as TitluCarte 
    FROM cititori c 
    LEFT JOIN carti ON c.CodCarte = carti.CodCarte 
    ORDER BY c.Nume, c.Prenume
");
$carti_disponibile = $conn->query("SELECT * FROM carti ORDER BY Titlu");
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestiune Cititori - Biblioteca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Biblioteca</a>
            <ul class="nav-links">
                <li><a href="carti.php">Carti</a></li>
                <li><a href="cititori.php" class="active">Cititori</a></li>
                <li><a href="rapoarte.php">Rapoarte</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Gestiune Cititori</h2>
        
        <?php if ($mesaj): ?>
            <div class="alert alert-<?php echo $tip_mesaj; ?>" id="autoAlert">
                <?php echo $mesaj; ?>
                <div class="alert-progress"></div>
            </div>
        <?php endif; ?>

        <!-- Formular adăugare cititor -->
        <div class="card">
            <div class="card-header">
                <h3>Adauga cititor nou</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" class="form-control" name="nume" placeholder="Nume" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="prenume" placeholder="Prenume" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="adresa" placeholder="Adresa">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="telefon" placeholder="Telefon">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <select class="form-control" name="cod_carte">
                                <option value="">Selecteaza carte imprumuta</option>
                                <?php while($carte = $carti_disponibile->fetch_assoc()): ?>
                                    <option value="<?php echo $carte['CodCarte']; ?>">
                                        <?php echo htmlspecialchars($carte['Titlu']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data imprumut:</label>
                            <input type="date" class="form-control" name="data_imprumut">
                        </div>
                        <div class="form-group">
                            <label>Data returnare:</label>
                            <input type="date" class="form-control" name="data_returnare">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="adauga_cititor" class="btn btn-success">Adauga</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista cititorilor -->
        <div class="card">
            <div class="card-header">
                <h3>Lista cititorilor</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cod</th>
                            <th>Nume</th>
                            <th>Prenume</th>
                            <th>Adresa</th>
                            <th>Telefon</th>
                            <th>Carte imprumutata</th>
                            <th>Data imprumut</th>
                            <th>Data returnare</th>
                            <th>Actiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($cititor = $cititori->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $cititor['CodCititor']; ?></td>
                            <td><?php echo htmlspecialchars($cititor['Nume']); ?></td>
                            <td><?php echo htmlspecialchars($cititor['Prenume']); ?></td>
                            <td><?php echo htmlspecialchars($cititor['Adresa']); ?></td>
                            <td><?php echo htmlspecialchars($cititor['Telefon']); ?></td>
                            <td><?php echo htmlspecialchars($cititor['TitluCarte'] ?? '-'); ?></td>
                            <td><?php echo $cititor['DataImprumut'] ?? '-'; ?></td>
                            <td><?php echo $cititor['DataReturnare'] ?? '-'; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="openModal('modificaModal<?php echo $cititor['CodCititor']; ?>')">
                                    Modifică
                                </button>
                                <a href="cititori.php?sterge_cititor=<?php echo $cititor['CodCititor']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Sigur doriți să ștergeți acest cititor?')">
                                    Șterge
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modaluri pentru modificare -->
    <?php
    $cititori->data_seek(0);
    while($cititor = $cititori->fetch_assoc()): 
    ?>
    <div class="modal" id="modificaModal<?php echo $cititor['CodCititor']; ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Modifica cititor</h3>
                <button class="close" onclick="closeModal('modificaModal<?php echo $cititor['CodCititor']; ?>')">&times;</button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="cod_cititor" value="<?php echo $cititor['CodCititor']; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nume:</label>
                            <input type="text" class="form-control" name="nume" 
                                   value="<?php echo htmlspecialchars($cititor['Nume']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Prenume:</label>
                            <input type="text" class="form-control" name="prenume" 
                                   value="<?php echo htmlspecialchars($cititor['Prenume']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Adresa:</label>
                            <input type="text" class="form-control" name="adresa" 
                                   value="<?php echo htmlspecialchars($cititor['Adresa']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Telefon:</label>
                            <input type="text" class="form-control" name="telefon" 
                                   value="<?php echo htmlspecialchars($cititor['Telefon']); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Carte imprumutata:</label>
                            <select class="form-control" name="cod_carte">
                                <option value="">Selectează carte</option>
                                <?php 
                                $carti = $conn->query("SELECT * FROM carti ORDER BY Titlu");
                                while($carte = $carti->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $carte['CodCarte']; ?>" 
                                        <?php echo ($cititor['CodCarte'] == $carte['CodCarte']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($carte['Titlu']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data imprumut:</label>
                            <input type="date" class="form-control" name="data_imprumut" 
                                   value="<?php echo $cititor['DataImprumut']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Data returnare:</label>
                            <input type="date" class="form-control" name="data_returnare" 
                                   value="<?php echo $cititor['DataReturnare']; ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modificaModal<?php echo $cititor['CodCititor']; ?>')">Anuleaza</button>
                    <button type="submit" name="modifica_cititor" class="btn btn-primary">Salveaza</button>
                </div>
            </form>
        </div>
    </div>
    <?php endwhile; ?>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        window.onclick = function(event) {
            var modals = document.getElementsByClassName('modal');
            for (var i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = 'none';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var alert = document.getElementById('autoAlert');
            if (alert) {
                var duration = 5000; 
                
                if (alert.classList.contains('alert-success')) {
                    duration = 4000; 
                } else if (alert.classList.contains('alert-danger')) {
                    duration = 6000; 
                }
                
                setTimeout(function() {
                    alert.style.transition = 'all 0.3s ease';
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(-100%)';
                    alert.style.height = '0';
                    alert.style.padding = '0';
                    alert.style.margin = '0';
                    alert.style.border = 'none';
                    
                    setTimeout(function() {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 300);
                }, duration);
            }
        });
    </script>
</body>
</html>