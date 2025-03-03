
# Sistema de Pedidos - API RESTful

API para gestão de clientes, produtos e pedidos com CodeIgniter 4

## 📥 Download
[Clique aqui para baixar o projeto (.zip)](https://github.com/Natangaf/API_CodeIgniter/archive/refs/heads/main.zip)

## 🚀 Configuração Inicial

### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7+
- Composer
- Git (opcional)

### Instalação
```bash
# Clone o repositório
git clone https://github.com/Natangaf/API_CodeIgniter.git

# Acesse a pasta do projeto
cd API_CodeIgniter

# Instale as dependências
composer install
```

### ⚙️ Configuração do Ambiente
Renomeie o arquivo .env.example para .env

Configure o arquivo .env:

```ini
# Database
database.default.hostname = localhost
database.default.database = api_pedidos
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi

# App
app.baseURL = 'http://localhost:8080/'
```

### 🗃️ Configuração do Banco de Dados
Crie o banco de dados:

```sql
CREATE DATABASE api_pedidos;
```

Execute as migrations:

```bash
php spark migrate
```

(Opcional) Popule com dados de teste:

```bash
php spark db:seed ClientSeeder
php spark db:seed ProdutoSeeder
```

### 🌐 Endpoints da API
#### Clientes
- `GET /clientes` - Lista todos
- `POST /clientes` - Cria novo

```json
{
  "documento": "123456789",
  "nome": "Cliente Exemplo",
  "tipo": "PF"
}
```

#### Produtos
- `GET /produtos` - Lista todos
- `POST /produtos` - Cria novo

```json
{
  "nome": "Produto Teste",
  "descricao": "Descrição exemplo",
  "preco": 99.90,
  "estoque": 10
}
```

#### Pedidos
- `POST /pedidos` - Cria novo pedido

```json
{
  "cliente_id": 1,
  "produto_id": 3,
  "quantidade": 2,
  "status": "Em Aberto"
}
```

### 🛠️ Estrutura do Projeto
```
api-pedidos/
├── app/
│   ├── Config/       # Configurações
│   ├── Controllers/  # Controladores
│   ├── Models/       # Modelos
│   └── Database/     # Migrations e Seeds
├── public/           # Pasta pública
└── writable/         # Logs e cache
```

### 🚨 Troubleshooting
#### Erro nas Migrations
```bash
# Limpe o cache
php spark cache:clear

# Force recriação das tabelas
php spark migrate:refresh --all
```

#### Problemas com Foreign Keys
- Verifique se as tabelas clientes e produtos existem
- Confira se os IDs são UNSIGNED no banco

### 📄 Licença
MIT License - Detalhes da licença

✅ Pronto para usar! Acesse via Postman ou Insomnia usando http://localhost:8080/
