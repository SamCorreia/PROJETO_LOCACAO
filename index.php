<?php 
// Define a constante ROOT_PATH para controle de acesso
define('ROOT_PATH', dirname(__FILE__));

session_start();
include("config.php");

// Modo debug - desative em produção
$debug_mode = true; // Mude para false em produção

if($debug_mode) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $debug_messages = []; // Armazenará mensagens de debug
}

try {
    $con = new DBConnection();
    
    if($debug_mode) {
        $debug_messages[] = "Conexão com o banco OK";
    }
} catch (PDOException $e) {
    if($debug_mode) {
        die("Erro de conexão: " . $e->getMessage());
    } else {
        die("<script>alert('Erro no sistema. Tente mais tarde.'); window.location='index.php';</script>");
    }
}

// PROCESSAMENTO DO FORMULÁRIO PRIMEIRO
if(isset($_POST["submit"])) {
    $user = preg_replace('/[^[:alnum:]_\-@.]/', '', $_POST['user']);
    $password = preg_replace('/[^[:alnum:]_]/', '', $_POST['password']);
    $md5password = md5($password);
    
    if($debug_mode) {
        $debug_messages[] = "Tentativa de login - Usuário: $user";
        $debug_messages[] = "Senha MD5: $md5password";
    }

    try {
        $stat = $con->prepare("SELECT id, login FROM usuarios WHERE login = ? AND senha = ?");
        $stat->execute([$user, $md5password]);
        
        if ($stat->rowCount() == 1) {
            $row = $stat->fetch(PDO::FETCH_ASSOC);
            
            $_SESSION['id_usuario'] = $row['id'];
            $_SESSION['login_usuario'] = $row['login'];
            $_SESSION['senha_usuario'] = $md5password;
            // Dentro do bloco de login bem-sucedido
            $_SESSION['usuario_nivel'] = $row['nivel_acesso']; // Garanta que está pegando o campo certo
            $_SESSION['is_admin'] = true; // Adicione esta flag
            
            if($debug_mode) {
                $debug_messages[] = "Autenticação bem-sucedida! Redirecionando...";
                $debug_messages[] = "Sessão criada com sucesso para: ".$_SESSION['login_usuario'];
            }
            
            // REDIRECIONAMENTO APÓS LOGIN BEM SUCEDIDO
            header("Location: restrito/index.php");
            exit();
            
            // Após o login bem-sucedido
        if ($_SESSION['usuario_nivel'] === 'administrador' || $_SESSION['nivel'] >= 3) {
           // Permite acesso às funções administrativas
        }
            
        } else {
            $error_message = '<div class="container" style="max-width: 600px; text-align:center; margin-top:20px; margin-bottom:auto; opacity:0.9">
                <div class="alert alert-danger">
                    <strong>Login inválido!</strong> Senha ou Login incorretos, tente novamente!.
                </div>
            </div>';
            
            if($debug_mode) {
                $checkUser = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE login = ?");
                $checkUser->execute([$user]);
                $userExists = $checkUser->fetchColumn();
                $debug_messages[] = "Usuário existe no banco: " . ($userExists ? 'Sim' : 'Não');
            }
        }
    } catch (PDOException $e) {
        $error_message = '<div class="container" style="max-width: 600px; text-align:center; margin-top:20px; margin-bottom:auto; opacity:0.9">
            <div class="alert alert-danger">
                <strong>Erro no sistema:</strong> Problema ao verificar credenciais.
            </div>
        </div>';
        
        if($debug_mode) {
            $debug_messages[] = "Erro PDO: " . $e->getMessage();
        }
        error_log("Erro de login: " . $e->getMessage());
    }
}

// VERIFICAÇÃO DE SESSÃO APÓS PROCESSAR O FORMULÁRIO
if(isset($_SESSION['id_usuario'])) {
    header("Location: ../restrito/index.php");
    exit();
}

// O RESTANTE DO SEU CÓDIGO HTML PERMANECE EXATAMENTE IGUAL
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Local Ferramentas - Sistema Administrativo</title>
    <meta name="author" content="Lucascorreia14@outlook.com.br">
    <link rel="stylesheet" href="style/css/login.css?v=<?php echo filemtime('style/css/login.css'); ?>">
    <link rel="icon" href="style/img/icone-litoralrent.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="style/img/imagens/icone-litoralrent.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="style/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="style/css/AdminLTE.min.css">
    <link rel="stylesheet" href="plugins/iCheck/square/green.css">
    <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Ubuntu:500' rel='stylesheet' type='text/css'>
    

    <!-- jQuery 2.2.4 (compatível com Bootstrap 3) -->
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

    <!-- Bootstrap 3 JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <?php if($debug_mode): ?>
    <style>
        .debug-panel {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.8);
            color: #fff;
            padding: 10px;
            font-family: monospace;
            font-size: 12px;
            z-index: 9999;
            max-height: 200px;
            overflow-y: auto;
        }
        .debug-toggle {
            position: fixed;
            bottom: 210px;
            right: 10px;
            background: #f00;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            z-index: 10000;
        }
    </style>
    <?php endif; ?>
</head>
<body class="wrapper">
    <?php if(isset($error_message)) echo $error_message; ?>
    
    <div class="background-login"></div>
    <div class="container-fluid">
        <div class="form-signin-2">
            <div class="formHeader">
                <h2 class="form-signin-heading-2"><center><img src="style/img/projetta-logo.png" alt="Logo Empresa" class="img-responsive" width="40%"/></center></h2>
            </div>
            <fieldset>
                <form method="post">
                    <div>
                        <div class="form-group">
                            <input class="form-control input-sm" type="text" name="user" placeholder="Usuario" required autofocus/>
                        </div>
                        <div class="form-group">
                            <input class="form-control input-sm" type="password" name="password" placeholder="Senha" required/>
                        </div>          
                        <div class="form-group remember-me">
                            <label class="container">
                                <input type="checkbox" class="lembrar" value="1" name="autologin"/> 
                                
                                <span class="titulo">
                                Lembrar senha</span>
                            </label>
                        </div>
                    </div>
                    <div class="footer">                                                               
                        <input class="btn btn-block btn-success" type="submit" name="submit" value="Entrar"/>
                    </div>
                </form>
            </fieldset>
        </div>
    </div>
    
    <footer class="pull-right" style="position:fixed; bottom:20px; text-align:center; width:100%; opacity:0.5; font-size:11px; color:#f3f3f3; letter-spacing:1.5px">
        <strong>Lucas Samuel | Todos direitos reservados. Copyright &copy; 2025</strong>
    </footer>

    <script src="plugins/jquery/dist/jquery.min.js"></script>
    <script src="plugins/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="plugins/iCheck/icheck.min.js"></script>
    <script>
        $(function () {
            $('.lembrar').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
                increaseArea: '20%'
            });
        });
    </script>
</body>
</html>