<?php
// Configuração inicial
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/debug.log');

require_once('../../config.php');
require_once('../../functions.php');

// Conexão com banco de dados e verificação de login
$con = new DBConnection();
verificaLogin(); 
getData();

// Atualização inicial de equipamentos
$con->query("UPDATE notas_equipamentos SET obra = '1' WHERE obra = '0'");

// Processamento do formulário de cadastro
if(isset($_GET['ac']) && $_GET['ac'] == 'up') {
    try {
        // Sanitização das entradas
        $patrimonio = isset($_POST['patrimonio']) ? htmlspecialchars($_POST['patrimonio']) : '';
        $placa = isset($_POST['placa']) ? htmlspecialchars($_POST['placa']) : '';
        $marca = isset($_POST['marca']) ? htmlspecialchars($_POST['marca']) : '';
        $patrimonio2 = isset($_POST['patrimonio2']) ? htmlspecialchars($_POST['patrimonio2']) : '';
        $valor = isset($_POST['valor']) ? floatval($_POST['valor']) : 0;
        $seguro = isset($_POST['seguro']) ? htmlspecialchars($_POST['seguro']) : '';
        $desconto = isset($_POST['desconto']) ? floatval($_POST['desconto']) : 0;
        $chassi = isset($_POST['chassi']) ? htmlspecialchars($_POST['chassi']) : '';
        $ano = isset($_POST['ano']) ? htmlspecialchars($_POST['ano']) : '';
        $empresa = isset($_POST['empresa']) ? intval($_POST['empresa']) : 0;
        $categoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : 0;
        $sub_categoria = isset($_POST['sub_categoria']) ? intval($_POST['sub_categoria']) : 0;
        $situacao = isset($_POST['situacao']) ? intval($_POST['situacao']) : 0;
        $entrada = isset($_POST['entrada']) ? $_POST['entrada'] : date('Y-m-d');
        $saida = isset($_POST['saida']) ? $_POST['saida'] : null;
        $id_usuario_logado = $_SESSION['id_usuario'] ?? 0;

        // Verifica se o patrimônio já existe
        $consulta = $con->prepare("SELECT COUNT(*) as total FROM notas_equipamentos WHERE patrimonio = ?");
        $consulta->execute([$patrimonio]);
        $cb = $consulta->fetch();

        if($cb['total'] == 0) {
            // Insere novo equipamento
            $query = $con->prepare("INSERT INTO notas_equipamentos 
                                  (placa, patrimonio, marca, patrimonio2, valor, justificativa, desconto, 
                                   chassi, ano, empresa, categoria, sub_categoria, situacao, entrada, 
                                   saida, user_edit, data_edit) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
            $query->execute([
                $placa, $patrimonio, $marca, $patrimonio2, $valor, $seguro, $desconto, 
                $chassi, $ano, $empresa, $categoria, $sub_categoria, $situacao, 
                $entrada, $saida, $id_usuario_logado
            ]);

            echo '<div class="alert alert-success" role="alert">Informações cadastradas com sucesso!</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Este BP já existe no sistema, tente um diferente!</div>';
        }
        exit;
    } catch(PDOException $e) {
        error_log("Erro no cadastro de equipamento: " . $e->getMessage());
        echo '<div class="alert alert-danger" role="alert">Erro ao processar o cadastro. Tente novamente.</div>';
        exit;
    }
}
?>

<link rel="stylesheet" href="../style/css/combobox.css"/>
<script>
$(document).ready(function() {
    // Inicializa o combobox
    $("#combobox").combobox();
    
    // Inicializa o multiselect
    $(".sel").multiselect({
        buttonClass: 'btn btn-sm', 
        numberDisplayed: 1,
        maxHeight: 500,
        includeSelectAllOption: true,
        selectAllText: "Selecionar todos",
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        selectAllValue: 'multiselect-all',
        buttonWidth: '100%'
    });
    
    // Máscara para o campo de patrimônio
    $(".placa").mask("aaa-*999");
});
</script>

<?php
// Carregamento dinâmico de obras
if(isset($_GET['atu']) && $_GET['atu'] == 'ac') {
    $obra_2 = isset($_GET['obra_2']) ? intval($_GET['obra_2']) : 0;
    
    echo '<label style="width:100%">Contrato:<br/>
          <select name="obraInput" class="form-control input-sm" style="width:100%" required>';
    
    $obras = $con->prepare("SELECT * FROM notas_obras WHERE cidade IN(?) AND id <> 0 ORDER BY descricao ASC");
    $obras->execute([$obra_2]);
    
    while($a = $obras->fetch()) {
        echo '<option value="'.$a['id'].'" selected>'.$a['descricao'].'</option>';
    }
    echo '</select></label>';
    exit;
}

// Carregamento dinâmico de sub-categorias
if(isset($_GET['atu']) && $_GET['atu'] == 'categoria') {
    $categoria = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;
    
    echo '<label style="width:100%"><small>Sub-Categoria</small>
          <select name="sub_categoria" style="width:100%" class="form-control input-sm">';
    
    $stms = $con->prepare("SELECT * FROM notas_cat_sub WHERE associada IN(?) ORDER BY descricao ASC");
    $stms->execute([$categoria]);
    
    while($l = $stms->fetch()) {
        echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>';
    }
    echo '</select></label>';
    exit;
}
?>

<div class="ajax"></div>

<div class="container-fluid" style="padding:0px">
    <form action="javascript:void(0)" onSubmit="post(this,'almoxarifado/cadastro-equipamentos.php?ac=up','.ajax')" enctype="multipart/form-data" class="formulario-info">
        <div class="panel">
            <div class="panel-body" style="width:100%">    
                <div class="col-xs-12 col-sm-4">
                    <div class="col-xs-6">
                        <label style="width:100%">Nº Motor: <br/> <input type="text" name="placa" class="form-control input-sm"></label>
                    </div>
                    <div class="col-xs-6">
                        <label style="width:100%">BP: <br/><input type="text" name="patrimonio" autocomplete="off" class="form-control input-sm up placa" required /></label>
                    </div>

                    <div class="col-xs-6">
                        <label style="width:100%">Marca: </br><input type="text" name="marca" class="form-control input-sm up" required></label>
                    </div>
                    <div class="col-xs-6">
                        <label style="width:100%">Nº Apolise: <br><input type="text" name="patrimonio2" class="form-control input-sm"></label>
                    </div>
                    <div class="col-xs-6">
                        <label style="width:100%">Valor: <br><input type="number" step="0.1" name="valor" class="form-control input-sm" required></label>
                    </div>
                    <div class="col-xs-6">
                        <label style="width:100%">Seguro: <br>
                            <select name="seguro" class="form-control input-sm" required>
                                <option value="" disabled>Selecione uma opção</option>
                                <option value="SIM">SIM</option>
                                <option value="NAO">NÃO</option>
                            </select>
                        </label>
                    </div>
                    <div class="col-xs-6">
                        <label style="width:100%">Nota: <br><input type="number" name="desconto" step="0.01" class="form-control input-sm" required /></label>
                    </div>
                    <div class="col-xs-6">
                        <label style="width:100%">Ano: <br><input type="text" name="ano" class="form-control input-sm" required></label>
                    </div>
                    <div class="col-xs-6">
                        <label style="width:100%">Chassi / Nº série: <br><input style="width:100%;" type="text" name="chassi" class="form-control input-sm up" required></label>
                    </div>
                </div>
        
                <div class="col-xs-12 col-sm-4">
                    <div class="col-xs-12">
                        <label style="width:100%">Empresa: <br>
                            <select id="combobox" name="empresa" class="form-control input-sm" required>
                                <option value="">SEM EMPRESA</option>
                                <?php 
                                    $empresasql = $con->query("SELECT * FROM localferramentas_cadastroempresa WHERE tipo_empresa IN(1,2) ORDER BY razao_social ASC");
                                    while($l = $empresasql->fetch()) {
                                        echo '<option value="'.$l['id'].'">'.$l['razao_social'].'</option>';
                                    }
                                ?>            
                            </select>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label style="width:100%"> Categoria: 
                            <select name="categoria" onChange="$('#itens23').load('../functions/functions-load.php?atu=categoria&control=1&categoria=' + $(this).val() + '');" style="width:100%" class="form-control input-sm" required>
                                <option value="0">SELECIONE UMA CATEGORIA</option>
                                <?php 
                                    $stms = $con->query("SELECT * FROM notas_cat_e WHERE oculto = '0' ORDER BY descricao ASC");
                                    while($l = $stms->fetch()){
                                        echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>'; 
                                    }
                                ?>        
                            </select>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label id="itens23" style="width:100%">Sub-Categoria:
                            <label style="width:100%">
                                <select name="sub_categoria" style="width:100%" class="form-control input-sm" required>
                                    <option value="" selected disabled>Selecione uma categoria</option>
                                </select>
                            </label>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label style="width:100%">Fornecedor: <br><input type="text" name="obs" class="form-control input-sm" disabled /></label>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-4">
                    <div class="col-xs-12">
                        <label style="width:100%">Obra:<br/>
                            <select name="cidade" onChange="$('#itens-obra').load('almoxarifado/cadastro-equipamentos.php?atu=ac&obra_2=' + $(this).val() + '');" class="input-sm form-control" style="width:100%" required>
                                <?php 
                                    $obras_consulta = $con->query("SELECT * FROM notas_obras_cidade WHERE id IN($cidade_usuario) AND id <> 0 ORDER BY nome ASC");
                                    while($l = $obras_consulta->fetch()) {
                                        echo '<option value="'.$l['id'].'">'.$l['nome'].'</option>'; 
                                    }
                                ?>    
                            </select>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label style="width:100%" id="itens-obra">
                            <label style="width:100%">Contrato:<br/>
                                <select name="obraInput" class="form-control input-sm" style="width:100%" required>
                                    <?php 
                                        $obras_consulta = $con->query("SELECT * FROM notas_obras WHERE id IN($obra_usuario) AND cidade IN($cidade_usuario) AND id <> 0 ORDER BY descricao ASC");
                                        while($l = $obras_consulta->fetch()) {
                                            echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>'; 
                                        }
                                    ?>    
                                </select>
                            </label>
                        </label>
                    </div>
                    <div class="col-xs-12">
                        <label style="width:100%">Tipo: <br>
                            <select name="situacao" class="form-control input-sm" required>
                                <option value="" selected disabled>00 - SEM TIPO </option>
                                <?php 
                                    $situacaosql = $con->query("SELECT * FROM notas_eq_situacao WHERE status = '0' AND id <> '0' ORDER BY descricao ASC");
                                    while($l = $situacaosql->fetch()) {
                                        echo '<option value="'.$l['id'].'">'.$l['descricao'].'</option>'; 
                                    }
                                ?>            
                            </select>
                        </label>
                    </div>
                    <div class="col-xs-6">
                        <label>Entrada: <br><input type="date" name="entrada" value="<?php echo date('Y-m-d') ?>" class="form-control input-sm"></label>
                    </div>
                    <div class="col-xs-6">
                        <label>Saída: <br><input type="date" name="saida" class="form-control input-sm"></label><br/>
                    </div>
                    <div class="col-xs-12" style="text-align:center">
                        <label style="width:50%">
                            <input type="submit" style="width:100%; height:30px; margin-top:20px" value="Atualizar" class="btn btn-info btn-sm">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>