<?php    
    include_once("default/header.php");
    
    if ($_SESSION['cliente']['acesso'] != 2) {
        header("location: erro.php");
    }
    
    require_once "funcoes/funcaoProduto.php";
    
    $id = "";
    $nomeMarca = "";
    $fornecedor = "";

    if (!empty($_GET)) {
        $id = $_GET['id'];

        if ($_GET['acao'] == 'carregar') {

            $marca = buscarMarca($id);
            $nomeMarca = $marca['nomeMarca'];
            $fornecedor = $marca['fornecedor'];
        }
        if ($_GET['acao'] == 'excluir') {
            excluirMarca($id);
            header("location: cadastroMarcas.php");
        }
    }
    if(!empty($_POST)) {

        if (!empty($_POST['id'])){
            editarMarca($_POST);
        } else {
            salvarMarca($_POST);
        }
    }
    $marcas = listarMarcas();

    $_SESSION['urlAnterior'] = $_SERVER['REQUEST_URI'];

?>

<!DOCTYPE html>

<body>
<?php    
    include_once("default/navbar.php");
?>
<main role="main" class="container">
    <h2>Cadastro de Marcas</h2>
        <form action="cadastroMarcas.php" method="POST">
        <input type="hidden" id="id" name="id" value="<?=$id?>"/>

        <div class="form-group">
            <label for="nomeMarca">Nome da Marca</label>
            <input type="text" class="form-control" maxlength="40" required name="nomeMarca"
            id="nomeMarca" placeholder="Digite o nome da Marca" value="<?=$nomeMarca?>">
        </div>

        <div class="form-group">
            <label for="fornecedor">Fornecedor</label>
            <input type="text"  maxlength="200" class="form-control" required
            name="fornecedor" id="fornecedor" placeholder="Digite o fornecedor da marca" value="<?=$fornecedor?>">
        </div>
        <input type="submit" value="Salvar" class="btn btn-primary" /> 
    </form>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Marca</th>
                    <th>Fornecedor</th>
                </tr>
            </thead>
            <?php
                foreach($marcas as $marca){
            ?>
                <tbody>
                    <tr>
                        <td><?=$marca['id']?></td>
                        <td><?=$marca['nomeMarca']?></td>
                        <td><?=$marca['fornecedor']?></td>
                        <td>
                            <a href="cadastroMarcas.php?acao=carregar&id=<?=$marca['id']?>"
                                class="btn btn-primary">Editar
                            </a>
                        </td>
                        <td>
                            <a href="cadastroMarcas.php?acao=excluir&id=<?=$marca['id']?>" 
                                class="btn btn-primary"
                                onclick="return confirm('Você está certo disso?');">
                                Remover
                            </a>
                        </td>
                    </tr>
                </tbody>
            <?php  
                }
            ?>
        </table>
    
        </main>

<!-- Bootstrap core JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
