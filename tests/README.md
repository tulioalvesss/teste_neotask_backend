# Documentação dos Testes da API

Este diretório contém os testes automatizados para a API Laravel da aplicação. Os testes foram organizados seguindo as boas práticas de Laravel e PHPUnit.

## Estrutura de Testes

- **Feature Tests**: Testes que simulam requisições HTTP e testam o comportamento completo de endpoints da API.
- **Unit Tests**: Testes que verificam o funcionamento de componentes específicos (modelos, etc).

## Testes Implementados

### Feature Tests

- **AuthTest**: Testa os endpoints de autenticação (login, registro, logout, etc).
- **SongTest**: Testa os endpoints relacionados às músicas.
- **SuggestionSongTest**: Testa os endpoints relacionados às sugestões de músicas.

### Unit Tests

- **UserTest**: Testa o modelo User e suas propriedades.
- **SongTest**: Testa o modelo Song e suas propriedades.
- **SuggestionSongTest**: Testa o modelo SuggestionSong e suas propriedades.

## Como Executar os Testes

Para executar todos os testes, use o comando:

```bash
cd backend
php artisan test
```

Para executar um grupo específico de testes, use:

```bash
# Testes unitários
php artisan test --testsuite=Unit

# Testes de feature
php artisan test --testsuite=Feature
```

Para executar um arquivo de teste específico:

```bash
php artisan test tests/Feature/AuthTest.php
```

## Configuração do Ambiente de Testes

Os testes utilizam:
- SQLite em memória para banco de dados (`:memory:`)
- Factories para criar dados de teste
- Mock HTTP para simular requisições externas

## Cobertura de Testes

Os testes cobrem as seguintes funcionalidades:

1. **Autenticação**:
   - Registro de usuários
   - Login (com JWT)
   - Atualização de token
   - Logout
   - Acesso aos dados do usuário autenticado

2. **Músicas**:
   - Listagem de músicas
   - Atualização de músicas (apenas para admin)
   - Exclusão de músicas (apenas para admin)

3. **Sugestões de Músicas**:
   - Envio de sugestões de músicas
   - Listagem de sugestões (apenas para admin)
   - Aprovação de sugestões (apenas para admin)
   - Rejeição de sugestões (apenas para admin)

4. **Permissões e Autorização**:
   - Verificação de papéis (admin vs usuário comum)
   - Proteção de rotas administrativas 