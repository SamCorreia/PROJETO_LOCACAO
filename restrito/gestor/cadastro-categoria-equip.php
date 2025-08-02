<?php
require_once('../../config.php');
require_once('../../functions.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$con = new DBConnection();
verificaLogin(); 
getData();

// Verificação da estrutura das tabelas
$colunas_cat = $con->query("SHOW COLUMNS FROM notas_cat_e")->fetchAll(PDO::FETCH_COLUMN);
$colunas_sub = $con->query("SHOW COLUMNS FROM notas_cat_sub")->fetchAll(PDO::FETCH_COLUMN);

// Função para encontrar coluna com fallback
function encontrarColuna($possiveisNomes, $colunasTabela) {
    foreach ($possiveisNomes as $nome) {
        if (in_array($nome, $colunasTabela)) {
            return $nome;
        }
    }
    throw new Exception("Nenhuma coluna correspondente encontrada");
}

if(@$_GET['ac'] == 'inserir1') {
    try {
        $colunaDesc = encontrarColuna(['descricao', 'nome', 'categoria'], $colunas_cat);
        $valor = $_POST['descricao'] ?? '';
        
        $query = $con->prepare("INSERT INTO notas_cat_e($colunaDesc, oculto) VALUES (?, '0')");
        $query->execute([$valor]);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Categoria cadastrada com sucesso!'
        ]);
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Erro: ' . $e->getMessage(),
            'debug' => ['colunas_disponiveis' => $colunas_cat]
        ]);
    }
    exit;
}

if(@$_GET['ac'] == 'inserir2') {
    try {
        $colunaDesc = encontrarColuna(['descricao', 'nome', 'subcategoria'], $colunas_sub);
        $colunaAssoc = encontrarColuna(['associada', 'categoria_id', 'id_categoria'], $colunas_sub);
        
        $valor = $_POST['descricao'] ?? '';
        $associada = $_POST['associada'] ?? '';
        
        $query = $con->prepare("INSERT INTO notas_cat_sub ($colunaDesc, $colunaAssoc) VALUES (?, ?)");
        $query->execute([$valor, $associada]);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Sub-categoria cadastrada com sucesso!'
        ]);
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Erro: ' . $e->getMessage(),
            'debug' => ['colunas_disponiveis' => $colunas_sub]
        ]);
    }
    exit;
}
?>
<script>
$(document).ready(function(){
    // Converte para maiúsculas
    $(".up").keyup(function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Configura o envio dos formulários via AJAX
    $("form").on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const retorno = $('.retorno'); // Alterado para selecionar diretamente
        
        // Mostra loading
        retorno.html('<div class="text-center"><img src="../style/img/loading.gif" width="20px"><p>Enviando...</p></div>');
        
       $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                let html = '<div class="alert alert-' + response.status + '">' + 
                          response.message + '</div>';
                
                // Adiciona debug se disponível
                if(response.debug && console) {
                    console.log('Debug:', response.debug);
                }
                
                retorno.html(html);
                
                if(response.status === 'success') {
                    form.trigger('reset');
                    setTimeout(() => retorno.empty(), 2000);
                    
                    // Atualiza a lista de categorias no select
                    if(form.find('[name="associada"]').length) {
                        $.get('gestor/carregar-categorias.php', function(data) {
                            form.find('[name="associada"]').html(data);
                        });
                    }
                }
            },
            error: function(xhr) {
                retorno.html('<div class="alert alert-danger">Erro na comunicação com o servidor</div>');
                console.error('Erro AJAX:', xhr.responseText);
            }
        });
    });
});
</script>

<div class="container-fluid">
    <div class="retorno"></div>
    <div class="col-md-6" style="padding:10px">
        <h5><small>CATEGORIA</small></h5>
        <form action="gestor/cadastro-categoria-equip.php?ac=inserir1" method="post" class="form-horizontal">
            <label style="width:100%">Descrição:
                <input type="text" name="descricao" class="form-control input-sm up" required/>
            </label>
            <label style="width:100%; text-align:center"><br/>
                <button type="submit" class="btn btn-success btn-sm" style="width:50%">Cadastrar</button>
            </label>
        </form>
    </div>
    
    <div class="col-md-6" style="border-left:1px solid #E5E5E5; padding:10px;">
        <h5><small>SUB-CATEGORIA</small></h5>
        <form action="gestor/cadastro-categoria-equip.php?ac=inserir2" method="post" class="form-horizontal">
            <label style="width:100%">Descrição:
                <input type="text" name="descricao" class="form-control input-sm up" required/>
            </label>
            <label style="width:100%">Associada:    
                <select name="associada" class="form-control input-sm" required>
                    <?php 
                    $categorias = $con->query("SELECT * FROM notas_cat_e WHERE oculto = '0' ORDER BY descricao ASC");
                    while($l = $categorias->fetch()){
                        echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>'; 
                    }
                    ?>        
                </select>
            </label>                    
            <label style="width:100%; text-align:center"><br/>
                <button type="submit" class="btn btn-success btn-sm" style="width:50%">Cadastrar</button>
            </label>
        </form>
    </div>
</div>