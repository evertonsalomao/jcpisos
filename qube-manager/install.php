<?php
require_once 'config.php';

$hashedPassword = password_hash('jj401rbz.', PASSWORD_DEFAULT);

$users = $supabase->select('cms_users', 'username=eq.adm_qube');

if (!$users || count($users) === 0) {
    $data = [
        'username' => 'adm_qube',
        'password' => $hashedPassword,
        'email' => 'admin@qube.com'
    ];

    $result = $supabase->insert('cms_users', $data);

    if ($result) {
        echo "Usuário inicial criado com sucesso!<br>";
    } else {
        echo "Erro ao criar usuário inicial<br>";
    }
} else {
    echo "Usuário inicial já existe<br>";
}

$categories = $supabase->select('cms_categories');

if (!$categories || count($categories) === 0) {
    $defaultCategories = [
        ['name' => 'Residencial/Condomínios', 'slug' => 'residencial-condominios'],
        ['name' => 'Comercial', 'slug' => 'comercial'],
        ['name' => 'Industrial', 'slug' => 'industrial']
    ];

    foreach ($defaultCategories as $category) {
        $result = $supabase->insert('cms_categories', $category);
        if ($result) {
            echo "Categoria '{$category['name']}' criada com sucesso!<br>";
        }
    }
} else {
    echo "Categorias já existem<br>";
}

echo "<br><br>Instalação concluída! <a href='index.php'>Ir para o login</a>";
