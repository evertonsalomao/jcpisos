<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Hash de Senha</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(0);
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        .result h2 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .hash-output {
            background: white;
            padding: 12px;
            border-radius: 4px;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #333;
            border: 1px solid #e0e0e0;
            margin-bottom: 15px;
        }
        .copy-btn {
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            width: auto;
        }
        .copy-btn:hover {
            background: #218838;
        }
        .sql-query {
            background: #2d3748;
            color: #a0aec0;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin-top: 15px;
            overflow-x: auto;
        }
        .sql-query .keyword {
            color: #fc8181;
        }
        .sql-query .string {
            color: #68d391;
        }
        .info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 14px;
            color: #1976d2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Gerador de Hash de Senha</h1>
        <p class="subtitle">Gere um hash seguro para sua senha</p>

        <form method="POST">
            <div class="form-group">
                <label for="senha">Digite a senha:</label>
                <input type="password" id="senha" name="senha" required placeholder="Digite sua senha aqui">
            </div>

            <div class="form-group">
                <label for="usuario">Usuário (opcional):</label>
                <input type="text" id="usuario" name="usuario" placeholder="Ex: adm_qube" value="adm_qube">
            </div>

            <button type="submit">Gerar Hash</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $senha = $_POST['senha'] ?? '';
            $usuario = $_POST['usuario'] ?? 'adm_qube';

            if (!empty($senha)) {
                $hash = password_hash($senha, PASSWORD_DEFAULT);

                echo '<div class="result">';
                echo '<h2>✅ Hash Gerado com Sucesso!</h2>';
                echo '<div class="hash-output" id="hashOutput">' . htmlspecialchars($hash) . '</div>';
                echo '<button class="copy-btn" onclick="copiarHash()">📋 Copiar Hash</button>';

                echo '<div class="sql-query">';
                echo '<span class="keyword">UPDATE</span> qube_users<br>';
                echo '<span class="keyword">SET</span> password = <span class="string">\'' . htmlspecialchars($hash) . '\'</span><br>';
                echo '<span class="keyword">WHERE</span> username = <span class="string">\'' . htmlspecialchars($usuario) . '\'</span>;';
                echo '</div>';

                echo '<div class="info">';
                echo '💡 <strong>Senha testada:</strong> ' . htmlspecialchars($senha) . '<br>';
                echo '👤 <strong>Usuário:</strong> ' . htmlspecialchars($usuario);
                echo '</div>';

                echo '</div>';
            }
        }
        ?>
    </div>

    <script>
    function copiarHash() {
        const hashText = document.getElementById('hashOutput').textContent;
        navigator.clipboard.writeText(hashText).then(function() {
            alert('Hash copiado para a área de transferência!');
        }, function(err) {
            console.error('Erro ao copiar: ', err);
        });
    }
    </script>
</body>
</html>
