<h1 align="center">🚆 Projeto SA – Vai de Trem</h1>
<p align="center">
  <strong>Aplicativo de gerenciamento de rodovias com integração à API ViaCEP</strong>
</p>

---

## 👥 Integrantes

- **Jaison Conaco Junior**  
- **João Guilherme Duarte**  
- **Eduardo Ducci**

---

## 🏢 Empresa Fictícia

**Vai de Trem** – Sistema voltado ao gerenciamento de rodovias, trechos e funcionários, facilitando o controle e organização das informações.

---

## 📚 Sobre o Projeto

Este repositório faz parte da **Situação de Aprendizagem (SA)** da matéria de **Desenvolvimento de Sistemas**.

Após a conclusão dos mockups, desenvolvemos a primeira versão funcional do sistema **Vai de Trem**, focado em:

- Cadastro e gerenciamento de **rodovias e trechos**;
- Organização de **funcionários** responsáveis;
- Automatização de **endereços por CEP** utilizando a **API ViaCEP**.

Atualmente, o projeto já conta com **back-end em PHP** integrado ao **MySQL** via **MySQLi**, além de uma interface construída com **HTML, CSS e JavaScript**.

---

## 🛠️ Tecnologias Utilizadas

| Camada         | Tecnologia                         |
|----------------|------------------------------------|
| Back-end       | PHP                                |
| Banco de Dados | MySQL / MySQLi                    |
| Front-end      | HTML5, CSS3, JavaScript           |
| Integração     | API ViaCEP                        |

---

## ✅ Funcionalidades Principais

- 📌 Cadastro de rodovias e trechos;  
- 🧑‍💼 Cadastro de funcionários ligados à gestão das rodovias;  
- 🔎 Consulta automática de endereço via **CEP** (API ViaCEP);  
- 📄 Visualização de informações cadastradas;  
- ✏️ Edição de registros (rodovias, trechos, funcionários);  
- 🗑️ Exclusão de registros;  
- 🧭 Navegação simples e organizada entre as telas.

## 🗄️ Configuração do Banco de Dados

Antes de usar o sistema, configure o arquivo `db.php` com os dados do seu servidor:

```php
<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'root';
$DB_NAME = 'vaidetrem2';   
