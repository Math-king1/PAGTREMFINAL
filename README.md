# 🚆 PagTrem

> Sistema moderno para gerenciamento de operações ferroviárias, rotas e comunicação com passageiros.

O **PagTrem** é uma aplicação web completa desenvolvida para facilitar a administração de linhas de trem, controle de funcionários e divulgação de avisos importantes para os usuários. Com uma interface limpa e responsiva, o sistema oferece painéis distintos para administradores e passageiros.

---

## 🛠️ Tecnologias Utilizadas

O projeto foi construído utilizando tecnologias robustas e amplamente suportadas:

- **Backend**: PHP 8+ (Vanilla)
- **Banco de Dados**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3 (Customizado), JavaScript
- **Ícones**: RemixIcon
- **Servidor Web**: Apache (via XAMPP)

---

## ✨ Funcionalidades Principais

### 👨‍💼 Painel Administrativo
O administrador possui controle total sobre o sistema:
- **Dashboard**: Visão geral do sistema.
- **Gestão de Rotas**: Criação, edição e remoção de rotas, incluindo status (Ativa/Manutenção) e informações extras.
- **Gestão de Funcionários**: Cadastro completo de funcionários com upload de foto e criação automática de usuário de acesso.
- **Sistema de Avisos**: Publicação de notificações (Manutenção, Novidades, Sistema) que aparecem para os usuários.
- **Perfil**: Gerenciamento de dados da conta administrativa.

### 👤 Painel do Usuário
Área dedicada aos passageiros para consulta de informações:
- **Visualização de Rotas**: Acompanhamento de rotas disponíveis e seus status em tempo real.
- **Notificações**: Aba dedicada para receber avisos e comunicados oficiais do sistema.
- **Perfil**: Visualização e edição de dados pessoais.

---

## 🚀 Guia de Instalação e Configuração

Siga os passos abaixo para rodar o projeto em seu ambiente local:

### 1. Pré-requisitos
- Ter o **XAMPP** (ou ambiente similar AMP) instalado.
- Ter o **Git** instalado.

### 2. Clonagem e Diretório
Clone o repositório dentro da pasta `htdocs` do seu XAMPP:

```bash
cd c:\xampp\htdocs
git clone https://github.com/seu-usuario/pagtrem-help.git
```

### 3. Configuração do Banco de Dados
1. Inicie os serviços **Apache** e **MySQL** no painel do XAMPP.
2. Acesse o **PHPMyAdmin** (geralmente em `http://localhost/phpmyadmin`).
3. Crie um banco de dados vazio (o script já lida com a criação, mas é bom garantir).
4. Importe o arquivo SQL localizado em:
   `assets/config/db.sql`

### 4. Configuração de Conexão
Verifique se as credenciais no arquivo de conexão correspondem ao seu ambiente local:
Arquivo: `assets/config/db.php`

```php
$host = 'localhost';
$user = 'root';
$pass = ''; // Senha padrão do XAMPP é vazia
$db   = 'pagtrem';
```

### 5. Acessando o Projeto
Abra seu navegador e acesse:
[http://localhost/pagtrem-help/public/](http://localhost/pagtrem-help/public/)

---

## 🔐 Credenciais de Acesso

O banco de dados já vem populado com usuários de teste para facilitar o desenvolvimento:

| Perfil | E-mail | Senha |
| :--- | :--- | :--- |
| **Administrador** | `admin@pagtrem.com` | `1234` |
| **Usuário** | `usuario@pagtrem.com` | `1234` |

> **Nota**: As senhas são criptografadas no banco de dados.

---

## 📂 Estrutura de Pastas

```
pagtrem-help/
├── assets/
│   ├── config/      # Configurações de DB e Auth
│   ├── css/         # Estilos globais
│   └── uploads/     # Imagens de perfil e funcionários
├── public/          # Arquivos acessíveis via navegador
│   ├── _partials/   # Componentes reutilizáveis (sidebars)
│   ├── *.php        # Páginas do sistema
└── README.md        # Documentação do projeto
```

---

Desenvolvido com 💙 para modernizar o transporte ferroviário.
