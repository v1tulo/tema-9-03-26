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
    $sql = "SELECT * FROM cadastro";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo "<table>";
    echo "<tr><th>ID</th><th>Nome</th><th>Celular</th><th>Idade</th><th>Gênero</th><th>Ações</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['nome']}</td>";
        echo "<td>{$row['celular']}</td>";
        echo "<td>{$row['idade']}</td>";
        echo "<td>{$row['genero']}</td>";
        echo "<td>";
        echo "<button style='margin-right: 5px;' onclick='openModal(\"{$row['id']}\", \"{$row['nome']}\", \"{$row['celular']}\", \"{$row['idade']}\", \"{$row['genero']}\")'>Editar</button>";
        echo "<form method='post' action='index.php'>";
        echo "<input type='hidden' name='delete_id' value='{$row['id']}'>";
        echo "<button type='submit' name='delete'>Excluir</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function addCadastro($conn, $nome, $celular, $idade, $genero) {
    $sql = "INSERT INTO cadastro (nome, celular, idade, genero) VALUES (:nome, :celular, :idade, :genero)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':celular', $celular);
    $stmt->bindParam(':idade', $idade);
    $stmt->bindParam(':genero', $genero);
    $stmt->execute();
}

function updateCadastro($conn, $id, $nome, $celular, $idade, $genero) {
    $sql = "UPDATE cadastro SET nome = :nome, celular = :celular, idade = :idade, genero = :genero WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':celular', $celular);
    $stmt->bindParam(':idade', $idade);
    $stmt->bindParam(':genero', $genero);
    $stmt->execute();
}

function deleteCadastro($conn, $id) {
    $sql = "DELETE FROM cadastro WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $nome = $_POST['nome'];
    $celular = $_POST['celular'];
    $idade = $_POST['idade'];
    $genero = $_POST['genero'];
    addCadastro($conn, $nome, $celular, $idade, $genero);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['cadastro_id'];
    $nome = $_POST['nome'];
    $celular = $_POST['celular'];
    $idade = $_POST['idade'];
    $genero = $_POST['genero'];
    updateCadastro($conn, $id, $nome, $celular, $idade, $genero);
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
            <input type="hidden" id="edit_cadastro_id" name="cadastro_id">
            <input type="text" id="edit_nome" name="nome" placeholder="Nome" required>
            <input type="text" id="edit_celular" name="celular" placeholder="Celular" required>
            <input type="number" id="edit_idade" name="idade" placeholder="Idade" required>
            <input type="text" id="edit_genero" name="genero" placeholder="Gênero" required>
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>
</div>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="text" name="nome" placeholder="Nome" required>
    <input type="text" name="celular" placeholder="Celular" required>
    <input type="number" name="idade" placeholder="Idade" required>
    <input type="text" name="genero" placeholder="Gênero" required>
    <button type="submit" name="add">Adicionar</button>
</form>

<script>
    function openModal(id, nome, celular, idade, genero) {
        document.getElementById('edit_cadastro_id').value = id;
        document.getElementById('edit_nome').value = nome;
        document.getElementById('edit_celular').value = celular;
        document.getElementById('edit_idade').value = idade;
        document.getElementById('edit_genero').value = genero;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
