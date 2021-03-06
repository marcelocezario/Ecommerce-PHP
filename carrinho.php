<?php
include_once("default/header.php");

if (!empty($_SESSION['cliente'])){
    $cliente = $_SESSION['cliente'];
}
?>

<?php 
    require_once "funcoes/funcaoProduto.php";
    require_once "funcoes/calculoFrete.php";
    include_once("default/header.php");

    if (!empty($_GET)) {
      $idProduto = $_GET['idProduto'];

      if ($_GET['acao'] == 'carregar') {
          $produto = buscarProduto($idProduto);
      }
    }

    if (!empty($_SESSION['carrinho'])){
        $carrinho = $_SESSION['carrinho'];
    } else {
        $carrinho = array();
    }

    $totalCarrinho = 0;
    $totalFrete = 0;
    $prazoEntrega = 0;

    $idTemp = 0;

    foreach($carrinho as $item){
        $totalCarrinho += $item['valorTotal'];
    }

    $cepDestino = "";

    if (!empty($_POST['cep'])){
        $cepOrigem = 83030580;
        $cepDestino = $_POST['cep'];

        $valorDeclarado = $totalCarrinho;

        if ($valorDeclarado < 50){
            $valorDeclarado = 50;
        } elseif ($valorDeclarado > 10000){
            $valorDeclarado = 10000;
        }

        $frete = consultaFrete($cepOrigem, $cepDestino, $valorDeclarado);
        
        if($totalCarrinho > 10000){
            $totalFrete = round($frete['Valor'] * ($totalCarrinho / $valorDeclarado));
        } else {
            $totalFrete = $frete['Valor'];
        }
        
        if($prazoEntrega < $frete['PrazoEntrega']){
            $prazoEntrega = $frete['PrazoEntrega'];
        }
    }

    $totalCarrinho = $totalCarrinho + $totalFrete;

    $_SESSION['urlAnterior'] = $_SERVER['REQUEST_URI'];



?>

<!DOCTYPE html>
<html lang="en">


  <body>
        <?php    
            include_once("default/navbar.php");
        ?>
        <div>
<br/>
<br/>
</div>

        <div class="jumbotron-fluid">
        
                <div class="container">
 
                    <div class="row justify-content-between">
                        <div class="col-10">

                <?php
                    if (!empty($cliente)){
                ?>
                    <h1 class="display-4">Olá <?=$cliente['apelido']?>,</h1>
                <?php
                    } else {
                ?>
                    <h1 class="display-4">Faça login e aproveite já as melhores ofertas</h1>
                <?php
                    }
                ?>
                    <?php
                        if(!count($carrinho)>0){
                    ?>

                    <p class="lead">Parece que não tem nenhum produto no seu carrinho =(
                    <br>Não perca tempo e aproveite nossas ofertas</p>






                    <?php
                            } else {
                    ?>
                    
                    
                    
                    
                    <p class="lead">esse é seu carrinho de compras, clique em Finalizar Pedido para garantir essas ofertas</p>

                    </div>
                        <div class="col-2">
                        <br/>
<br/>
                            <form action="enderecoEntrega.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="cep" value="<?=$cepDestino?>">
                            <button type="submit" class="btn btn-primary btn-lg" >Finalizar Pedido</button>
<!--                            <button type="submit" class="btn btn-primary btn-lg" <?php //if(empty($_POST['cep'])):?>disabled<?php //endif;?>>Finalizar Pedido</button>
-->                            </form>
                        </div>
                    </div>
                    
                </div>



                <div class="container">
                    <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>Produto</th>
                        <th>Valor Unitário</th>
                        <th>Quantidade</th>
                        <th>Valor total item</th>
                    </tr>
                </thead>
                <?php
                    $idTemp = 0;
                    foreach($carrinho as $item){
                ?>
                    <tbody>
                        <tr>
                            <td>
                            <?php
                                $produto = buscarProduto($item['idProduto']);
                                if(!empty($produto['url'])){                                 
                            ?>
                            <img src="<?=$produto['url']?>" class="rounded-circle" width="50" height="50" />
                            <?php
                                }
                            ?>
                            </td> 
                            <td><?=$item['nomeProduto']?></td>
                            <td><?=number_format($item['valor'],2,",",".")?></td>
                            
                            
                            <td>
                            <a class="btn btn-link" href="adicionarCarrinho.php?qtde=diminuir&id=<?=$item['idTemp']-1?>">
                                <span class="badge badge-pill badge-danger">-</span>
                            </a>
                            
                            <?=$item['qtde']?>
                            <a class="btn btn-link" href="adicionarCarrinho.php?qtde=aumentar&id=<?=$item['idTemp']-1?>">
                                <span class="badge badge-pill badge-success">+</span>
                            </a>
                            </td>

                            <td><?=number_format($item['valorTotal'],2,",",".")?></td>
                            <td>
                                <a href="adicionarCarrinho.php?acao=remover&id=<?=$item['idTemp']-1?>" 
                                    class="btn btn-primary"
                                    onclick="return confirm('Você está certo disso?');">
                                    Remover
                                </a>
                            </td>

                        </tr>
                        
                    </tbody>
                <?php 
                     $idTemp++;
                    }
                ?>
                        <td colspan="4">
                            <div class="text-right">

                            <form action="carrinho.php" method="POST" enctype="multipart/form-data">
                                <div class="col-5">
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="cep" placeholder="Cep" value="<?=$_POST['cep']?>">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-outline-secondary" id="calcularFrete">Simular Frete</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            </div>
                        </td>
                        <td colspan="4">
                            <div class="text-right">

                                <h6>Valor do frete: <?=number_format($totalFrete,2,",",".")?></h6>
                                <br>
                                <h6>Prazo para entrega: <?=$prazoEntrega?> dias úteis</h6>
                            </div>
                               </td>
                               </tr>
                        <td colspan="6">
                            <div class="text-right">

                                <h4>Valor total da compra: R$ <?=number_format($totalCarrinho,2,",",".");?></h4>
                            </div>
                               </td>
            </table>

                <div>

                    <a href="opcoes.php?acao=limpar" class="badge badge-danger">Limpar carrinho</a>

                                     <?php
                            }
                    ?>

                </div>

                </div>
                </div>

        






    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  
  </body>

</html>
