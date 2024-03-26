# PHP_TEMA9
Conexão com Banco de Dados

DESAFIO:

Levando em consideração a seguinte conexão origem:

$servername = "localhost";
$port = 7306;
$username = "root";
$password = "";
$dbname = "banco_de_dados";

Desenvolva uma aplicação para CRUD na seguinte tabela:

CREATE TABLE IF NOT EXISTS estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_produto VARCHAR(255) NOT NULL,
    quantidade_min INT NOT NULL,
    quantidade_atual INT NOT NULL
);

Receber atividade

https://almeida-cma.github.io/receber/
