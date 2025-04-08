# Sistema de Cadastro de Usuários com Laravel e Vue.js

## 📄 Sobre o Projeto
Este projeto consiste em um sistema de cadastro de usuários utilizando duas aplicações distintas:
- **Back-end:** Laravel (PHP)
- **Front-end:** Vue.js com Typescript
- **Banco de Dados:** MySQL
- **Gerenciamento de Estado:** Pinia
- **Estilização:** Sass
- **Containerização:** Docker

## 🔄 Fluxo da Aplicação
1. **Tela Inicial** com botão para login via Google (OAuth).
2. Redirecionamento para autenticação do Google.
3. Callback da autenticação retorna para a API Laravel.
4. A API salva o token do Google e redireciona para a tela de **complemento de cadastro**.
5. O usuário informa **nome, data de nascimento e CPF**, completando o cadastro.
6. Um **e-mail de confirmação** é enviado utilizando fila assíncrona.
7. Após isso, é exibida uma **lista com todos os usuários cadastrados**, com filtro por **nome** e **CPF**.

## ⚙️ Tecnologias Utilizadas

### Back-End (Laravel)
- Laravel 10+
- MySQL
- PHP 8.2+
- Filas com database
- Google OAuth (biblioteca oficial: [`google-api-php-client`](https://github.com/googleapis/google-api-php-client))
- Padrões:
  - Service
  - Repository
  - Migrations, Seeders, Factories

### Front-End (Vue.js)
- Vue 3 + Typescript
- Vue Router
- Pinia
- Sass (SCSS)
- Vitest (testes)

### Outros
- Docker (ambiente de desenvolvimento completo com Laravel, MySQL e Front-end)

## 📁 Instalação do Projeto

### Requisitos
- Docker e Docker Compose instalados

### Passos

```bash
git clone https://github.com/matheusasp/User-Registration-Tray.git
cd user-registration-tray
docker-compose up -d
```

### Back-End
```bash
docker exec -it laravel_app php artisan migrate --seed
docker exec -it laravel_app php artisan serve --host=0.0.0.0 --port=8000
```

### Front-End
```bash
docker exec -it vue_app npm install
docker exec -it vue_app npm run dev
```

### Acesso
- Front-end: `http://localhost:8080`
- API: `http://localhost:8000`

## 🚀 Funcionalidades
- Login com conta Google (OAuth)
- Cadastro complementar de dados (nome, data nascimento, CPF)
- Listagem e filtro de usuários
- Envio automático de e-mail após cadastro
- Filtro otimizado para grandes volumes de dados

## ✉️ Envio de E-mail
O envio de e-mail é feito com base no token do Google salvo. O processo é assíncrono via fila e utiliza a biblioteca oficial do Google.  
Necessário configurar no `.env` as variáveis relacionadas ao e-mail e ao Google.

## 🔧 Testes

### Back-end (Laravel)
Os testes unitários estão localizados em `tests/Feature` e `tests/Unit`:
```bash
docker exec -it app php artisan test
```

### Front-end (Vitest)
```bash
docker exec -it frontend npm run test
```

## 🖊️ Observações e Decisões Técnicas
- Toda autenticação é tratada via back-end para segurança e controle de tokens
- Utilização de filas para evitar bloqueio de requisições no envio de e-mail
- Banco de dados com indexes em `name` e `cpf` para melhor performance nos filtros
- Front-end responsivo e com foco em usabilidade

---

## 🛠️ Problemas com Docker?

Caso você enfrente algum problema ao rodar o projeto com Docker, é possível executar o sistema localmente:

### Back-End (Laravel)
1. Instale as dependências com o Composer:
   ```bash
   composer install
   ```
2. Copie o `.env.example` para `.env` e configure as variáveis de ambiente.
3. Gere a key do Laravel:
   ```bash
   php artisan key:generate
   ```
4. Execute as migrations e seeders:
   ```bash
   php artisan migrate --seed
   ```
5. Inicie o servidor:
   ```bash
   php artisan serve
   ```

### Front-End (Vue.js)
1. Instale as dependências:
   ```bash
   npm install
   ```
2. Inicie a aplicação:
   ```bash
   npm run dev
   ```

Certifique-se de que o back-end esteja rodando na mesma porta configurada no front para o consumo da API.
