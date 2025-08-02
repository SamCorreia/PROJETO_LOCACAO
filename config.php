<?php

// Configurações de sessão segura (DEVEM vir antes de session_start())
//ini_set('session.use_only_cookies', 1);
//ini_set('session.cookie_httponly', 1);
//ini_set('session.cookie_secure', isset($_SERVER['HTTPS'])); // Ativa somente em HTTPS
//ini_set('session.cookie_samesite', 'Strict');
//ini_set('session.gc_maxlifetime', 1800); // 30 minutos
//ini_set('session.use_strict_mode', 1);
//ini_set('session.hash_function', 'sha256');

// Nome personalizado para a sessão
//session_name('LITORALRENT_SESSID');

// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers de segurança reforçados (depois de iniciar a sessão)
header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
if(isset($_SERVER['HTTPS'])) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// Configurações regionais
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_MONETARY, "pt_BR", "ptb");

class DBConnection extends PDO
{
    private static $instance = null;

    public function __construct()
    {
        // Configurações do banco em variáveis locais
        $DBhost = "127.0.0.1";
        $DBname = "u662866198_ferramentas";
        $DBuser = "u662866198_localferrament";
        $DBpass = "9l~H73!0ki";
        
        try {
            parent::__construct(
                "mysql:host=$DBhost;dbname=$DBname;charset=utf8mb4",
                $DBuser, 
                $DBpass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
                ]
            );
        } catch (PDOException $e) {
            // Log seguro sem expor detalhes sensíveis
            error_log("[" . date('Y-m-d H:i:s') . "] Erro DB: " . preg_replace('/\s+/', ' ', $e->getMessage()));
            
            // Mensagem genérica para o usuário
            if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
                die("Erro de conexão: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
            } else {
                die("Sistema temporariamente indisponível. Por favor, tente novamente em alguns minutos.");
            }
        }
    }

    // Padrão Singleton para reutilizar a conexão
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Previne clonagem da conexão
    public function __clone()
    {
        throw new Exception("Clonagem da conexão com o banco não é permitida.");
    }
}

// Função para sanitizar inputs
function sanitizeInput($data)
{
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Define constantes de ambiente
define('ENVIRONMENT', 'development'); // Mude para 'production' em produção
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Regenera o ID da sessão periodicamente para segurança
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // A cada 30 minutos
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
?>