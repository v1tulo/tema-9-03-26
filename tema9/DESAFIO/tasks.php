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

function displayCadastros($conn) {
    $sql = "SELECT * FROM estoque";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo "<table>";
    echo "<tr><th>ID</th><th>nome produto</th><th>quantidade min</th><th>quantidade atual</th><th>Ações</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome_produto']}</td>";
        echo "<td>{$row['quantidade_min']}</td>";
        echo "<td>{$row['quantidade_atual']}</td>";
        echo "<td>";
        echo "<button style='margin-right: 5px;' onclick='openModal(\"{$row['id']}\", \"{$row['nome_produto']}\", \"{$row['quantidade_min']}\", \"{$row['quantidade_atual']}\")'>Editar</button>";
        echo "<form method='post' action='index.php'>";
        echo "<input type='hidden' name='delete_id' value='{$row['id']}'>";
        echo "<button type='submit' name='delete'>Excluir</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function addCadastro($conn, $nomeProd, $quantiMin, $quantAual) {
    $sql = "INSERT INTO estoque (nome_produto, quantidade_min, quantidade_atual) VALUES (:nomePro, :quantMin, :quantAtua)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nomePro', $nomeProd);
    $stmt->bindParam(':quantMin', $quantiMin);
    $stmt->bindParam(':quantAtua', $quantAual);
    $stmt->execute();
}

function updateCadastro($conn, $id, $nomeProd, $quantiMin, $quantAual) {
    $sql = "UPDATE estoque SET nome_produto = :nomePro, quantidade_min = :quantMin, quantidade_atual = :quantAtua WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nomePro', $nomeProd);
    $stmt->bindParam(':quantMin', $quantiMin);
    $stmt->bindParam(':quantAtua', $quantAual);
    $stmt->execute();
}

function deleteCadastro($conn, $id) {
    $sql = "DELETE FROM estoque WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $nomeProd = $_POST['nomeProduto'];
    $quantMin = $_POST['quantiMin'];
    $quantAual = $_POST['quantiAtual'];
    addCadastro($conn, $nomeProd, $quantMin, $quantAual);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nomeProd = $_POST['nomeDoProduto'];
    $quantMin = $_POST['quantidadeMin'];
    $quantAual = $_POST['quantidadeAtual'];
    updateCadastro($conn, $id, $nomeProd, $quantMin, $quantAual);
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id = $_POST['delete_id'];
    deleteCadastro($conn, $id);
    header("Location: index.php");
}

displayCadastros($conn);

$conn = null;
?>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Editar Cadastro</h3>
        <form method="post" action="index.php">
            <input type="hidden" id="id" name="id">
            <input type="text" id="nomeDoProduto" name="nomeDoProduto" placeholder="nomeDoProduto" required>
            <input type="number" id="quantidadeMin" name="quantidadeMin" placeholder="quantidadeMin" required>
            <input type="number" id="quantidadeAtual" name="quantidadeAtual" placeholder="quantidadeAtual" required>
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>
</div>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="text" name="nomeProduto" placeholder="nomeDoProduto" required>
    <input type="number" name="quantiMin" placeholder="quantidadeMin" required>
    <input type="number" name="quantiAtual" placeholder="quantidadeAtual" required>
    <button type="submit" name="add">Adicionar</button>
</form>

<script>
    function openModal(id, nomeProd, quantMin, quantAual) {
        document.getElementById('id').value = id;
        document.getElementById('nomeDoProduto').value = nomeProd;
        document.getElementById('quantidadeMin').value = quantMin;
        document.getElementById('quantidadeAtual').value = quantAual;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>