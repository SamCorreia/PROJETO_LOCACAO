<?php
    require_once('../../config.php');
    require_once('../../functions.php');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

$con = new DBConnection();
verificaLogin();
getData();


// Função segura para obter categorias
function getCategorias($con) {
    $tablesToTry = ['notas_cat_e', 'notas_categoria', 'categoria_equipamentos'];
    
    foreach ($tablesToTry as $table) {
        try {
            // Verifica se a tabela existe
            $check = $con->query("SHOW TABLES LIKE '$table'");
            if ($check->rowCount() > 0) {
                return $con->query("SELECT * FROM $table WHERE id <> '0'");
            }
        } catch (PDOException $e) {
            continue;
        }
    }
    throw new Exception("Nenhuma tabela de categorias foi encontrada");
}

// Função segura para obter subcategorias
function getSubcategorias($con, $categoriaId) {
    $tablesToTry = ['notas_cat_sub', 'subcategoria_equipamentos'];
    
    foreach ($tablesToTry as $table) {
        try {
            // Verifica se a tabela existe
            $check = $con->query("SHOW TABLES LIKE '$table'");
            if ($check->rowCount() > 0) {
                return $con->query("SELECT * FROM $table WHERE associada = '$categoriaId' ORDER BY descricao ASC");
            }
        } catch (PDOException $e) {
            continue;
        }
    }
    return false;
}
?>
<script>
// Substitua o evento de submit do formulário por este código
$(document).on('submit', '#form-cadastro', function(e) {
    e.preventDefault(); // Impede o comportamento padrão
    
    // Exibe loading
    $('#myModal .modal-body').html('<div class="text-center"><img src="../style/img/loading.gif" width="20px"><p>Enviando dados...</p></div>');
    
    // Envia via AJAX
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            $('#myModal .modal-body').html(response);
            // Recarrega a lista após 2 segundos
            setTimeout(function() {
                $('.modal').modal('hide');
                ldy('gestor/consulta-categoria-equip.php', '.conteudo');
            }, 2000);
        },
        error: function(xhr) {
            $('#myModal .modal-body').html(
                '<div class="alert alert-danger">Erro: ' + 
                xhr.statusText + 
                '</div><button onclick="history.go(0)" class="btn btn-default">Recarregar</button>'
            );
        }
    });
});
</script>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header clearfix">
                <h3 class="pull-left" style="font-family: 'Oswald', sans-serif; letter-spacing: 1px;">
                    CONSULTA <small>CATEGORIAS & SUB-CATEGORIAS</small>
                </h3>
                <div class="pull-right btn-group">
                    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal"
                        onclick="$('.modal-body').load('gestor/cadastro-categoria-equip.php')">
                        <i class="fa fa-plus-circle"></i> Cadastrar
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="ldy('gestor/consulta-categoria-equip.php','.conteudo')">
                        <i class="fa fa-refresh"></i> Atualizar
                    </button>
                </div>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h4><small>LISTA DE CATEGORIA</small></h4>
            <div class="table-responsive">
                <table class="table table-striped table-condensed small">
                    <thead>
                        <tr>
                            <th colspan="2"><i class="glyphicon glyphicon-eject"></i></th>
                            <th>Nome</th>
                            <th class="text-center"><i class="glyphicon glyphicon-eye-open"></i></th>
                            <th class="text-center"><i class="glyphicon glyphicon-cog"></i></th>
                            <th class="text-center"><i class="glyphicon glyphicon-flag"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $categorias = getCategorias($con);
                            $se = 0;
                            
                            while($categoria = $categorias->fetch()): 
                                $se++;
                                $statusClass = $categoria['oculto'] == '0' ? 'success' : 'danger';
                                $statusText = $categoria['oculto'] == '0' ? 'ATIVO' : 'INATIVO';
                        ?>
                        <tr class="info" id="categoria-<?= $categoria['id'] ?>">
                            <td width="3%"><?= $se ?></td>
                            <td colspan="2"><?= htmlspecialchars($categoria['descricao']) ?></td>
                            <td class="text-center">
                                <span class="btn btn-xs btn-<?= $statusClass ?>" style="font-size:8px">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="text-center" width="40px">
                                <a href="#" class="btn btn-primary btn-xs"
                                    onclick="$('.modal-body').load('gestor/editar-categoria-eqp.php?id=<?= $categoria['id'] ?>')"
                                    data-toggle="modal" data-target="#myModal">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                            <td class="text-center" width="40px">
                                <a href="#" class="btn btn-danger btn-xs btn-excluir"
                                    onclick="$('.modal-body').load('gestor/del/ex-cat-e.php?id=<?= $categoria['id'] ?>')"
                                    data-toggle="modal" data-target="#myModal2">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </a>
                            </td>
                        </tr>
                        
                        <?php
                            // Subcategorias
                            $subcategorias = getSubcategorias($con, $categoria['id']);
                            if ($subcategorias) {
                                $se2 = 0;
                                while($sub = $subcategorias->fetch()): 
                                    $se2++;
                                    $subStatusClass = $sub['oculto'] == '0' ? 'success' : 'danger';
                                    $subStatusText = $sub['oculto'] == '0' ? 'ATIVO' : 'INATIVO';
                        ?>
                        <tr id="subcategoria-<?= $sub['id'] ?>">
                            <td></td>
                            <td width="3%"><?= $se2 ?></td>
                            <td><?= htmlspecialchars($sub['descricao']) ?></td>
                            <td class="text-center">
                                <span class="btn btn-xs btn-<?= $subStatusClass ?>" style="font-size:8px">
                                    <?= $subStatusText ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-info btn-xs"
                                    onclick="$('.modal-body').load('gestor/editar-sub-categoria-eqp.php?id=<?= $sub['id'] ?>')"
                                    data-toggle="modal" data-target="#myModal">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-xs btn-excluir" style="background:#ff8484"
                                    onclick="$('.modal-body').load('gestor/del/ex-cat-sub.php?id=<?= $sub['id'] ?>')"
                                    data-toggle="modal" data-target="#myModal2">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                                endwhile;
                            }
                            endwhile;
                        } catch (Exception $e) {
                            echo '<tr><td colspan="6" class="text-center text-danger">';
                            echo '<div class="alert alert-danger">';
                            echo '<i class="fas fa-exclamation-triangle"></i> ' . $e->getMessage();
                            
                            if ($_SESSION['usuario']['nivel'] === 'admin') {
                                echo '<br><small>Erro técnico: ' . $e->getFile() . ' na linha ' . $e->getLine() . '</small>';
                            }
                            
                            echo '</div></td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modais -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header box box-info">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    onclick="$('.modal-body').empty()">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Gerenciamento</h4>
            </div>
            <div class="modal-body text-center">
                <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
                <p>Aguarde um momento...</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header box box-danger">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="confirmModalLabel">Confirmação</h4>
            </div>
            <div class="modal-body text-center">
                <img src="../style/img/loading.gif" alt="Carregando" width="20px"/>
                <p>Carregando confirmação...</p>
            </div>
        </div>
    </div>
</div>

<div class="retorno"></div>