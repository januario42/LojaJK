<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}

$id_vendedor = $_SESSION['id_usuario'];

// ---------------------------
// Produtos cadastrados
// ---------------------------
$sql_produtos = "SELECT COUNT(*) AS total_produtos FROM produtos WHERE id_vendedor = ?";
$stmt = $conexao->prepare($sql_produtos);
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$stmt->bind_result($total_produtos);
$stmt->fetch();
$stmt->close();
// ---------------------------
// Vendas realizadas (pedidos entregues) - corrigido
// ---------------------------
$sql_vendas = "SELECT COUNT(*) AS total_vendas
               FROM itens_pedido ip
               JOIN pedidos p ON ip.pedido_id = p.id
               JOIN produtos pr ON ip.produto_id = pr.id
               WHERE pr.id_vendedor = ? AND p.status = 'entregue'";
$stmt = $conexao->prepare($sql_vendas);
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$stmt->bind_result($total_vendas);
$stmt->fetch();
$stmt->close();

// ---------------------------
// Faturamento total (somente entregues) - corrigido
// ---------------------------
$sql_faturamento = "SELECT SUM(ip.subtotal) AS faturamento
                    FROM itens_pedido ip
                    JOIN pedidos p ON ip.pedido_id = p.id
                    JOIN produtos pr ON ip.produto_id = pr.id
                    WHERE pr.id_vendedor = ? AND p.status = 'entregue'";
$stmt = $conexao->prepare($sql_faturamento);
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$stmt->bind_result($faturamento_total);
$stmt->fetch();
$stmt->close();
$faturamento_total = $faturamento_total ?: 0;

// ---------------------------
// Faturamento mensal (somente entregues) - corrigido
// ---------------------------
$sql_faturamento_mensal = "SELECT MONTH(p.data_criacao) AS mes, SUM(ip.subtotal) AS total
                           FROM itens_pedido ip
                           JOIN pedidos p ON ip.pedido_id = p.id
                           JOIN produtos pr ON ip.produto_id = pr.id
                           WHERE pr.id_vendedor = ? AND p.status = 'entregue'
                           GROUP BY mes ORDER BY mes";
$stmt = $conexao->prepare($sql_faturamento_mensal);
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$resultado_faturamento_mensal = $stmt->get_result();

$faturamento_mes = array_fill(1, 12, 0);
while ($row = $resultado_faturamento_mensal->fetch_assoc()) {
    $faturamento_mes[(int)$row['mes']] = (float)$row['total'];
}
$stmt->close();
// ---------------------------
// Ticket médio
// ---------------------------
$ticket_medio = $total_vendas > 0 ? $faturamento_total / $total_vendas : 0;

// ---------------------------
// Últimos produtos adicionados
// ---------------------------
$sql_ultimos = "SELECT nome, preco, estoque, imagem, data_cadastro 
                FROM produtos 
                WHERE id_vendedor = ? 
                ORDER BY data_cadastro DESC 
                LIMIT 5";
$stmt = $conexao->prepare($sql_ultimos);
$stmt->bind_param("i", $id_vendedor);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard do Vendedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="./css/dashboard.css"> 
</head>
<body class="container mt-5">
    <div class="dashboard">
        <a href="perfil.php" class="btn btn-secondary btn-voltar">← Voltar ao Perfil</a>
        <h2>Bem-vindo ao seu Painel, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h2>

        <div class="row mt-4 mb-4">
            <div class="col-md-3 mb-3">
                <div class="card p-3">
                    <h5>Produtos cadastrados</h5>
                    <p class="fs-4"><?php echo $total_produtos; ?></p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card p-3">
                    <h5>Vendas realizadas</h5>
                    <p class="fs-4"><?php echo $total_vendas; ?></p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card p-3">
                    <h5>Faturamento total</h5>
                    <p class="fs-4">R$ <?php echo number_format($faturamento_total, 2, ',', '.'); ?></p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card p-3">
                    <h5>Ticket médio</h5>
                    <p class="fs-4">R$ <?php echo number_format($ticket_medio, 2, ',', '.'); ?></p>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h4 class="mb-3">Faturamento Mensal</h4>
            <canvas id="graficoFaturamentoMes"></canvas>
        </div>

        <h4>Últimos Produtos Adicionados</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($produto = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><img src="produtos/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="img" style="width:50px;"></td>
                            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                            <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                            <td><?php echo $produto['estoque']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($produto['data_cadastro'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('graficoFaturamentoMes').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Faturamento por Mês (R$)',
                    data: [<?php echo implode(",", $faturamento_mes); ?>],
                    backgroundColor: '#025c0e'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => 'R$ ' + value.toLocaleString('pt-BR') }
                    }
                },
                plugins: {
                    title: { display: true, text: 'Faturamento Mensal' },
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>
