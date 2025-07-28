<?php
// Configurações do Mercado Pago
// IMPORTANTE: Substitua pelas suas credenciais reais

// Credenciais de Produção (use estas quando estiver em produção)
define('MP_PRODUCTION_ACCESS_TOKEN', 'APP_USR-4966121152290989-050623-b624cf07af6434ca27eb9c31945894f2-397089893');

// Credenciais de Teste (use estas para desenvolvimento)
// Para obter o token de teste: https://www.mercadopago.com.br/developers/panel/credentials
define('MP_TEST_ACCESS_TOKEN', 'SEU_ACCESS_TOKEN_DE_TESTE_AQUI');

// URLs de retorno
define('MP_SUCCESS_URL', 'http://localhost/LojaJK-main/LojaJK-main/sucesso.php');
define('MP_FAILURE_URL', 'http://localhost/LojaJK-main/LojaJK-main/falha.php');
define('MP_PENDING_URL', 'http://localhost/LojaJK-main/LojaJK-main/pendente.php');

// URL do webhook
define('MP_WEBHOOK_URL', 'http://localhost/LojaJK-main/LojaJK-main/webhook_mercadopago.php');

// Configurações da loja
define('MP_STORE_NAME', 'LojaJK');
define('MP_STORE_EMAIL', 'contato@lojajk.com');

// Função para obter o token correto baseado no ambiente
function getMercadoPagoToken() {
    // Para desenvolvimento, use o token de teste
    // Para produção, use o token de produção
    return MP_PRODUCTION_ACCESS_TOKEN; // Usando token de produção para pagamentos reais
}
?> 