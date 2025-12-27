<?php
// -------------------- SERVER INFO SECTIONS --------------------

function render_section($title, $content_html, $id = '') {
    $section_id = $id ?: 'section_' . md5($title);
    echo '<div class="section">';
    echo '<div class="section-header" data-section-id="'.$section_id.'" style="cursor:pointer;">';
    echo '<span>'.$title.'</span><span id="'.$section_id.'_icon" class="section-icon">▼</span>';
    echo '</div>';
    echo '<div class="section-content" id="'.$section_id.'">'.$content_html.'</div>';
    echo '</div>';
}

// -------------------- REQUEST DETAILS --------------------
$request_html = '<pre>';
$request_html .= "<strong>Method:</strong> " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
$request_html .= "<strong>URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
$request_html .= "<strong>Protocol:</strong> " . ($_SERVER['SERVER_PROTOCOL'] ?? 'N/A') . "\n\n";

$request_html .= "<strong>Query Params:</strong>\n";
if (!empty($_GET)) {
    $request_html .= htmlspecialchars(json_encode($_GET, JSON_PRETTY_PRINT)) . "\n";
} else {
    $request_html .= "None\n";
}

$request_html .= "\n<strong>Cookies:</strong>\n";
if (!empty($_COOKIE)) {
    $request_html .= htmlspecialchars(json_encode($_COOKIE, JSON_PRETTY_PRINT)) . "\n";
} else {
    $request_html .= "None\n";
}

$request_html .= "\n<strong>Request Headers:</strong>\n";
foreach (getallheaders() as $k => $v) {
    $request_html .= "<span class='key'>" . htmlspecialchars($k) . ":</span> <span class='value'>" . htmlspecialchars($v) . "</span>\n";
}
$request_html .= '</pre>';
render_section('📥 Request Details', $request_html);

// -------------------- SERVER INFO --------------------
$os_info = php_uname();
$uptime = @exec('uptime') ?: 'N/A';
$php_version = PHP_VERSION;
$apache_version = function_exists('apache_get_version') ? apache_get_version() : 'N/A';

$server_html = '<pre>';
$server_html .= "<strong>Operating System:</strong> $os_info\n";
$server_html .= "<strong>Server Uptime:</strong> $uptime\n";
$server_html .= "<strong>PHP Version:</strong> $php_version\n";
$server_html .= "<strong>Apache Version:</strong> $apache_version\n";
$server_html .= "<strong>SAPI:</strong> " . php_sapi_name() . "\n\n";

$server_html .= "<strong>Loaded PHP Extensions:</strong>\n";
$extensions = get_loaded_extensions();
sort($extensions);
$server_html .= implode(', ', array_map('htmlspecialchars', $extensions)) . "\n";
$server_html .= "</pre>";
render_section('🖥️ Server Info', $server_html);

// -------------------- PHP CONFIGURATION --------------------
$php_directives = [
    'memory_limit' => 'Memory Limit',
    'post_max_size' => 'Post Max Size',
    'upload_max_filesize' => 'Upload Max Filesize',
    'max_execution_time' => 'Max Execution Time',
    'max_input_time' => 'Max Input Time',
    'max_input_vars' => 'Max Input Vars',
    'display_errors' => 'Display Errors',
    'error_reporting' => 'Error Reporting',
    'date.timezone' => 'Timezone',
    'session.gc_maxlifetime' => 'Session Lifetime',
    'opcache.enable' => 'OPcache Enabled'
];

$php_html = '<pre>';
foreach ($php_directives as $directive => $label) {
    $value = ini_get($directive);
    $php_html .= "<span class='key'>$label:</span> <span class='value'>$value</span>\n";
}
$php_html .= '</pre>';
render_section('⚙️ PHP Configuration', $php_html);

// -------------------- PROXY & LOAD BALANCER INFO --------------------
$proxy_html = '<pre>';
$proxy_html .= "<strong>Proxy Headers:</strong>\n";
foreach (getProxyHeaders() as $k => $v) {
    $display_value = $v ?: 'Not set';
    $proxy_html .= "<span class='key'>$k:</span> <span class='value'>" . htmlspecialchars($display_value) . "</span>\n";
}

$proxy_html .= "\n<strong>HAProxy-Specific Headers:</strong>\n";
$haproxy_headers = [
    'HTTP_X_HAPROXY_SERVER' => 'HAProxy Server',
    'HTTP_X_HAPROXY_BACKEND' => 'HAProxy Backend',
    'HTTP_X_HAPROXY_SESSION' => 'HAProxy Session ID'
];

foreach ($haproxy_headers as $key => $label) {
    $value = $_SERVER[$key] ?? 'Not set';
    $proxy_html .= "<span class='key'>$label:</span> <span class='value'>" . htmlspecialchars($value) . "</span>\n";
}

$proxy_html .= '</pre>';
render_section('🔄 Proxy & Load Balancer Info', $proxy_html);

// -------------------- NETWORK INFO --------------------
$network_html = '<pre>';
$network_html .= "<span class='key'>Server IP:</span> <span class='value'>" . ($_SERVER['SERVER_ADDR'] ?? 'N/A') . "</span>\n";
$network_html .= "<span class='key'>Server Port:</span> <span class='value'>" . ($_SERVER['SERVER_PORT'] ?? 'N/A') . "</span>\n";
$network_html .= "<span class='key'>Remote IP:</span> <span class='value'>" . ($_SERVER['REMOTE_ADDR'] ?? 'N/A') . "</span>\n";
$network_html .= "<span class='key'>Remote Port:</span> <span class='value'>" . ($_SERVER['REMOTE_PORT'] ?? 'N/A') . "</span>\n";
$network_html .= "<span class='key'>Protocol:</span> <span class='value'>" . ($_SERVER['SERVER_PROTOCOL'] ?? 'N/A') . "</span>\n";
$network_html .= "<span class='key'>HTTPS:</span> <span class='value'>" . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'Yes' : 'No') . "</span>\n";
$network_html .= '</pre>';
render_section('🌐 Network Info', $network_html);

// -------------------- SSL/TLS INFO --------------------
$cert_html = '<pre>';
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    // Basic SSL/TLS Info
    $cert_html .= "<strong>=== Connection Info ===</strong>\n";
    $cert_html .= "<span class='key'>Protocol:</span> <span class='value'>" . ($_SERVER['SSL_PROTOCOL'] ?? 'N/A') . "</span>\n";
    $cert_html .= "<span class='key'>Cipher:</span> <span class='value'>" . ($_SERVER['SSL_CIPHER'] ?? 'N/A') . "</span>\n";
    $cert_html .= "<span class='key'>Cipher Strength:</span> <span class='value'>" . ($_SERVER['SSL_CIPHER_USEKEYSIZE'] ?? 'N/A') . " bits</span>\n";
    
    // Session Info
    $cert_html .= "\n<strong>=== Session Info ===</strong>\n";
    $cert_html .= "<span class='key'>Session ID:</span> <span class='value'>" . ($_SERVER['SSL_SESSION_ID'] ?? 'N/A') . "</span>\n";
    $cert_html .= "<span class='key'>Session Reused:</span> <span class='value'>" . ($_SERVER['SSL_SESSION_RESUMED'] ?? ($_SERVER['SSL_SESSION_ID'] ? 'No' : 'N/A')) . "</span>\n";
    
    // Server Certificate
    $cert_html .= "\n<strong>=== Server Certificate ===</strong>\n";
    $cert_html .= "<span class='key'>Subject DN:</span> <span class='value'>" . ($_SERVER['SSL_SERVER_S_DN'] ?? 'N/A') . "</span>\n";
    $cert_html .= "<span class='key'>Issuer DN:</span> <span class='value'>" . ($_SERVER['SSL_SERVER_I_DN'] ?? 'N/A') . "</span>\n";
    $cert_html .= "<span class='key'>Serial:</span> <span class='value'>" . ($_SERVER['SSL_SERVER_M_SERIAL'] ?? 'N/A') . "</span>\n";
    $cert_html .= "<span class='key'>Valid From:</span> <span class='value'>" . ($_SERVER['SSL_SERVER_V_START'] ?? 'N/A') . "</span>\n";
    $cert_html .= "<span class='key'>Valid To:</span> <span class='value'>" . ($_SERVER['SSL_SERVER_V_END'] ?? 'N/A') . "</span>\n";
    
    // mTLS / Client Certificate Info
    if (isset($_SERVER['SSL_CLIENT_VERIFY'])) {
        $cert_html .= "\n<strong>=== mTLS - Client Certificate ===</strong>\n";
        $cert_html .= "<span class='key'>Client Verified:</span> <span class='value'>" . $_SERVER['SSL_CLIENT_VERIFY'] . "</span>\n";
        
        if ($_SERVER['SSL_CLIENT_VERIFY'] === 'SUCCESS') {
            $cert_html .= "<span class='key'>Client Subject DN:</span> <span class='value'>" . ($_SERVER['SSL_CLIENT_S_DN'] ?? 'N/A') . "</span>\n";
            $cert_html .= "<span class='key'>Client Issuer DN:</span> <span class='value'>" . ($_SERVER['SSL_CLIENT_I_DN'] ?? 'N/A') . "</span>\n";
            $cert_html .= "<span class='key'>Client Serial:</span> <span class='value'>" . ($_SERVER['SSL_CLIENT_M_SERIAL'] ?? 'N/A') . "</span>\n";
            $cert_html .= "<span class='key'>Client Fingerprint:</span> <span class='value'>" . ($_SERVER['SSL_CLIENT_CERT_FINGERPRINT'] ?? 'N/A') . "</span>\n";
            $cert_html .= "<span class='key'>Client Valid From:</span> <span class='value'>" . ($_SERVER['SSL_CLIENT_V_START'] ?? 'N/A') . "</span>\n";
            $cert_html .= "<span class='key'>Client Valid To:</span> <span class='value'>" . ($_SERVER['SSL_CLIENT_V_END'] ?? 'N/A') . "</span>\n";
        } else {
            $cert_html .= "<span style='color: #ff5555;'>⚠ Client certificate verification failed</span>\n";
        }
    } else {
        $cert_html .= "\n<strong>=== mTLS Status ===</strong>\n";
        $cert_html .= "<span style='color: #f1fa8c;'>mTLS not enabled (no client certificate required)</span>\n";
    }
    
} else {
    $cert_html .= "⚠ Connection is not HTTPS. Certificate info not available.\n";
    $cert_html .= "Access this page via HTTPS to view SSL/TLS information.";
}
$cert_html .= '</pre>';
render_section('🔒 SSL/TLS & mTLS Info', $cert_html);

// -------------------- SESSION INFO --------------------
$session_html = '<pre>';
$session_html .= "<span class='key'>Session ID:</span> <span class='value'>" . session_id() . "</span>\n";
$session_html .= "<span class='key'>Session Name:</span> <span class='value'>" . session_name() . "</span>\n";
$session_html .= "<span class='key'>Session Save Path:</span> <span class='value'>" . session_save_path() . "</span>\n\n";
$session_html .= "<strong>Session Variables:</strong>\n";
$session_html .= htmlspecialchars(print_r($_SESSION, true));
$session_html .= '</pre>';
render_section('🔑 Session Information', $session_html);

// -------------------- ENVIRONMENT VARIABLES --------------------
$env_html = '<pre>';
$env_categories = [
    'Server' => ['SERVER_SOFTWARE', 'SERVER_NAME', 'SERVER_ADDR', 'SERVER_PORT', 'SERVER_PROTOCOL', 'DOCUMENT_ROOT', 'SERVER_ADMIN'],
    'Request' => ['REQUEST_METHOD', 'REQUEST_URI', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT', 'QUERY_STRING'],
    'Client' => ['REMOTE_ADDR', 'REMOTE_PORT', 'HTTP_USER_AGENT', 'HTTP_ACCEPT', 'HTTP_ACCEPT_LANGUAGE'],
    'Script' => ['SCRIPT_FILENAME', 'SCRIPT_NAME', 'PHP_SELF', 'GATEWAY_INTERFACE']
];

foreach ($env_categories as $category => $vars) {
    $env_html .= "<strong>$category:</strong>\n";
    foreach ($vars as $var) {
        $value = $_SERVER[$var] ?? 'N/A';
        $env_html .= "<span class='key'>$var:</span> <span class='value'>" . htmlspecialchars($value) . "</span>\n";
    }
    $env_html .= "\n";
}

$env_html .= "<strong>All Environment Variables:</strong>\n";
foreach ($_SERVER as $key => $value) {
    if (!is_array($value)) {
        $env_html .= "<span class='key'>$key:</span> <span class='value'>" . htmlspecialchars($value) . "</span>\n";
    }
}
$env_html .= '</pre>';
render_section('📋 Environment Variables', $env_html);
?>