<?php
$servername = "localhost";
$port = 7306;
$username = "root";
$password = "";
$dbname = "banco_de_dados";

try {
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erro na conexão com o banco de dados: " . $e->getMessage();
}

function displayLeads($conn) {
    $sql = "SELECT * FROM leads";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo "<table>";
    echo "<tr><th>ID</th><th>Nome</th><th>Celular</th><th>Ações</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>{$row['celular']}</td>";
        echo "<td>
                <button onclick='openModal(\"{$row['id']}\", \"{$row['nome']}\", \"{$row['celular']}\")'>Editar</button>
                <a href='index.php?action=delete&id={$row['id']}'>Excluir</a>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
}

function addLead($conn, $nome, $celular) {
    $sql = "INSERT INTO leads (nome, celular) VALUES (:nome, :celular)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':celular', $celular);
    $stmt->execute();
}

function updateLead($conn, $id, $nome, $celular) {
    $sql = "UPDATE leads SET nome = :nome, celular = :celular WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':celular', $celular);
    $stmt->execute();
}

function deleteLead($conn, $id) {
    $sql = "DELETE FROM leads WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $nome = $_POST['nome'];
    $celular = $_POST['celular'];
    addLead($conn, $nome, $celular);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['lead_id'];
    $nome = $_POST['nome'];
    $celular = $_POST['celular'];
    updateLead($conn, $id, $nome, $celular);
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    deleteLead($conn, $id);
}

displayLeads($conn);

$conn = null;
?>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Editar Lead</h3>
        <form method="post" action="index.php">
            <input type="hidden" id="edit_lead_id" name="lead_id">
            <input type="text" id="edit_nome" name="nome" placeholder="Nome" required>
            <input type="text" id="edit_celular" name="celular" placeholder="Celular" required>
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>
</div>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="text" name="nome" placeholder="Nome" required>
    <input type="text" name="celular" placeholder="Celular" required>
    <button type="submit" name="add">Adicionar</button>
</form>

<script>
    function openModal(id, nome, celular) {
        document.getElementById('edit_lead_id').value = id;
        document.getElementById('edit_nome').value = nome;
        document.getElementById('edit_celular').value = celular;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
