<?php
require __DIR__ . '/vendor/autoload.php';
include("config_mercadopago.php");

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

try {
    // Testar se o SDK está carregado
    MercadoPagoConfig::setAccessToken(getMercadoPagoToken());
    echo "SDK do MercadoPago carregado com sucesso!<br>";
    
    // Testar conectividade com a API
    $client = new PreferenceClient();
    
    // Criar uma preferência de teste simples
    $testRequest = [
        "items" => [
            [
                "id" => "test_item",
                "title" => "Produto de Teste",
                "description" => "Descrição do produto de teste",
                "currency_id" => "BRL",
                "quantity" => 1,
                "unit_price" => 10.00
            ]
        ],
        "back_urls" => [
            "success" => "http://localhost/teste_sucesso.php",
            "failure" => "http://localhost/teste_falha.php"
        ],
        "auto_return" => "approved"
    ];
    
    $preference = $client->create($testRequest);
    echo "Preferência criada com sucesso! ID: " . $preference->id . "<br>";
    echo "URL de pagamento: " . $preference->init_point . "<br>";
    
} catch (MPApiException $e) {
    echo "Erro da API do MercadoPago:<br>";
    echo "Mensagem: " . $e->getMessage() . "<br>";
    echo "Código: " . $e->getApiResponse()->getStatusCode() . "<br>";
    echo "Resposta: " . json_encode($e->getApiResponse()->getContent()) . "<br>";
} catch (Exception $e) {
    echo "Erro ao carregar SDK: " . $e->getMessage();
}
?> 