<?php
// Conexão com o banco de dados
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

// Função para exibir tarefas
function displayTasks($conn) {
    $sql = "SELECT * FROM tasks";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    echo "<table>";
    echo "<tr><th>Tarefa</th><th>Ações</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['description']}</td>";
        echo "<td>
                <button onclick='openModal(\"{$row['id']}\", \"{$row['description']}\")'>Editar</button>
                <a href='index.php?action=delete&id={$row['id']}'>Excluir</a>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Função para adicionar tarefa
function addTask($conn, $description) {
    $sql = "INSERT INTO tasks (description) VALUES (:description)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
}

// Função para atualizar tarefa
function updateTask($conn, $id, $description) {
    $sql = "UPDATE tasks SET description = :description WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':description', $description);
    $stmt->execute();
}

// Função para excluir tarefa
function deleteTask($conn, $id) {
    $sql = "DELETE FROM tasks WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
}

// Adicionar tarefa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $description = $_POST['description'];
    addTask($conn, $description);
}

// Atualizar tarefa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['task_id'];
    $description = $_POST['description'];
    updateTask($conn, $id, $description);
    header("Location: index.php"); // Redirecionar para a página principal após a atualização
}

// Excluir tarefa
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    deleteTask($conn, $id);
}

// Exibir tarefas
displayTasks($conn);

$conn = null; // Fechar conexão
?>

<!-- Modal para edição de tarefa -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Editar Tarefa</h3>
        <form method="post" action="index.php">
            <input type="hidden" id="edit_task_id" name="task_id">
            <input type="text" id="edit_description" name="description" required>
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>
</div>

<!-- Formulário para adicionar tarefa -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="text" name="description" placeholder="Adicionar tarefa" required>
    <button type="submit" name="add">Adicionar</button>
</form>

<script>
    // Função para abrir a modal de edição com os dados da tarefa selecionada
    function openModal(id, description) {
        document.getElementById('edit_task_id').value = id;
        document.getElementById('edit_description').value = description;
        document.getElementById('editModal').style.display = 'block';
    }

    // Função para fechar a modal de edição
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>