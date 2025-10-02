<?php
$favoritos = $_SESSION['favoritos'] ?? [];
$quantidadeFavoritos = count($favoritos);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre nós</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="./css/sobreNos.css">
    <style>
        .sobre-nos-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            padding: 3rem 1rem 2rem 1rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .sobre-card {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 1.2rem;
            box-shadow: 0 8px 32px rgba(12, 180, 34, 0.13), 0 1.5px 6px rgba(0, 0, 0, 0.07);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 700px;
            text-align: left;
            position: relative;
            overflow: hidden;
            transition: box-shadow .3s, transform .3s, background .3s;
            border-left: 7px solid #0cb422;
            margin-bottom: 0.5rem;
            animation: fadeInUp 0.8s;
        }

        .sobre-card:hover {
            box-shadow: 0 16px 40px rgba(12, 180, 34, 0.18), 0 2px 8px rgba(0, 0, 0, 0.09);
            transform: translateY(-8px) scale(1.02);
            background: rgba(255, 255, 255, 0.99);
            border-left: 7px solid #ffe600;
        }

        .sobre-card h3 {
            color: #0cb422;
            margin-bottom: 1.2rem;
            font-size: 2.1rem;
            font-weight: 700;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            text-shadow: 0 2px 8px #0cb42222;
        }

        .sobre-card p {
            color: #222;
            font-size: 1.18rem;
            line-height: 1.8;
            margin-bottom: 0;
        }

        .sobre-card b {
            color: #0cb422;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            .sobre-nos-container {
                padding: 1.5rem 0.5rem;
                gap: 1.2rem;
            }

            .sobre-card {
                padding: 1.2rem 0.7rem;
                border-radius: 0.7rem;
                font-size: 1rem;
            }

            .sobre-card h3 {
                font-size: 1.2rem;
            }

            .sobre-card p {
                font-size: 0.98rem;
            }
        }
    </style>
</head>

<body>
    <!-- header section starts -->

    <header>
        <div class="header-top">

            <div class="fas fa-bars" id="menu"></div>

            <div class="social">
                <a href="#" class="fab fa-facebook"></a>
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-pinterest"></a>
                <a href="#" class="fab fa-linkedin"></a>
            </div>

            <a href="#" class="logo"><i class="fas fa-couch"></i>Loja Jk</a>

            <div class="icons">
                <?php if (isset($_SESSION['usuario'])): ?>
                <span class="user-name">👤 <?php echo $_SESSION['usuario']; ?></span>
                <button class="action-btn">
                    <a href="perfil.php" class="fas fa-user-cog" title="Configurações"></a>
                </button>
                <button class="action-btn">
                    <a href="logout.php" class="fas fa-sign-out-alt" title="Sair"></a>
                </button>
                <?php else: ?>
                <button class="action-btn">
                    <a href="login.php" class="fas fa-user"></a>
                </button>
                <?php endif; ?>

                <button class="action-btn">
                    <a href="favoritos.php" class="fas fa-heart"></a>
                    <span class="count"><?= $quantidadeFavoritos ?></span>
                </button>

                <button class="action-btn">
                    <a href="#" class="fas fa-shopping-cart"></a>
                    <span class="count">0</span>
                </button>

            </div>
        </div>

        <!-- navbar start -->
        <div class="header-main">
            <nav class="navbar">
                <a href="logado.php#home">HOME</a>
                <a href="sobreNos.php">SOBRE NÓS</a>
                <a href="logado.php#services">NOSSOS SERVIÇOS</a>
                <a href="logado.php#product">NOSSOS PRODUTOS</a>
                <a href="logado.php#review">AVALIAÇÃO DOS CLIENTES</a>
            </nav>
        </div>
    </header>

    <section class="sobre-nos-container">
        <div class="sobre-card">
            <h3>Nossa História</h3>
            <p>
                Estamos no mercado desde 2019 e, desde então, buscamos oferecer qualidade e diversidade em cada produto. A Loja JK nasceu para tornar as compras mais práticas e acessíveis, reunindo tudo o que você precisa em um só lugar.<br>
                Com dedicação e compromisso, seguimos inovando para atender nossos clientes com excelência.<br>
                <b>Loja JK</b> — confiança e variedade para o seu dia a dia.
            </p>
        </div>
        <div class="sobre-card">
            <h3>Missão</h3>
            <p>
                Oferecer variedade, qualidade e praticidade para tornar a experiência de compra acessível e satisfatória.<br>
                Nosso compromisso é atender nossos clientes com excelência, trazendo produtos que facilitem o dia a dia e proporcionem bem-estar.
            </p>
        </div>
        <div class="sobre-card">
            <h3>Visão</h3>
            <p>
                Ser referência no mercado, oferecendo uma experiência de compra acessível, confiável e inovadora.<br>
                Buscamos crescer junto com nossos clientes, sempre trazendo produtos que atendam às suas necessidades com qualidade e praticidade.
            </p>
        </div>
        <div class="sobre-card">
            <h3>Valores</h3>
            <p>
                <b>Compromisso com a qualidade:</b> Oferecemos produtos selecionados para garantir a melhor experiência de compra.<br>
                <b>Respeito e transparência:</b> Construímos relações baseadas na confiança e na honestidade com nossos clientes e parceiros.<br>
                <b>Inovação constante:</b> Estamos sempre buscando novidades para oferecer praticidade e variedade.<br>
                <b>Excelência no atendimento:</b> Priorizamos o bom atendimento, proporcionando soluções rápidas e eficazes.<br>
                <b>Satisfação do cliente:</b> Nossa maior missão é atender às necessidades de quem confia na Loja JK.
            </p>
        </div>
    </section>

    <section class="footer">
        <div class="box-container">

            <div class="box">
                <h3>Encontre-nos aqui</h3>
                <div class="share">
                    <a href="#" class="fab fa-facebook"></a>
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                    <a href="#" class="fab fa-pinterest"></a>
                </div>
            </div>

            <div class="box">
                <h3>Links Rápidos</h3>
                <a href="#" class="links">Política de Privacidade</a>
                <a href="#" class="links">Devoluções e Trocas</a>
                <a href="#" class="links">Avaliações de Clientes</a>
            </div>

            <div class="box">
                <h3>Contate-nos</h3>
                <a href="#" class="links">+21-98000-0000</a>
                <a href="#" class="links">+21-98000-0000</a>
                <a href="#" class="links">Loja.@gmail.com</a>
            </div>

        </div>

        <div class="credit">© Feito por.<span> Enzo</span> </div>

    </section>


</body>

</html>


