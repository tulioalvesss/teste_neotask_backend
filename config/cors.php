<?php

return [
    'paths' => ['api/*'], // Permite apenas rotas da API
    'allowed_methods' => ['*'], // Permite todos os métodos HTTP
    'allowed_origins' => ['http://localhost:5173'], // URLs do frontend
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Permite todos os cabeçalhos
    'exposed_headers' => [],
    'max_age' => 0, // Sem cache
    'supports_credentials' => true, // Importante para autenticação
];
