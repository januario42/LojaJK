# 🛒 LojaJK — Sua Loja Virtual em PHP

> "Código simples, vendas reais."

LojaJK é um projeto de e-commerce desenvolvido em PHP e MySQL, com integração real com Mercado Pago para pagamentos seguros e rápidos. Ideal para quem deseja um MVP funcional, leve e fácil de manter.

---

## 🚀 Funcionalidades principais

- Cadastro e login de usuários com recuperação de senha  
- Painel do vendedor para cadastro de produtos e gestão de pedidos  
- Carrinho de compras com aplicação de cupons de desconto  
- Favoritar produtos para compra futura  
- Histórico de pedidos para clientes e vendedores  
- Integração com Mercado Pago Checkout Pro para pagamentos reais  

---

## 🛠 Tecnologias utilizadas

| Tecnologia     | Função                          |
| -------------- | ------------------------------ |
| PHP (>=7.4)    | Lógica de back-end              |
| MySQL/MariaDB  | Banco de dados relacional       |
| HTML / CSS / JS| Interface e interação           |
| Mercado Pago   | Processamento de pagamentos     |
| Composer       | Gerenciamento de dependências   |

---

## 📦 Como executar localmente

1. Clone o repositório:
   `git clone https://github.com/januario42/LojaJK.git` e entre na pasta com `cd LojaJK`.

2. Crie o banco de dados e importe o schema usando o comando:  
   `mysql -u root -p lojajk < recriar_banco.sql`  
   *(substitua `root` e `lojajk` conforme seu ambiente)*

3. Configure o arquivo `conexao.php` com as credenciais do seu banco de dados.

4. Configure suas credenciais do Mercado Pago no arquivo `config_mercadopago.php`.

5. Inicie o servidor local, por exemplo:  
   `php -S localhost:8000`

6. Acesse o sistema no navegador pelo endereço `http://localhost:8000/`.

📈 Roadmap
Melhorias no layout para responsividade

Criação de API para integração com aplicativos móveis

Sistema de avaliações e comentários em produtos

Implementação de busca avançada com filtros dinâmicos

🤝 Como contribuir
Faça um fork do projeto

Crie uma branch com sua feature: git checkout -b minha-feature

Commit suas alterações: git commit -m "Minha nova feature"

Envie para seu fork: git push origin minha-feature

Abra um Pull Request para análise

📄 Licença
Este projeto ainda não possui licença definida. Recomenda-se usar a licença MIT para maior liberdade.
