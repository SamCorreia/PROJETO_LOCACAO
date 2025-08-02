<?php 
// Controle de sessão seguro
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once('config.php');
$con = new DBConnection();

/**
 * Verifica o login do usuário via cookie ou sessão
 */
function verificaLogin(){
    global $con, $nome_usuario, $obra_usuario, $cidade_usuario, $login_usuario, 
           $id_usuario_logado, $acesso_usuario, $status, $nivel_acesso, 
           $editarss_usuario, $tipo_home, $tipo_login, $ip_usuario;
    
    // Verificação por cookies
    if(isset($_COOKIE['user']) && isset($_COOKIE['password'])){
        $stat = $con->prepare("SELECT * FROM usuarios WHERE login = ? AND senha = ? LIMIT 1");
        $stat->execute([$_COOKIE['user'], $_COOKIE['password']]);
        
        if($stat->rowCount() == 1){
            $row = $stat->fetch(PDO::FETCH_ASSOC);
            carregaDadosUsuario($row);
            $status = true;
        }
    } else {
        // Verificação por sessão
        if(isset($_SESSION['login_usuario']) && isset($_SESSION['senha_usuario'])){
            $stat = $con->prepare("SELECT * FROM usuarios WHERE login = ? AND senha = ? LIMIT 1");
            $stat->execute([$_SESSION['login_usuario'], $_SESSION['senha_usuario']]);
            
            if($stat->rowCount() == 1){
                $row = $stat->fetch(PDO::FETCH_ASSOC);
                carregaDadosUsuario($row);
                $status = true;
            } else {
                session_destroy();
                $status = false;
                redirecionaParaLogin();
            }
        } else {
            $status = false;
            redirecionaParaLogin();            
        }    
    }
}

/**
 * Carrega os dados do usuário na sessão
 */
function carregaDadosUsuario($row) {
    global $nome_usuario, $obra_usuario, $cidade_usuario, $login_usuario, 
           $id_usuario_logado, $acesso_usuario, $nivel_acesso, $editarss_usuario, 
           $tipo_home, $tipo_login, $ip_usuario, $con;
    
    $nome_usuario = $row['nome'] ?? '';
    $login_usuario = $row['login'] ?? '';
    $obra_usuario = $row['obra'] ?? '';
    $cidade_usuario = $row['cidade'] ?? 'Não informada';
    $nivel_acesso = $row['nivel_acesso'] ?? '0';
    $acesso_usuario = $row['acesso_login'] ?? date('Y-m-d H:i:s');
    $id_usuario_logado = $row['id'] ?? 0;
    $editarss_usuario = $row['editarss'] ?? 0;
    $tipo_home = $row['tipo_home'] ?? 'padrão';
    $tipo_login = $row['tipo_login'] ?? 'normal';
    $ip_usuario = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    // Atualiza último login
    try {
        $atu = $con->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
        $atu->execute([$id_usuario_logado]);
    } catch (PDOException $e) {
        error_log("Erro ao atualizar último login: " . $e->getMessage());
    }
    
    // Carrega permissões do usuário
    getNivel();
}

/**
 * Redireciona para a página de login
 */
function redirecionaParaLogin() {
    echo "<script>window.location='../index.php';</script>";
    exit();
}

/**
 * Obtém informações de data/hora
 */
function getData(){
    global $today, $todayTotal, $inicioMes, $mes_nome, $meses_numero, 
           $hora_view, $data_view, $ano_view, $dia_view;
    
    $today = getdate(); 
    $mon = str_pad($today['mon'], 2, '0', STR_PAD_LEFT);
    $mday = str_pad($today['mday'], 2, '0', STR_PAD_LEFT);
    
    $todayTotal = "{$today['year']}-{$mon}-{$mday}";
    $inicioMes = "{$today['year']}-{$mon}-01";
    $data_view = date("d/m/Y");
    $hora_view = date("H:i:s");
    $ano_view = $today['year'];
    $dia_view = $mday;
    
    $meses_numero = array(
        1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril",
        5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto",
        9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"
    );
    
    switch($mon){
        case '01': $mes_nome = 'JANEIRO'; break;
        case '02': $mes_nome = 'FEVEREIRO'; break;
        case '03': $mes_nome = 'MARÇO'; break;
        case '04': $mes_nome = 'ABRIL'; break;
        case '05': $mes_nome = 'MAIO'; break;
        case '06': $mes_nome = 'JUNHO'; break;
        case '07': $mes_nome = 'JULHO'; break;
        case '08': $mes_nome = 'AGOSTO'; break;
        case '09': $mes_nome = 'SETEMBRO'; break;
        case '10': $mes_nome = 'OUTUBRO'; break;
        case '11': $mes_nome = 'NOVEMBRO'; break;
        case '12': $mes_nome = 'DEZEMBRO'; break;
        default: $mes_nome = 'INDEFINIDO';
    }
}

/**
 * Carrega os níveis de acesso do usuário
 */
function getNivel(){
    global $nivel_acesso, $nivel_acesso_array, 
           $financeiro_array, $logistica_array, $equipamento_array, 
           $compras_array, $consulta_array, $gestor_array;
    
    // Reset de todas as permissões
    $financeiro_array = $logistica_array = $equipamento_array = false;
    $compras_array = $consulta_array = $gestor_array = false;
    
    if (!empty($nivel_acesso)) {
        // Se for 'admin' ou 'superadmin', concede todas as permissões
        if ($nivel_acesso == 'admin' || $nivel_acesso == 'superadmin') {
            $gestor_array = true;
            $financeiro_array = true;
            $logistica_array = true;
            $equipamento_array = true;
            $compras_array = true;
            $consulta_array = true;
            $_SESSION['acesso_total'] = true; // Flag de acesso total
        }
        // Se for numérico (sistema antigo)
        elseif (is_numeric($nivel_acesso) || strpos($nivel_acesso, ',') !== false) {
            $niveis = str_replace(' ', '', $nivel_acesso);
            $nivel_acesso_array = explode(",", $niveis);
            
            foreach($nivel_acesso_array as $key_acesso){
                switch($key_acesso){
                    case '1': $financeiro_array = true; break;
                    case '2': $logistica_array = true; break;
                    case '3': $equipamento_array = true; break;
                    case '4': $compras_array = true; break;
                    case '5': $consulta_array = true; break;
                    case '6': 
                        $gestor_array = true;
                        // Garante todas as permissões para gerentes
                        $financeiro_array = $logistica_array = $equipamento_array = true;
                        $compras_array = $consulta_array = true;
                        break;
                }
            }
        }
    }
}

/**
 * Verifica se o usuário tem determinada permissão
 */
function temPermissao($permissao) {
    global $gestor_array, $nivel_acesso;
    
    // Admin/Superadmin tem acesso a tudo
    if ($nivel_acesso == 'admin' || $nivel_acesso == 'superadmin') {
        return true;
    }
    
    // Gerente tem acesso a quase tudo
    if ($gestor_array && $permissao != 'superadmin') {
        return true;
    }
    
    switch($permissao) {
        case 'financeiro': return $GLOBALS['financeiro_array'] ?? false;
        case 'logistica': return $GLOBALS['logistica_array'] ?? false;
        case 'equipamentos': return $GLOBALS['equipamento_array'] ?? false;
        case 'compras': return $GLOBALS['compras_array'] ?? false;
        case 'consulta': return $GLOBALS['consulta_array'] ?? false;
        case 'gerente': return $GLOBALS['gestor_array'] ?? false;
        case 'superadmin': return ($nivel_acesso == 'superadmin');
        default: return false;
    }
}

/**
 * Função para verificar acesso total (nova função)
 */
function temAcessoTotal() {
    return temPermissao('superadmin') || temPermissao('admin') || ($_SESSION['acesso_total'] ?? false);
}

/**
 * Função para debug (pode ser removida em produção)
 */
function debugPermissoes() {
    echo "<pre>Permissões do Usuário:\n";
    echo "Acesso Total: " . (temAcessoTotal() ? 'SIM' : 'NÃO') . "\n";
    echo "Financeiro: " . (temPermissao('financeiro') ? 'SIM' : 'NÃO') . "\n";
    echo "Logística: " . (temPermissao('logistica') ? 'SIM' : 'NÃO') . "\n";
    echo "Equipamentos: " . (temPermissao('equipamentos') ? 'SIM' : 'NÃO') . "\n";
    echo "Compras: " . (temPermissao('compras') ? 'SIM' : 'NÃO') . "\n";
    echo "Consulta: " . (temPermissao('consulta') ? 'SIM' : 'NÃO') . "\n";
    echo "Gerente: " . (temPermissao('gerente') ? 'SIM' : 'NÃO') . "\n";
    echo "Superadmin: " . (temPermissao('superadmin') ? 'SIM' : 'NÃO') . "\n";
    echo "</pre>";
}