<?php
session_start();

$error = '';
$success = '';
$step = $_GET['step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $step == 1) {
    $host = $_POST['host'] ?? 'localhost';
    $dbname = $_POST['dbname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($host) && !empty($dbname) && !empty($username)) {
        try {
            $dsn = "mysql:host=" . $host . ";dbname=" . $dbname . ";charset=utf8mb4";
            $conn = new PDO($dsn, $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlFile = __DIR__ . '/database.sql';
            $sql = file_get_contents($sqlFile);

            $statements = preg_split('/;\s*$/m', $sql);

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    try {
                        $conn->exec($statement);
                    } catch (PDOException $e) {
                    }
                }
            }

            function generateUUID() {
                return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                    mt_rand(0, 0xffff),
                    mt_rand(0, 0x0fff) | 0x4000,
                    mt_rand(0, 0x3fff) | 0x8000,
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                );
            }

            $checkUser = $conn->query("SELECT COUNT(*) FROM qube_users WHERE username = 'adm_qube'")->fetchColumn();
            if ($checkUser == 0) {
                $userId = generateUUID();
                $stmt = $conn->prepare("INSERT INTO qube_users (id, username, password, name, email) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $userId,
                    'adm_qube',
                    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                    'Administrador',
                    'admin@qube.com'
                ]);
            }

            $checkCategories = $conn->query("SELECT COUNT(*) FROM qube_categories")->fetchColumn();
            if ($checkCategories == 0) {
                $categories = [
                    ['name' => 'Residencial/Condomínios', 'slug' => 'first', 'order' => 1],
                    ['name' => 'Comercial', 'slug' => 'second', 'order' => 2],
                    ['name' => 'Industrial', 'slug' => 'third', 'order' => 3]
                ];

                $stmt = $conn->prepare("INSERT INTO qube_categories (id, name, slug, order_index) VALUES (?, ?, ?, ?)");
                foreach ($categories as $cat) {
                    $stmt->execute([
                        generateUUID(),
                        $cat['name'],
                        $cat['slug'],
                        $cat['order']
                    ]);
                }
            }

            $configContent = "<?php

class Database {
    private \$host = \"" . $host . "\";
    private \$db_name = \"" . $dbname . "\";
    private \$username = \"" . $username . "\";
    private \$password = \"" . addslashes($password) . "\";
    private \$charset = \"utf8mb4\";
    public \$conn;

    public function getConnection() {
        \$this->conn = null;

        try {
            \$dsn = \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name . \";charset=\" . \$this->charset;
            \$this->conn = new PDO(\$dsn, \$this->username, \$this->password);
            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            \$this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException \$exception) {
            echo \"Erro de conexão: \" . \$exception->getMessage();
        }

        return \$this->conn;
    }
}";

            file_put_contents(__DIR__ . '/config/database.php', $configContent);

            header('Location: install.php?step=2');
            exit();

        } catch(PDOException $e) {
            $error = 'Erro na conexão ou instalação: ' . $e->getMessage();
        }
    } else {
        $error = 'Preencha todos os campos obrigatórios';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Qube Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h2 {
            color: #667eea;
            font-weight: bold;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }
        .step.active {
            background: #667eea;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-card">
            <div class="logo">
                <h2>🎨 Qube Manager</h2>
                <p class="text-muted">Instalação do Sistema</p>
            </div>

            <div class="step-indicator">
                <div class="step <?php echo $step >= 1 ? 'active' : ''; ?>">1</div>
                <div class="step <?php echo $step >= 2 ? 'active' : ''; ?>">2</div>
            </div>

            <?php if ($step == 1): ?>
                <h4 class="mb-4">Passo 1: Configuração do Banco de Dados</h4>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Host do MySQL</label>
                        <input type="text" class="form-control" name="host" value="localhost" required>
                        <small class="text-muted">Geralmente é "localhost"</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nome do Banco de Dados</label>
                        <input type="text" class="form-control" name="dbname" required>
                        <small class="text-muted">O banco de dados deve já existir</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Usuário MySQL</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Senha MySQL</label>
                        <input type="password" class="form-control" name="password">
                        <small class="text-muted">Deixe em branco se não houver senha</small>
                    </div>

                    <div class="alert alert-info">
                        <strong>Importante:</strong> O banco de dados deve já estar criado no MySQL. Este instalador criará apenas as tabelas.
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Continuar</button>
                </form>

            <?php elseif ($step == 2): ?>
                <div class="text-center">
                    <div class="alert alert-success">
                        <h4>✓ Instalação Concluída!</h4>
                        <p>O sistema foi instalado com sucesso.</p>
                    </div>

                    <h5 class="mb-3">Credenciais de Acesso</h5>
                    <div class="alert alert-light">
                        <strong>Usuário:</strong> adm_qube<br>
                        <strong>Senha:</strong> jj401rbz.
                    </div>

                    <div class="alert alert-warning">
                        <strong>Importante:</strong> Altere a senha após o primeiro login!
                    </div>

                    <h5 class="mb-3">O que foi criado:</h5>
                    <ul class="list-group mb-4 text-start">
                        <li class="list-group-item">✓ Tabelas do banco de dados</li>
                        <li class="list-group-item">✓ Usuário administrador</li>
                        <li class="list-group-item">✓ Categorias padrão (Residencial, Comercial, Industrial)</li>
                        <li class="list-group-item">✓ Arquivo de configuração</li>
                    </ul>

                    <a href="login.php" class="btn btn-primary btn-lg w-100">Acessar o Painel</a>

                    <div class="mt-3">
                        <small class="text-muted">
                            Por segurança, você pode deletar o arquivo <code>install.php</code> após a instalação.
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
