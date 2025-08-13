<?php
session_start();
require_once 'conexao.php';

function escapar($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Recupera os produtos
$sql = "SELECT * FROM produtos ORDER BY data_cadastro DESC";
$result = $conexao->query($sql);

// Lógica de desconto com validade
function getDescontoComDuracao($produtoId) {
    if (!isset($_SESSION['descontos'])) $_SESSION['descontos'] = [];

    $hoje = date('Y-m-d');
    if (isset($_SESSION['descontos'][$produtoId])) {
        $info = $_SESSION['descontos'][$produtoId];
        if ($hoje <= $info['validade']) return $info;
    }

    $desconto = rand(5, 30);
    $dias = rand(3, 7);
    $validade = date('Y-m-d', strtotime("+$dias days"));

    $_SESSION['descontos'][$produtoId] = [
        'percentual' => $desconto,
        'validade' => $validade
    ];
    return $_SESSION['descontos'][$produtoId];
}

// Inicializar a lista de favoritos
$favoritos = $_SESSION['favoritos'] ?? [];
$quantidadeFavoritos = count($favoritos);
// Adiciona a contagem real do carrinho
$quantidadeCarrinho = isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0;

// Exibir os produtos
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site LojaJK</title>
    <!-- fontawesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- swiper js cdn link -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- custom css file -->
    <link rel="stylesheet" href="./css/logado.css">
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
                    <a href="carrinho.php" class="fas fa-shopping-cart"></a>
                    <span class="count" id="cart-count"><?= $quantidadeCarrinho ?></span>
                </button>
            </div>

        </div>

        <div class="header-main">

            <nav class="navbar">
                <a href="#home">HOME</a>
                <a href="#Promo">PROMOÇÕES</a>
                <a href="sobreNos.php">SOBRE NÓS</a>
                <a href="#services">NOSSOS SERVIÇOS</a>
                <a href="#product">NOSSOS PRODUTOS</a>
                <a href="#review">AVALIAÇÃO DOS CLIENTES</a>
            </nav>

        </div>

    </header>


    <!-- header section ends -->

    <!-- home section starts -->

    <section class="home" id="home">

        <div class="content">
            <h3>Produtos</h3>
            <p>Que cada mochila carregue não apenas livros, mas também o potencial infinito de uma educação de
                qualidade.</p>
            <a href="#" class="btn">Comprar</a>
        </div>

    </section>

    <!-- home section ends -->


    <!-- banner section starts -->

        <!-- banner section ends -->


        <!-- about section starts -->

        <section class="about" id="about">

            <h1 class="heading">SOBRE <span>NÓS</span></h1>

            <div class="row">

                <div class="image">
                    <img src="images/about.png" alt="">
                </div>

                <div class="content">
                    <h3>Material escolar de melhor qualidade</h3>
                    <p>No universo da educação, cada detalhe importa. Desde os primeiros passos até a jornada acadêmica,
                        a qualidade do material escolar desenha a experiência de aprendizado. Optar pelo melhor não é
                        apenas uma escolha, é um investimento no futuro.

                        Imagine uma sala de aula onde cada caderno é mais do que papel e capa: é um convite à
                        criatividade, à organização e ao aprendizado meticuloso. Papéis suaves que acariciam as canetas,
                        marcadores vibrantes que saltam das páginas, e mochilas robustas que carregam sonhos.
                    </p>
                    <a href="#" class="btn">Comprar</a>
                </div>

            </div>

        </section>

        <!-- about section ends -->


        <!-- services section starts -->

        <section class="services" id="services">

    <h1 class="heading">NOSSOS <span>SERVIÇOS</span></h1>

    <div class="box-container">

        <div class="box">
            <data class="card-number" value="01">01</data>
            <h3>Cadastro de Produtos</h3>
            <p>Permite que os alunos cadastrem facilmente os produtos que desejam vender dentro da escola.</p>
        </div>

        <div class="box">
            <data class="card-number" value="02">02</data>
            <h3>Gerenciamento de Vendas</h3>
            <p>Controle rápido de pedidos, status de vendas e histórico, tudo em um só lugar.</p>
        </div>

        <div class="box">
            <data class="card-number" value="03">03</data>
            <h3>Relatórios Simples</h3>
            <p>Visualize gráficos e estatísticas para acompanhar o desempenho das vendas de forma clara e rápida.</p>
        </div>

        <div class="box">
            <data class="card-number" value="04">04</data>
            <h3>Facilidade e Agilidade</h3>
            <p>Um sistema intuitivo que ajuda os alunos a venderem mais rápido e de forma organizada.</p>
        </div>

    </div>

</section>


        <!-- services section ends -->


        <!-- products section starts -->


        <section class="product" id="product">
            <h1 class="heading">NOSSOS <span>PRODUTOS</span></h1>
            <div class="box-container">
                <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($produto = $result->fetch_assoc()): ?>
                <?php
                    $id = $produto['id'];
                    $imagem = "produtos/" . escapar($produto['imagem']);
                    if (!file_exists($imagem) || empty($produto['imagem'])) {
                        $imagem = "images/default.png";
                    }

                    // Calcular o desconto e preço final
                    $desconto = getDescontoComDuracao($id);
                    $precoOriginal = $produto['preco'];
                    $precoFinal = $precoOriginal * (1 - $desconto['percentual'] / 100);

                    // Verificar se o produto está nos favoritos
                    $favoritos = $_SESSION['favoritos'] ?? [];
                    $isFavorito = in_array($id, $favoritos);
                ?>
                <div class="box">
                    <figure class="cardbanner">
                        <img src="<?= $imagem ?>" width="200" height="200" loading="lazy"
                            alt="Imagem de <?= escapar($produto['nome']) ?>">
                        <div class="btn-wrapper">
                            <button class="product-btn btn-favorito" data-id="<?= $produto['id']; ?>">
                                <i class="<?= $isFavorito ? 'fas' : 'far'; ?> fa-heart"></i>
                                <div class="tooltip"><?= $isFavorito ? 'Remover do gostei' : 'Gostei'; ?></div>
                            </button>
                            <button class="product-btn" data-id="<?= $produto['id'] ?>">
                                <i class="fas fa-shopping-cart"></i>
                                <div class="tooltip">Adicionar ao carrinho</div>
                            </button>
                        </div>

                    </figure>
                    <div class="card-content">
                        <h3><?= escapar($produto['nome']) ?></h3>
                        <div class="price-wrapper">
                            <data class="price">R$<?= number_format($precoOriginal, 2, ',', '.') ?></data>
                        </div>
                    </div>

                <?php endwhile; ?>
                <?php else: ?>
                <p>Nenhum produto disponível.</p>
                <?php endif; ?>
            </div>
        </section>

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

        // Adicionar ao carrinho
        document.querySelectorAll('.product-btn:not(.btn-favorito)').forEach(btn => {
            btn.addEventListener('click', async function(e) {
                e.preventDefault();

                const id = this.dataset.id;
                try {
                    const res = await fetch('acoes/carrinho.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + encodeURIComponent(id)
                    });

                    const json = await res.json();

                    if (json.status === 'ok') {
                        // Atualiza o número do carrinho no topo
                        document.getElementById('cart-count').textContent = json.total;
                    }
                } catch (err) {
                    console.error('Erro ao adicionar ao carrinho:', err);
                }
            });
        });
</script>



        <!-- products section ends -->

        <!-- review section starts -->
<section class="review" id="review">

    <div class="row">

        <div class="swiper review-slider">

            <div class="swiper-wrapper">

                <div class="swiper-slide slide">
                    <h2 class="heading">Avaliação do aluno</h2>
                    <i class="fas fa-quote-right"></i>
                    <div class="user">
                        <img src="images/pic-1.png" alt="">
                        <div class="user-info">
                            <h3>Ana Clara</h3>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p>Adorei como posso cadastrar meus produtos e acompanhar as vendas de forma prática!</p>
                </div>

                <div class="swiper-slide slide">
                    <h2 class="heading">Avaliação do aluno</h2>
                    <i class="fas fa-quote-right"></i>
                    <div class="user">
                        <img src="images/pic-2.png" alt="">
                        <div class="user-info">
                            <h3>Lucas Silva</h3>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p>O sistema facilita muito minhas vendas e ajuda a organizar meus pedidos.</p>
                </div>

                <div class="swiper-slide slide">
                    <h2 class="heading">Avaliação do aluno</h2>
                    <i class="fas fa-quote-right"></i>
                    <div class="user">
                        <img src="images/pic-3.png" alt="">
                        <div class="user-info">
                            <h3>Mariana Costa</h3>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p>Agora consigo acompanhar todas as minhas vendas e clientes sem me perder nos pedidos.</p>
                </div>

                <div class="swiper-slide slide">
                    <h2 class="heading">Avaliação do aluno</h2>
                    <i class="fas fa-quote-right"></i>
                    <div class="user">
                        <img src="images/pic-4.png" alt="">
                        <div class="user-info">
                            <h3>Rafael Gomes</h3>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p>O sistema incentiva o empreendedorismo dentro da escola de forma simples e eficiente.</p>
                </div>

                <div class="swiper-slide slide">
                    <h2 class="heading">Avaliação do aluno</h2>
                    <i class="fas fa-quote-right"></i>
                    <div class="user">
                        <img src="images/pic-5.png" alt="">
                        <div class="user-info">
                            <h3>Beatriz Lima</h3>
                            <div class="stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p>Recomendo para todos os alunos que querem começar a vender seus produtos na escola.</p>
                </div>

            </div>

            <div class="swiper-pagination"></div>

        </div>

        <div class="accordion-container">

            <div class="accordion active">
                <div class="accordion-heading">
                    <h3>Como posso cadastrar meus produtos?</h3>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accordion-content">
                    Basta acessar sua conta, clicar em "Adicionar Produto" e preencher os detalhes do item que deseja vender.
                </p>
            </div>

            <div class="accordion">
                <div class="accordion-heading">
                    <h3>O sistema é seguro e confiável?</h3>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accordion-content">
                    Sim! Todas as informações são armazenadas de forma segura e os pedidos são acompanhados em tempo real.
                </p>
            </div>

            <div class="accordion">
                <div class="accordion-heading">
                    <h3>Quais formas de pagamento são aceitas?</h3>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accordion-content">
                    O sistema permite pagamento via cartão, PIX ou transferência interna da escola, conforme definido pelo administrador.
                </p>
            </div>

            <div class="accordion">
                <div class="accordion-heading">
                    <h3>Como funcionam os relatórios de vendas?</h3>
                    <i class="fas fa-angle-down"></i>
                </div>
                <p class="accordion-content">
                    Você pode visualizar gráficos e estatísticas das suas vendas, acompanhar os pedidos e verificar o desempenho dos seus produtos.
                </p>
            </div>

        </div>

    </div>

</section>


        <!-- review section ends -->

        <!-- newsletter -->

        <section class="newsletter">

            <div class="content">
                <h3>informe </h3>
                <h6>como nós podemos melhorar</h6>
                <p></p>
                <input type="email" class="email" placeholder="Seu e-mail">
                <a href="#" class="btn"> Enviar</a>
            </div>

        </section>

        <!-- end -->

        <!-- footer section start -->

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












        <!-- footer section end -->























        <!-- swiper js cdn link -->
         
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <!-- custom js file -->
        <script src="script.js"></script>

</body>

</html>