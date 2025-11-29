<?php
session_start();
include 'conexiune.php';

$mesaj = '';
$tip_mesaj = 'info';

// Adaugare carte
if (isset($_POST['adauga_carte'])) {
    $titlu = $_POST['titlu'];
    $autor = $_POST['autor'];
    $editura = $_POST['editura'];
    $pret = $_POST['pret'];

    $sql = "INSERT INTO carti (Titlu, Autor, Editura, Pret) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssd", $titlu, $autor, $editura, $pret);
    
    if ($stmt->execute()) {
        $_SESSION['mesaj'] = "Cartea a fost adăugată cu succes!";
        $_SESSION['tip_mesaj'] = "success";
        header('Location: carti.php');
        exit();
    } else {
        $mesaj = "Eroare la adăugare: " . $conn->error;
        $tip_mesaj = "danger";
    }
}

// stergere carte
if (isset($_GET['sterge_carte'])) {
    $cod = $_GET['sterge_carte'];
    $sql = "DELETE FROM carti WHERE CodCarte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cod);
    
    if ($stmt->execute()) {
        $_SESSION['mesaj'] = "Cartea a fost ștearsă cu succes!";
        $_SESSION['tip_mesaj'] = "success";
        header('Location: carti.php');
        exit();
    } else {
        $mesaj = "Eroare la ștergere: " . $conn->error;
        $tip_mesaj = "danger";
    }
}

// Modificare carte
if (isset($_POST['modifica_carte'])) {
    $cod = $_POST['cod_carte'];
    $titlu = $_POST['titlu'];
    $autor = $_POST['autor'];
    $editura = $_POST['editura'];
    $pret = $_POST['pret'];

    $sql = "UPDATE carti SET Titlu=?, Autor=?, Editura=?, Pret=? WHERE CodCarte=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdi", $titlu, $autor, $editura, $pret, $cod);
    
    if ($stmt->execute()) {
        $_SESSION['mesaj'] = "Cartea a fost actualizată cu succes!";
        $_SESSION['tip_mesaj'] = "success";
        header('Location: carti.php');
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

$carti = $conn->query("SELECT * FROM carti ORDER BY Titlu");
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestiune Carti - Biblioteca</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php" class="logo">Biblioteca</a>
            <ul class="nav-links">
                <li><a href="carti.php" class="active">Carti</a></li>
                <li><a href="cititori.php">Cititori</a></li>
                <li><a href="rapoarte.php">Rapoarte</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Gestiune Carti</h2>
        
        <?php if ($mesaj): ?>
            <div class="alert alert-<?php echo $tip_mesaj; ?>" id="autoAlert">
                <?php echo $mesaj; ?>
                <div class="alert-progress"></div>
            </div>
        <?php endif; ?>

        <!-- Formular -->
        <div class="card">
            <div class="card-header">
                <h3>Adauga carte noua</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" class="form-control" name="titlu" placeholder="Titlu" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="autor" placeholder="Autor" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="editura" placeholder="Editura" required>
                        </div>
                        <div class="form-group">
                            <input type="number" step="0.01" class="form-control" name="pret" placeholder="Preț" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="adauga_carte" class="btn btn-success">Adauga</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Lista cartilor</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cod</th>
                            <th>Titlu</th>
                            <th>Autor</th>
                            <th>Editura</th>
                            <th>Pret</th>
                            <th>Actiuni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($carte = $carti->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $carte['CodCarte']; ?></td>
                            <td><?php echo htmlspecialchars($carte['Titlu']); ?></td>
                            <td><?php echo htmlspecialchars($carte['Autor']); ?></td>
                            <td><?php echo htmlspecialchars($carte['Editura']); ?></td>
                            <td><?php echo $carte['Pret']; ?> RON</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="openModal('modificaModal<?php echo $carte['CodCarte']; ?>')">
                                    Modifica
                                </button>
                                <a href="carti.php?sterge_carte=<?php echo $carte['CodCarte']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Sigur doriți să ștergeți această carte?')">
                                    Sterge
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
    $carti->data_seek(0); 
    while($carte = $carti->fetch_assoc()): 
    ?>
    <div class="modal" id="modificaModal<?php echo $carte['CodCarte']; ?>">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Modifica carte</h3>
                <button class="close" onclick="closeModal('modificaModal<?php echo $carte['CodCarte']; ?>')">&times;</button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="cod_carte" value="<?php echo $carte['CodCarte']; ?>">
                    <div class="form-group">
                        <label>Titlu:</label>
                        <input type="text" class="form-control" name="titlu" 
                               value="<?php echo htmlspecialchars($carte['Titlu']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Autor:</label>
                        <input type="text" class="form-control" name="autor" 
                               value="<?php echo htmlspecialchars($carte['Autor']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Editura:</label>
                        <input type="text" class="form-control" name="editura" 
                               value="<?php echo htmlspecialchars($carte['Editura']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Pret:</label>
                        <input type="number" step="0.01" class="form-control" name="pret" 
                               value="<?php echo $carte['Pret']; ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modificaModal<?php echo $carte['CodCarte']; ?>')">Anuleaza</button>
                    <button type="submit" name="modifica_carte" class="btn btn-primary">Salveaza</button>
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