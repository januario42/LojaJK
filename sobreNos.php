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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="./css/sobreNos.css">
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
                <a href="logado.php#Promo">PROMOÇÕES</a>
                <a href="sobreNos.php">SOBRE NÓS</a>
                <a href="logado.php#services">NOSSOS SERVIÇOS</a>
                <a href="logado.php#product">NOSSOS PRODUTOS</a>
                <a href="logado.php#review">AVALIAÇÃO DOS CLIENTES</a>
            </nav>
        </div>
    </header>
    
    <script>
        document.querySelectorAll('.btn-favorito').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                e.preventDefault();

                const id = this.dataset.id;
                const icon = this.querySelector('i');

                try {
                    const res = await fetch('favorito_toggle.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + encodeURIComponent(id)
                    });

                    const json = await res.json();

                    if (json.status === 'favoritado') {
                        this.classList.add('ativo');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        this.classList.remove('ativo');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                } catch (err) {
                    console.error('Erro ao favoritar:', err);
                }
            });
        });
    </script>

    <section class="container">
        <!--  
        <div class="carousel-backgrounds">
            <div class="bg-slide active" style="background-image: url('uploads/67f5ad3ddff37.jpg');"></div>
            <div class="bg-slide" style="background-image: url('uploads/classe.jpg');"></div>
            <div class="bg-slide" style="background-image: url('uploads/classe.jpg');"></div>
        </div>
        -->
       
        

        <div class="content left card">
            <h3>Nossa História</h3>
            <p>
                Estamos no mercado desde 2019 e, desde então, buscamos oferecer qualidade e diversidade em cada
                produto. A Loja JK nasceu para tornar as compras mais práticas e acessíveis, reunindo tudo o que
                você precisa em um só lugar.
                Com dedicação e compromisso, seguimos inovando para atender nossos clientes com excelência.
                Loja JK—confiança e variedade para o seu dia a dia.
            </p>
        </div>

        

        <div class="content right card">
            <h3>Missão</h3>
            <p>
                Oferecer variedade, qualidade e praticidade para tornar a experiência de compra acessível e
                satisfatória.
                Nosso compromisso é atender nossos clientes com excelência, trazendo produtos que facilitem o dia a
                dia e proporcionem bem-estar.
            </p>
        </div>

        <div class="content left card">
            <h3>Visão</h3>
            <p>
                Ser referência no mercado, oferecendo uma experiência de compra acessível, confiável e inovadora.
                Buscamos crescer junto com nossos clientes, sempre trazendo produtos que atendam às suas
                necessidades com qualidade e praticidade.
            </p>
        </div>
        
        <div class="content right card">
            <h3>Valores</h3>
            <p> 
                <b>Compromisso com a qualidade:</b> Oferecemos produtos selecionados para garantir a melhor experiência de compra.
                <b>Respeito e transparência:</b> Construímos relações baseadas na confiança e na honestidade com nossos clientes e parceiros.
                <b>Inovação constante:</b> Estamos sempre buscando novidades para oferecer praticidade e variedade.
                <b>Excelência no atendimento:</b> Priorizamos o bom atendimento, proporcionando soluções rápidas e eficazes.
                <b>Satisfação do cliente:</b> Nossa maior missão é atender às necessidades de quem confia na Loja JK.
            </p>
        </div>
        
        <div class="linha-divisoria"></div>
    </section>                

    <section class="carousel-container">
        

        <script>
        const slides = document.querySelectorAll('.bg-slide');
        let current = 0;

        setInterval(() => {
            slides[current].classList.remove('active');
            current = (current + 1) % slides.length;
            slides[current].classList.add('active');
        }, 5000); 
        </script>
        
    </section>

    <section class="footer">

        <div class="box-container">

            <div class="box">
                <h3>Encontre-nos aqui</h3>
                <p></p>
                <div class="share">
                    <a href="#" class="fab fa-facebook"></a>
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-linkedin"></a>
                    <a href="#" class="fab fa-pinterest"></a>
                </div>
            </div>

            <div class="box">
                <h3>Links Rápidos</h3>
                <a href="#" class="links"> política de Privacidade</a>
                <a href="#" class="links"> devoluções e trocas</a>
                <a href="#" class="links"> Avaliações de Clientes</a>
            </div>

            <div class="box">
                <h3>Contate-nos</h3>
                <a href="#" class="links"> +21-98000-0000</a>
                <a href="#" class="links"> +21-98000-0000</a>
                <a href="#" class="links"> Loja.@gmail.com</a>
            </div>

        </div>

        <div class="credit">© Feito por.<span> Enzo</span> </div>

    </section>
    

</body>

</html>