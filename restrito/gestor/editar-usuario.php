<?php
require_once('../../config.php');
require_once('../../functions.php');

// Configuração de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Inicialização segura
$con = new DBConnection();
verificaLogin();
$dadosUsuario = getData();
$acessoUsuario = $dadosUsuario['acesso'] ?? null;

// Inicialização de variáveis para evitar notices
$id = $_GET['id'] ?? 0;
$nome = $rg = $cargo = $telefone = $login = $status = $obra = $cidade = $editarss = $nivel_acesso = '';
$nivel_acesso2 = $nivel_acesso3 = [];

// Processamento AJAX para carregar contratos
if(isset($_GET['atu']) && $_GET['atu'] == 'ac') {
    $obra_2 = filter_input(INPUT_GET, 'obra_2', FILTER_SANITIZE_STRING);
    $obras = $con->query("SELECT * FROM notas_obras WHERE cidade IN($obra_2) AND id <> 0 ORDER BY descricao ASC");
    
    echo '<label style="width:100%">CONTRATO:<br/>
            <select name="ob[]" class="sel" style="width:100%" multiple="multiple" required>';
    while($a = $obras->fetch()) {
        echo '<option value="'.$a['id'].'" selected>'.htmlspecialchars($a['descricao']).'</option>';
    }
    echo '</select></label>';
    exit;
}

// Processamento do formulário de atualização
if(isset($_GET['ac']) && $_GET['ac'] == 'update') {
    // Filtragem dos inputs
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $rgInput = filter_input(INPUT_POST, 'rgInput', FILTER_SANITIZE_STRING);
    $cargo = filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_STRING);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $loginInput = filter_input(INPUT_POST, 'loginInput', FILTER_SANITIZE_STRING);
    $status22 = filter_input(INPUT_POST, 'status22', FILTER_SANITIZE_STRING);
    $editarss = filter_input(INPUT_POST, 'editarss', FILTER_SANITIZE_STRING);
    $acesso_usuarioInput = filter_input(INPUT_POST, 'acesso_usuarioInput', FILTER_SANITIZE_STRING);
    
    // Processamento seguro dos arrays
    $ob = isset($_POST['ob']) ? array_map('intval', $_POST['ob']) : [];
    $ci = isset($_POST['ci']) ? array_map('intval', $_POST['ci']) : [];
    $nivel_acesso2 = isset($_POST['nivel_acesso2']) ? array_map('intval', $_POST['nivel_acesso2']) : [];
    
    $obu = implode(',', $ob);
    $ciu = implode(',', $ci);
    $nivel_acesso3 = implode(',', $nivel_acesso2);

    try {
        $query = $con->prepare("UPDATE `usuarios` SET 
            `nome` = :nome, 
            `rg` = :rg, 
            `cargo` = :cargo, 
            `telefone` = :telefone, 
            `login` = :login, 
            `status` = :status, 
            `obra` = :obra, 
            `cidade` = :cidade, 
            `editarss` = :editarss, 
            `nivel_acesso` = :nivel_acesso, 
            `acesso_login` = :acesso_login 
            WHERE id = :id");
            
        $result = $query->execute([
            ':nome' => $nome,
            ':rg' => $rgInput,
            ':cargo' => $cargo,
            ':telefone' => $telefone,
            ':login' => $loginInput,
            ':status' => $status22,
            ':obra' => $obu,
            ':cidade' => $ciu,
            ':editarss' => $editarss,
            ':nivel_acesso' => $nivel_acesso3,
            ':acesso_login' => $acesso_usuarioInput,
            ':id' => $id
        ]);

        echo $result 
            ? '<div class="alert alert-success" role="alert">Informações atualizadas com sucesso!</div>'
            : '<div class="alert alert-danger" role="alert">Erro ao atualizar.</div>';
    } catch (PDOException $e) {
        error_log("Erro ao atualizar usuário: " . $e->getMessage());
        echo '<div class="alert alert-danger" role="alert">Erro no sistema. Tente novamente.</div>';
    }
    exit;    
}

// Carregar dados do usuário
$stm = $con->prepare("SELECT * FROM usuarios WHERE id = :id");
$stm->execute([':id' => $id]);
$b = $stm->fetch();

if($b) {
    $nome = htmlspecialchars($b['nome']);
    $rg = htmlspecialchars($b['rg']);
    $cargo = htmlspecialchars($b['cargo']);
    $telefone = htmlspecialchars($b['telefone']);
    $login = htmlspecialchars($b['login']);
    $status = $b['status'];
    $obra = $b['obra'];
    $cidade = $b['cidade'];
    $editarss = $b['editarss'];
    $nivel_acesso = $b['nivel_acesso'];
    $acesso_usuario = $b['acesso_login'];
}
?>

<script>
$(function () {
    $('.sel').multiselect({
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
});
</script>

<style>
.nivel_acesso_class input[type="checkbox"] {
    display:none;
}
.nivel_acesso_class input[type="checkbox"] + label {
    color:#333;
    margin:10px;
}
.nivel_acesso_class input[type="checkbox"] + label span {
    display:inline-block;
    width:29px;
    height:19px;
    margin:-2px 10px 0 0;
    vertical-align:middle;
    background:#f3f3f3;
    border:1px solid #ccc;
    cursor:pointer;
}
.nivel_acesso_class input[type="checkbox"]:checked + label span {
    background:#5CB85C;
    border:1px solid #ccc;
}
</style>

<div class="ajax" style="width:100%; text-align:center;"></div>
<div class="panel panel-default" style="border:none">
    <div class="panel-body">
        <form action="javascript:void(0)" onSubmit="post(this,'gestor/editar-usuario.php?ac=update&id=<?php echo $id ?>','.ajax');" class="small">
            <div class="col-md-12">
                <div class="col-xs-6">
                    <label style="width:100%">Login:<input type="text" name="loginInput" value="<?php echo $login ?>" class="form-control input-sm" size="10"></label>
                </div>
                <div class="col-xs-6">
                    <label style="width:100%">Status:
                        <select name="status22" class="form-control input-sm">
                            <option value="0" <?= $status == '0' ? 'selected' : '' ?>>ATIVO</option>
                            <option value="1" <?= $status == '1' ? 'selected' : '' ?>>INATIVO</option>
                        </select>
                    </label>
                </div>
                <div class="col-xs-12">
                    <label style="width:100%">Nome:<input type="text" name="nome" value="<?= $nome ?>" class="form-control input-sm up" required /></label><br>
                </div>
                <div class="col-xs-4">
                    <label style="width:100%">R.G.:<input type="text" name="rgInput" value="<?= $rg ?>" class="form-control input-sm up" required /></label><br>
                </div>
                <div class="col-xs-4">
                    <label style="width:100%">Cargo:<input type="text" name="cargo" value="<?= $cargo ?>" class="form-control input-sm up" required /></label><br>
                </div>
                <div class="col-xs-4">
                    <label style="width:100%">Telefone:<input type="text" name="telefone" value="<?= $telefone ?>" class="form-control input-sm up" required /></label><br>
                </div>
                <div class="col-xs-6">
                    <label style="width:100%">Obra:<br/>
                        <select name="ci[]" onChange="$('#itens').load('gestor/editar-usuario.php?atu=ac&obra_2=' + $(this).val() + '');" class="sel" style="width:100%" multiple="multiple" required>
                            <?php 
                                $obras_consulta = $con->query("SELECT * FROM notas_obras_cidade WHERE id IN($cidade) AND id <> 0 ORDER BY nome ASC");
                                while($l = $obras_consulta->fetch()) {
                                    echo '<option value="'.$l['id'].'" selected>'.htmlspecialchars($l['nome']).'</option>'; 
                                }
                                
                                $obras_consulta = $con->query("SELECT * FROM notas_obras_cidade WHERE id NOT IN($cidade) AND id <> 0 ORDER BY nome ASC");
                                while($l = $obras_consulta->fetch()) {
                                    echo '<option value="'.$l['id'].'">'.htmlspecialchars($l['nome']).'</option>';  
                                }
                            ?>  
                        </select>
                    </label>
                </div>
                <div class="col-xs-6">
                    <label style="width:100%" id="itens">
                        <label style="width:100%">Contrato:<br/>
                            <select name="ob[]" class="sel" style="width:100%" multiple="multiple" required>
                                <?php 
                                    $obras_consulta = $con->query("SELECT * FROM notas_obras WHERE id IN($obra) AND cidade IN($cidade) AND id <> 0 ORDER BY descricao ASC");
                                    while($l = $obras_consulta->fetch()) {
                                        echo '<option value="'.$l['id'].'" selected>'.htmlspecialchars($l['descricao']).'</option>'; 
                                    }

                                    $obras_consulta = $con->query("SELECT * FROM notas_obras WHERE id NOT IN($obra) AND cidade IN($cidade) AND id <> 0 ORDER BY descricao ASC");
                                    while($l = $obras_consulta->fetch()) {
                                        echo '<option value="'.$l['id'].'">'.htmlspecialchars($l['descricao']).'</option>';
                                    }
                                ?>  
                            </select>
                        </label>
                    </label>
                </div>
                
                <!-- Seção de nível de acesso -->
                <div class="col-xs-12">
                    <label style="width:100%">Nível de Acesso:</label>
                    <div class="nivel_acesso_class">
                        <?php
                        $niveis = [
                            'MASTER' => 'Master',
                            'EQUIPAMENTOS' => 'Equipamentos',
                            'FINANCEIRO' => 'Financeiro',
                            'RH' => 'Recursos Humanos'
                        ];
                        
                        $niveisUsuario = explode(',', $nivel_acesso);
                        
                        foreach($niveis as $valor => $label): 
                            $checked = in_array($valor, $niveisUsuario) ? 'checked' : '';
                        ?>
                            <input type="checkbox" name="nivel_acesso2[]" id="nivel_<?= $valor ?>" value="<?= $valor ?>" <?= $checked ?>>
                            <label for="nivel_<?= $valor ?>">
                                <span></span><?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="col-xs-12" style="margin-top:20px;">
                    <input type="submit" value="Atualizar" class="btn btn-success btn-sm">
                </div>
            </div>
        </form>
    </div>
</div>