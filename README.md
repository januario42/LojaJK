<div align="center">

# 🛒 LojaJK — E-commerce Completo em PHP

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Mercado Pago](https://img.shields.io/badge/Mercado%20Pago-Integrado-00B1EA?style=flat-square)](https://mercadopago.com.br)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)

**Sua loja virtual completa com pagamentos reais integrados**

*"Código simples, vendas reais, resultados extraordinários."*

[🚀 Demo Online](#) • [📖 Documentação](#documentação) • [🐛 Reportar Bug](https://github.com/januario42/LojaJK/issues) • [💡 Solicitar Feature](https://github.com/januario42/LojaJK/issues)

</div>

---

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias](#-tecnologias)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Uso](#-uso)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [API](#-api)
- [Segurança](#-segurança)
- [Contribuição](#-contribuição)
- [Roadmap](#-roadmap)
- [FAQ](#-faq)
- [Licença](#-licença)
- [Contato](#-contato)

---

## 🎯 Sobre o Projeto

**LojaJK** é uma solução completa de e-commerce desenvolvida em PHP puro, projetada para ser **simples, eficiente e escalável**. Com integração nativa ao Mercado Pago, oferece uma experiência de compra segura e profissional tanto para vendedores quanto para compradores.

### ✨ Por que escolher a LojaJK?

- 🚀 **Rápida implementação** - Configure sua loja em minutos
- 💰 **Pagamentos reais** - Integração completa com Mercado Pago
- 🔒 **Segurança robusta** - Proteção contra vulnerabilidades comuns
- 📱 **Responsivo** - Funciona perfeitamente em todos os dispositivos
- 🛠 **Fácil manutenção** - Código limpo e bem documentado
- 💡 **Extensível** - Arquitetura modular para futuras expansões

---

## 🚀 Funcionalidades

### 👥 Para Clientes
- ✅ **Cadastro e Login** com recuperação de senha por email
- 🛒 **Carrinho de Compras** inteligente com persistência
- 💝 **Lista de Favoritos** para compras futuras
- 🎫 **Sistema de Cupons** com desconto automático
- 📱 **Checkout Responsivo** otimizado para mobile
- 📋 **Histórico de Pedidos** completo com rastreamento
- 🔔 **Notificações** por email sobre status dos pedidos

### 🏪 Para Vendedores
- 📊 **Painel Administrativo** completo e intuitivo
- 📦 **Gestão de Produtos** com categorias e variações
- 💼 **Controle de Estoque** em tempo real
- 📈 **Relatórios de Vendas** detalhados
- 🎯 **Gestão de Cupons** com regras personalizáveis
- 👥 **Gerenciamento de Clientes** e histórico
- 💰 **Controle Financeiro** integrado

### 💳 Pagamentos
- 🏦 **Mercado Pago Checkout Pro** - Cartões, PIX, Boleto
- 🔐 **Transações Seguras** com criptografia SSL
- 📊 **Webhook Automático** para confirmação de pagamentos
- 💸 **Múltiplas Formas de Pagamento** em uma única integração

---

## 🛠 Tecnologias

<table>
<tr>
<td align="center" width="96">
<img src="https://skillicons.dev/icons?i=php" width="48" height="48" alt="PHP" />
<br>PHP 7.4+
</td>
<td align="center" width="96">
<img src="https://skillicons.dev/icons?i=mysql" width="48" height="48" alt="MySQL" />
<br>MySQL 5.7+
</td>
<td align="center" width="96">
<img src="https://skillicons.dev/icons?i=html" width="48" height="48" alt="HTML" />
<br>HTML5
</td>
<td align="center" width="96">
<img src="https://skillicons.dev/icons?i=css" width="48" height="48" alt="CSS" />
<br>CSS3
</td>
<td align="center" width="96">
<img src="https://skillicons.dev/icons?i=js" width="48" height="48" alt="JavaScript" />
<br>JavaScript
</td>
</tr>
</table>

### 📚 Dependências Principais
- **Composer** - Gerenciamento de dependências
- **Mercado Pago SDK** - Integração de pagamentos
- **PHPMailer** - Envio de emails
- **Bootstrap 5** - Framework CSS responsivo

---

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- **PHP 7.4 ou superior** com extensões:
  - `mysqli` ou `pdo_mysql`
  - `curl`
  - `json`
  - `mbstring`
  - `openssl`
- **MySQL 5.7+ ou MariaDB 10.2+**
- **Composer** (para gerenciamento de dependências)
- **Servidor Web** (Apache, Nginx ou PHP built-in server)

### 🔍 Verificar Requisitos

\`\`\`bash
# Verificar versão do PHP
php -v

# Verificar extensões instaladas
php -m | grep -E "(mysqli|curl|json|mbstring|openssl)"

# Verificar Composer
composer --version
\`\`\`

---

## 🚀 Instalação

### 1️⃣ Clone o Repositório

\`\`\`bash
git clone https://github.com/januario42/LojaJK.git
cd LojaJK
\`\`\`

### 2️⃣ Instale as Dependências

\`\`\`bash
composer install
\`\`\`

### 3️⃣ Configure o Banco de Dados

\`\`\`bash
# Crie o banco de dados
mysql -u root -p -e "CREATE DATABASE lojajk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importe o schema
mysql -u root -p lojajk < database/recriar_banco.sql
\`\`\`

### 4️⃣ Configure o Ambiente

\`\`\`bash
# Copie o arquivo de configuração
cp config/config.example.php config/config.php
cp config/mercadopago.example.php config/mercadopago.php
\`\`\`

### 5️⃣ Inicie o Servidor

\`\`\`bash
# Servidor PHP built-in (desenvolvimento)
php -S localhost:8000

# Ou configure seu Apache/Nginx apontando para a pasta do projeto
\`\`\`

🎉 **Pronto!** Acesse `http://localhost:8000` e sua loja estará funcionando!

---

## ⚙️ Configuração

### 🗄️ Banco de Dados (`config/config.php`)

\`\`\`php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'lojajk');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_CHARSET', 'utf8mb4');
?>
\`\`\`

### 💳 Mercado Pago (`config/mercadopago.php`)

\`\`\`php
<?php
// Credenciais de Sandbox (Teste)
define('MP_ACCESS_TOKEN', 'TEST-1234567890-123456-abcdef123456789-123456789');
define('MP_PUBLIC_KEY', 'TEST-abcdef12-3456-7890-abcd-ef1234567890');

// Para produção, use suas credenciais reais
// define('MP_ACCESS_TOKEN', 'APP_USR-1234567890-123456-abcdef123456789-123456789');
// define('MP_PUBLIC_KEY', 'APP_USR-abcdef12-3456-7890-abcd-ef1234567890');
?>
\`\`\`

### 📧 Email (`config/email.php`)

\`\`\`php
<?php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'seu-email@gmail.com');
define('SMTP_PASS', 'sua-senha-app');
define('SMTP_FROM', 'noreply@lojajk.com');
?>
\`\`\`

---

## 📖 Uso

### 👤 Primeiro Acesso

1. **Acesse a loja**: `http://localhost:8000`
2. **Crie uma conta** de cliente ou vendedor
3. **Configure produtos** (se for vendedor)
4. **Teste uma compra** usando dados de teste do Mercado Pago

### 🛒 Fluxo de Compra

1. Cliente navega pelos produtos
2. Adiciona itens ao carrinho
3. Aplica cupom de desconto (opcional)
4. Finaliza compra com Mercado Pago
5. Recebe confirmação por email

### 🏪 Gestão da Loja

1. Acesse o painel administrativo: `/admin`
2. Cadastre produtos e categorias
3. Configure cupons de desconto
4. Acompanhe pedidos e vendas
5. Gerencie clientes e relatórios

---

## 📁 Estrutura do Projeto

\`\`\`
LojaJK/
├── 📁 assets/              # CSS, JS, imagens
│   ├── css/
│   ├── js/
│   └── img/
├── 📁 config/              # Arquivos de configuração
│   ├── config.php
│   ├── mercadopago.php
│   └── email.php
├── 📁 database/            # Scripts SQL
│   ├── recriar_banco.sql
│   └── migrations/
├── 📁 includes/            # Arquivos PHP incluídos
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── 📁 pages/               # Páginas da aplicação
│   ├── home.php
│   ├── produto.php
│   ├── carrinho.php
│   └── checkout.php
├── 📁 admin/               # Painel administrativo
│   ├── dashboard.php
│   ├── produtos.php
│   └── pedidos.php
├── 📁 api/                 # Endpoints da API
│   ├── produtos.php
│   ├── carrinho.php
│   └── webhook.php
├── 📁 vendor/              # Dependências do Composer
├── 📄 index.php            # Arquivo principal
├── 📄 composer.json        # Dependências
└── 📄 README.md           # Este arquivo
\`\`\`

---

## 🔐 Segurança

### 🛡️ Medidas Implementadas

- ✅ **Sanitização de Dados** - Todos os inputs são validados
- ✅ **Prepared Statements** - Proteção contra SQL Injection
- ✅ **CSRF Protection** - Tokens de segurança em formulários
- ✅ **XSS Prevention** - Escape de dados de saída
- ✅ **Senhas Criptografadas** - Hash seguro com `password_hash()`
- ✅ **Sessões Seguras** - Configuração robusta de sessões
- ✅ **HTTPS Ready** - Preparado para SSL/TLS

### 🔒 Recomendações de Produção

\`\`\`bash
# 1. Configure HTTPS
# 2. Use senhas fortes no banco de dados
# 3. Configure firewall adequadamente
# 4. Mantenha PHP e MySQL atualizados
# 5. Configure backup automático
# 6. Use ambiente separado para testes
\`\`\`

---

## 🤝 Contribuição

Contribuições são sempre bem-vindas! Veja como você pode ajudar:

### 🚀 Como Contribuir

1. **Fork** o projeto
2. **Clone** seu fork: `git clone https://github.com/seu-usuario/LojaJK.git`
3. **Crie uma branch**: `git checkout -b feature/nova-funcionalidade`
4. **Faça suas alterações** e teste bem
5. **Commit**: `git commit -m "feat: adiciona nova funcionalidade"`
6. **Push**: `git push origin feature/nova-funcionalidade`
7. **Abra um Pull Request**

### 📝 Padrões de Commit

- `feat:` Nova funcionalidade
- `fix:` Correção de bug
- `docs:` Documentação
- `style:` Formatação
- `refactor:` Refatoração
- `test:` Testes
- `chore:` Manutenção

### 🐛 Reportando Bugs

Encontrou um bug? [Abra uma issue](https://github.com/januario42/LojaJK/issues) com:

- Descrição clara do problema
- Passos para reproduzir
- Comportamento esperado vs atual
- Screenshots (se aplicável)
- Informações do ambiente

---

## 📈 Roadmap

### 🎯 Próximas Versões

#### v2.0 - Q2 2024
- [ ] 📱 **App Mobile** (React Native)
- [ ] 🌐 **API REST** completa
- [ ] 🔍 **Busca Avançada** com filtros
- [ ] ⭐ **Sistema de Avaliações**

#### v2.1 - Q3 2024
- [ ] 📊 **Dashboard Analytics** avançado
- [ ] 🚚 **Integração com Correios**
- [ ] 💬 **Chat de Suporte** em tempo real
- [ ] 🎨 **Temas Personalizáveis**

#### v2.2 - Q4 2024
- [ ] 🤖 **IA para Recomendações**
- [ ] 📧 **Email Marketing** integrado
- [ ] 🏷️ **Sistema de Afiliados**
- [ ] 🌍 **Multi-idiomas**

### 💡 Ideias Futuras
- Integração com redes sociais
- Marketplace multi-vendedor
- PWA (Progressive Web App)
- Integração com ERPs

---

## ❓ FAQ

<details>
<summary><strong>Como configurar o Mercado Pago?</strong></summary>

1. Crie uma conta no [Mercado Pago Developers](https://developers.mercadopago.com)
2. Obtenha suas credenciais de teste e produção
3. Configure no arquivo `config/mercadopago.php`
4. Teste com dados de cartão de teste
</details>

<details>
<summary><strong>Como personalizar o layout?</strong></summary>

1. Edite os arquivos CSS em `assets/css/`
2. Modifique os templates em `includes/`
3. Use Bootstrap classes para responsividade
4. Teste em diferentes dispositivos
</details>

<details>
<summary><strong>Como fazer backup do banco?</strong></summary>

\`\`\`bash
# Backup completo
mysqldump -u root -p lojajk > backup_lojajk_$(date +%Y%m%d).sql

# Restaurar backup
mysql -u root -p lojajk < backup_lojajk_20240101.sql
\`\`\`
</details>

<details>
<summary><strong>Como configurar email?</strong></summary>

1. Configure SMTP no arquivo `config/email.php`
2. Para Gmail, use senha de app
3. Teste o envio na página de configurações
4. Verifique spam/lixo eletrônico
</details>

---

## 📄 Licença

Este projeto está licenciado sob a **Licença MIT** - veja o arquivo [LICENSE](LICENSE) para detalhes.

\`\`\`
MIT License - Você pode usar, modificar e distribuir livremente!
\`\`\`

---

## 📞 Contato

<div align="center">

**Desenvolvido com ❤️ por [Januário](https://github.com/januario42)**

[![GitHub](https://img.shields.io/badge/GitHub-januario42-181717?style=flat-square&logo=github)](https://github.com/januario42)
[![Email](https://img.shields.io/badge/Email-Contato-D14836?style=flat-square&logo=gmail&logoColor=white)](mailto:contato@lojajk.com)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-Perfil-0077B5?style=flat-square&logo=linkedin)](https://linkedin.com/in/januario42)

---

### 🌟 Se este projeto te ajudou, deixe uma estrela!

[![Stargazers](https://img.shields.io/github/stars/januario42/LojaJK?style=social)](https://github.com/januario42/LojaJK/stargazers)

</div>

---

<div align="center">
<sub>Feito com 💻 e ☕ no Brasil 🇧🇷</sub>
</div>
