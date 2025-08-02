<?php
// Configuração de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/debug.log');

// Inclusão de arquivos e inicialização
require_once('../../config.php');
require_once('../../functions.php');

// Conexão com banco de dados e verificação de login
$con = new DBConnection();
verificaLogin(); 
getData();

// Definir variáveis padrão
$todayTotal = date('Y-m-d');

// Verificação de CNPJ/CPF existente (busca-rgi)
if(isset($_GET['up']) && $_GET['up'] == 'busca-rgi' && isset($_GET['busca'])) {
    $busca = $_GET['busca'];
    $stm = $con->prepare("SELECT * FROM localferramentas_cadastroempresa WHERE cnpj = ?");
    $stm->execute([$busca]);
    
    while($b = $stm->fetch()) {
        echo '<script>
            alert("Empresa já se encontra cadastrada em nosso sistema!!!");
            $("#razao_social").val("'.htmlspecialchars($b['razao_social'], ENT_QUOTES).'");
            $("#telefone").val("'.htmlspecialchars($b['telefone'], ENT_QUOTES).'");
            $("#celular").val("'.htmlspecialchars($b['celular'], ENT_QUOTES).'");
            $("#contato").val("'.htmlspecialchars($b['contato'], ENT_QUOTES).'");
            $("#email").val("'.htmlspecialchars($b['email'], ENT_QUOTES).'");
            $("#endereco").val("'.htmlspecialchars($b['endereco'], ENT_QUOTES).'");
            $("#seguimento").val("'.htmlspecialchars($b['seguimento'], ENT_QUOTES).'");
            $("#data_retorno").val("'.htmlspecialchars($b['data_retorno'], ENT_QUOTES).'");
            $("#visita").val("'.htmlspecialchars($b['visita'], ENT_QUOTES).'");
        </script>'; 
    }        
    exit;
}

// Processamento do formulário de cadastro
if(isset($_POST['ac'])) {
    // Sanitização das entradas
    $tipo_empresa = isset($_POST['tipo_empresa']) ? (int)$_POST['tipo_empresa'] : 0;
    $cnpj = isset($_POST['cnpj']) ? preg_replace('/[^0-9]/', '', $_POST['cnpj']) : '';
    $razao_social = isset($_POST['razao_social']) ? htmlspecialchars($_POST['razao_social'], ENT_QUOTES) : '';
    $telefone = isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone'], ENT_QUOTES) : '';
    $celular = isset($_POST['celular']) ? htmlspecialchars($_POST['celular'], ENT_QUOTES) : '';
    $contato = isset($_POST['contato']) ? htmlspecialchars($_POST['contato'], ENT_QUOTES) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $endereco = isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco'], ENT_QUOTES) : '';
    $seguimento = isset($_POST['seguimento']) ? htmlspecialchars($_POST['seguimento'], ENT_QUOTES) : '';
    $data_retorno = isset($_POST['data_retorno']) && $_POST['data_retorno'] != '' ? $_POST['data_retorno'] : '0001-01-01';
    $obs = isset($_POST['obs']) ? htmlspecialchars($_POST['obs'], ENT_QUOTES) : '';
    $visita = isset($_POST['visita']) ? $_POST['visita'] : $todayTotal;

    // Verifica se empresa já existe
    $stm = $con->prepare("SELECT COUNT(*) FROM localferramentas_cadastroempresa WHERE cnpj = ?");
    $stm->execute([$cnpj]);
    $count = $stm->fetchColumn();
    
    if($count != 0) {
        echo '<div class="alert alert-danger">
                <h4>Empresa já cadastrada!</h4>
                <p>O CPF/CNPJ: '.htmlspecialchars($cnpj).' já está cadastrado no sistema, favor consultar as empresas já cadastradas e tentar novamente!!!</p>
              </div>';
    } else {
        // Cadastra nova empresa
        $query = $con->prepare("INSERT INTO localferramentas_cadastroempresa 
                              (tipo_empresa, cnpj, razao_social, telefone, celular, contato, email, endereco, seguimento, data_retorno, obs, visita, data_cadastro) 
                              VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        
        $result = $query->execute([
            $tipo_empresa, $cnpj, $razao_social, $telefone, $celular, $contato, 
            $email, $endereco, $seguimento, $data_retorno, $obs, $visita, $todayTotal
        ]);
        
        if($result) {
            echo '<div class="alert alert-success">
                    <h4>Empresa Cadastrada!</h4>
                    <p>O CPF/CNPJ: '.htmlspecialchars($cnpj).' de nome '.htmlspecialchars($razao_social).' foi cadastrado com sucesso no sistema!!!</p>
                  </div>';
        } else {
            echo '<div class="alert alert-danger">
                    <h4>Erro ao cadastrar!</h4>
                    <p>Ocorreu um erro ao tentar cadastrar a empresa. Por favor, tente novamente.</p>
                  </div>';
        }
    }
    
    echo '<script>
            $("html, body").animate({ scrollTop: $("#alert1").offset().top }, "slow");
          </script>';
    exit;
}
?>

<section class="content-header" id="alert1">
    <h1>Cadastro Empresa <small></small></h1>
</section>
<section class="content">
    <div class="resultadoCadastro"></div>
    <div class="box box-primary" style="padding-top:10px">
        <form action="javascript:void(0)" onSubmit="post(this,'financeiro/cadastro-empresa.php?ac=ins','.resultadoCadastro')">
            <div class="box-body">
                <div class="form-group">
                    <label>Tipo Empresa:</label>
                    <select name="tipo_empresa" onChange="$('#itens_empresa').load('../functions/functions-load.php?atu=cadastroEmpresa&tipo_empresa=' + $(this).val() + '');" class="form-control input-sm" required>
                        <option value="0">CLIENTE</option>
                        <option value="1">FORNECEDOR</option>
                        <option value="2">CLIENTE / FORNECEDOR</option>
                        <option value="3">PESSOA FISICA</option>
                    </select>
                </div>
                <div id="itens_empresa">
                    <div class="form-group">
                        <label>CNPJ:</label>
                        <input type="text" name="cnpj" onblur="$('#autoco').load('financeiro/cadastro-empresa.php?up=busca-rgi&busca=' + $(this).val() + '');" onfocus="$(this).mask('99.999.999/9999-99')" placeholder="__.___.___/____-__" class="juridica form-control input-sm" required />
                        <div id="autoco"></div>
                    </div>
                    <div class="form-group">
                        <label>Razão Social:</label>
                        <input type="text" name="razao_social" id="razao_social" placeholder="Nome da Empresa" size="80" class="todosInput form-control input-sm" required />
                    </div>
                </div>
                <div class="form-group">
                    <label>Telefone:</label>
                    <input type="text" name="telefone" id="telefone" onfocus="$(this).mask('(99) 9999-9999')" placeholder="(__) ____-____" size="80" class="todosInput form-control input-sm">
                </div>
                <div class="form-group">
                    <label>Celular:</label>
                    <input type="text" name="celular" id="celular" onfocus="$(this).mask('(99) 99999999?9')" placeholder="(__) _________" size="80" class="todosInput form-control input-sm">
                </div>
                <div class="form-group">
                    <label>Representante:</label>
                    <input type="text" name="contato" id="contato" placeholder="Responsável Legal" size="80" class="todosInput form-control input-sm">
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" id="email" placeholder="E-mail para contato" size="80" class="todosInput form-control input-sm">
                </div>
                <div class="form-group">
                    <label>Endereço:</label>
                    <input type="text" name="endereco" id="endereco" placeholder="Endereço completo" size="80" class="todosInput form-control input-sm" required />
                </div>
                <div class="form-group">
                    <label>Seguimento:</label>
                    <input type="text" name="seguimento" id="seguimento" placeholder="Seguimento" size="80" class="todosInput form-control input-sm">
                </div>
                <div class="form-group">
                    <label>Data Retorno:</label>
                    <input type="date" name="data_retorno" id="data_retorno" class="todosInput form-control input-sm" />
                </div>
                <div class="form-group">
                    <label>Visita:</label>
                    <input type="text" name="visita" value="<?= htmlspecialchars($todayTotal) ?>" id="visita" class="todosInput form-control input-sm">
                </div>
                <div class="form-group">
                    <label>Observações:</label>
                    <textarea name="obs" class="todosInput form-control input-sm"></textarea>
                </div>
                <div class="box-footer" style="text-align:center">
                    <input type="submit" style="width:50%" class="btn btn-success btn-sm submit-empresa" value="Salvar">
                </div>
            </div>
        </form>
    </div>
</section>