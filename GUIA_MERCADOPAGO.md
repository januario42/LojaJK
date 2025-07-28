# 🚀 Guia de Configuração do Mercado Pago - LojaJK

## 📋 Pré-requisitos

1. **Conta no Mercado Pago**
   - Acesse: https://www.mercadopago.com.br
   - Crie uma conta gratuita
   - Complete a verificação da conta

2. **Composer instalado**
   - O projeto já tem o `composer.json` configurado
   - Execute: `composer install` na pasta do projeto

## 🔧 Configuração das Credenciais

### 1. Obter Access Token

1. **Acesse o Painel do Mercado Pago**
   - Vá para: https://www.mercadopago.com.br/developers/panel/credentials

2. **Escolha o Ambiente**
   - **Teste**: Para desenvolvimento
   - **Produção**: Para loja ativa

3. **Copie o Access Token**
   - Clique em "Ver credenciais"
   - Copie o "Access Token"

### 2. Configurar o Arquivo

Edite o arquivo `config_mercadopago.php`:

```php
// Para desenvolvimento (teste)
define('MP_TEST_ACCESS_TOKEN', 'TEST-1234567890abcdef...');

// Para produção
define('MP_PRODUCTION_ACCESS_TOKEN', 'APP-1234567890abcdef...');
```

## 🧪 Testando a Integração

### 1. Instalar Dependências
```bash
composer install
```

### 2. Testar o Sistema
- Acesse: `http://localhost/LojaJK-main/teste_rapido.php`
- Verifique se não há erros

### 3. Testar o Carrinho
1. Adicione produtos ao carrinho
2. Vá para o checkout
3. Teste o pagamento

## 💳 Cartões de Teste

### Cartões de Crédito (Teste)
- **Visa**: 4509 9535 6623 3704
- **Mastercard**: 5031 4332 1540 6351
- **American Express**: 3711 8030 3257 522

### Dados de Teste
- **CVV**: 123
- **Data**: 12/25
- **Nome**: Qualquer nome
- **CPF**: 12345678909

## 🔄 Fluxo de Pagamento

1. **Cliente adiciona produtos ao carrinho**
2. **Acessa o checkout**
3. **Preenche dados de entrega**
4. **Clica em "Pagar com Mercado Pago"**
5. **É redirecionado para o Mercado Pago**
6. **Escolhe método de pagamento**
7. **Finaliza o pagamento**
8. **Retorna para a loja**

## 📧 URLs de Retorno

O sistema está configurado para retornar para:
- **Sucesso**: `sucesso.php`
- **Falha**: `falha.php`
- **Pendente**: `pendente.php`

## 🛠️ Personalização

### Alterar URLs de Retorno
Edite o arquivo `checkout.php`:

```php
$preference->back_urls = [
    "success" => "http://seudominio.com/sucesso.php",
    "failure" => "http://seudominio.com/falha.php",
    "pending" => "http://seudominio.com/pendente.php"
];
```

### Adicionar Dados do Cliente
No arquivo `checkout.php`, você pode adicionar:

```php
$preference->payer = [
    "name" => $usuario['nome'],
    "email" => $usuario['email']
];
```

## 🔒 Segurança

### 1. Webhooks (Recomendado)
Configure webhooks para receber notificações em tempo real:

```php
// Exemplo de webhook
$payment = \MercadoPago\Payment::find_by_id($_POST['data']['id']);
if ($payment->status === 'approved') {
    // Processar pedido
}
```

### 2. Validação de Dados
Sempre valide os dados recebidos do Mercado Pago.

## 📊 Monitoramento

### 1. Painel do Mercado Pago
- Acesse: https://www.mercadopago.com.br/activities
- Monitore pagamentos em tempo real

### 2. Logs
Configure logs para rastrear transações:

```php
error_log("Pagamento processado: " . $payment_id);
```

## 🆘 Solução de Problemas

### Erro: "Access Token inválido"
- Verifique se o token está correto
- Confirme se está usando o ambiente correto (teste/produção)

### Erro: "SDK não encontrado"
- Execute: `composer install`
- Verifique se o `vendor/autoload.php` existe

### Erro: "URL de retorno inválida"
- Verifique se as URLs estão corretas
- Confirme se o domínio está acessível

## 📞 Suporte

- **Mercado Pago**: https://www.mercadopago.com.br/developers/support
- **Documentação**: https://www.mercadopago.com.br/developers/docs

## 🎯 Próximos Passos

1. **Configure as credenciais**
2. **Teste com cartões de teste**
3. **Configure webhooks**
4. **Teste em produção**
5. **Monitore as transações**

---

**Boa sorte com sua integração! 🚀** 