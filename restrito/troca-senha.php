<?php
	require('../config.php');
	require('../functions.php');
	$con = new DBConnection();
	verificaLogin();
	getNivel();

	// Processa o envio do formulário
	if (isset($_POST['senhaInput']) && isset($_POST['id_usuario'])) {
	    $senhaInput = $_POST['senhaInput'];
	    $nomeInput = $_POST['nomeInput'] ?? '';
	    $id_usuario = $_POST['id_usuario'];

	    $senha_crip = md5($senhaInput);
	    
	    try {
	        $stmt = $con->prepare("UPDATE `usuarios` SET senha = ?, nome = ? WHERE id = ?");
	        $stmt->execute([$senha_crip, $nomeInput, $id_usuario]);

	        // Exibe mensagem de sucesso e recarrega a página inicial
	        echo "<script>
                    alert('Senha trocada com sucesso!');
                    window.location.href='index.php';
                  </script>";
	        exit;
	    } catch (PDOException $e) {
	        echo 'Erro: ' . $e->getMessage();
	    }
	}

	// Caso não tenha enviado os dados, exibe o formulário
$stm = $con->prepare("select * from usuarios where id = ?");
$stm->execute(array($id_usuario_logado));
while ($x = $stm->fetch()) {
?>
<!-- Container para mensagens ou carregamento -->
<div class="resultadoCadastro"></div>

<!-- Formulário -->
<div class="trocasenha"></div>
<div class="row">
    <div class="col-md-6" style="float:none; margin: 0 auto;">
        <div class="panel panel-default">
            <div class="panel-heading">
                Dados do Usuário <span class="pull-right btn btn-xs btn-danger disabled"><?php echo strtoupper($acesso_usuario)?></span>
            </div>
            <div class="panel-body">
                <form id="formTrocaSenha" action="troca-senha.php" method="post">
                    <input type="hidden" name="id_usuario" value="<?php echo $x['id']; ?>"/>
                    <div class="col-md-12">
                        <label style="width:100%">Nome:<br/>
                            <input type="text" name="nomeInput" value="<?php echo $x['nome']; ?>" class="form-control input-sm" required />
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label style="width:100%">Login:<br/>
                            <input type="text" name="login" value="<?php echo $x['login']; ?>" class="form-control input-sm" disabled>
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label style="width:100%">Nova Senha:<br/>
                            <input type="password" name="senhaInput" class="form-control input-sm" autofocus required/>
                        </label>
                    </div>
                    <div class="col-md-12" style="text-align:center">
                        <br/>
                        <input type="submit" class="btn btn-success btn-sm" style="width:50%" value="Salvar Alterações"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
} // final do while
?>

<!-- Script para capturar o submit e usar sua função post() -->
<script>
// Aguarde o carregamento da página
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formTrocaSenha');
    // Evento de submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        // Chama sua função post() passando a URL e o FormData
        window.post('/restrito/troca-senha.php', formData);
    });
});
</script>