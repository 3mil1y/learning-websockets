<?php
// Usuários de teste
$users = [
    'usuario1' => [
        'password' => 'senha1',
        'name' => 'Usuário 1'
    ],
    'usuario2' => [
        'password' => 'senha2',
        'name' => 'Usuário 2'
    ],
    'usuario3' => [
        'password' => 'senha3',
        'name' => 'Usuário 3'
    ]
];

// Função para verificar credenciais
function authenticate($username, $password) {
    global $users;
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        return [
            'success' => true,
            'user' => [
                'username' => $username,
                'name' => $users[$username]['name']
            ]
        ];
    }
    return ['success' => false];
}

// Função para criar usuário anônimo
function createAnonymousUser($nickname = null) {
    $randomId = 'anon_' . substr(md5(uniqid()), 0, 8);
    $name = $nickname ? $nickname : 'Anônimo ' . substr($randomId, 5, 3);
    
    return [
        'success' => true,
        'user' => [
            'username' => $randomId,
            'name' => $name
        ]
    ];
} 