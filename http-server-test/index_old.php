<?php
// -------------------- CONFIGURATION --------------------
session_start();

// Initialize session counters
if (!isset($_SESSION['page_count'])) {
    $_SESSION['page_count'] = 0;
}
$_SESSION['page_count']++;

// Generate unique session ID for tracking
if (!isset($_SESSION['tracking_id'])) {
    $_SESSION['tracking_id'] = uniqid('session_', true);
}

// -------------------- HELPER FUNCTIONS --------------------
function getServerMetrics() {
    return [
        'hostname' => gethostname(),
        'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'N/A',
        'server_port' => $_SERVER['SERVER_PORT'] ?? 'N/A',
        'current_time' => date('Y-m-d H:i:s'),
        'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
        'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
        'cpu_load' => sys_getloadavg()[0] ?? 0,
        'page_count' => $_SESSION['page_count'],
        'tracking_id' => $_SESSION['tracking_id']
    ];
}

function getProxyHeaders() {
    return [
        'X-Forwarded-For' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
        'X-Forwarded-Proto' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '',
        'X-Forwarded-Port' => $_SERVER['HTTP_X_FORWARDED_PORT'] ?? '',
        'Forwarded' => $_SERVER['HTTP_FORWARDED'] ?? '',
        'X-Real-IP' => $_SERVER['HTTP_X_REAL_IP'] ?? ''
    ];
}

function getHTTPVersion() {
    $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    
    // Check for HTTP/2
    if (isset($_SERVER['HTTP2']) && $_SERVER['HTTP2'] == 'on') {
        return 'HTTP/2';
    }
    
    // Check protocol string
    if (strpos($protocol, '2') !== false) {
        return 'HTTP/2';
    }
    
    // Check for HTTP/3 indicators
    if (isset($_SERVER['HTTP3']) || isset($_SERVER['QUIC'])) {
        return 'HTTP/3';
    }
    
    return $protocol;
}

function getSSLInfo() {
    $ssl_info = [
        'enabled' => false,
        'protocol' => 'N/A',
        'cipher' => 'N/A',
        'session_id' => 'N/A',
        'session_reused' => 'N/A'
    ];
    
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $ssl_info['enabled'] = true;
        $ssl_info['protocol'] = $_SERVER['SSL_PROTOCOL'] ?? 'N/A';
        $ssl_info['cipher'] = $_SERVER['SSL_CIPHER'] ?? 'N/A';
        $ssl_info['session_id'] = $_SERVER['SSL_SESSION_ID'] ?? 'N/A';
        $ssl_info['session_reused'] = $_SERVER['SSL_SESSION_RESUMED'] ?? 'N/A';
    }
    
    return $ssl_info;
}

function getMTLSInfo() {
    $mtls_info = [
        'enabled' => false,
        'client_verified' => 'N/A',
        'client_cert_subject' => 'N/A',
        'client_cert_issuer' => 'N/A',
        'client_cert_serial' => 'N/A',
        'client_cert_fingerprint' => 'N/A'
    ];
    
    if (isset($_SERVER['SSL_CLIENT_VERIFY'])) {
        $mtls_info['enabled'] = true;
        $mtls_info['client_verified'] = $_SERVER['SSL_CLIENT_VERIFY'];
        $mtls_info['client_cert_subject'] = $_SERVER['SSL_CLIENT_S_DN'] ?? 'N/A';
        $mtls_info['client_cert_issuer'] = $_SERVER['SSL_CLIENT_I_DN'] ?? 'N/A';
        $mtls_info['client_cert_serial'] = $_SERVER['SSL_CLIENT_M_SERIAL'] ?? 'N/A';
        $mtls_info['client_cert_fingerprint'] = $_SERVER['SSL_CLIENT_CERT_FINGERPRINT'] ?? 'N/A';
    }
    
    return $mtls_info;
}

$metrics = getServerMetrics();
$http_version = getHTTPVersion();
$ssl_info = getSSLInfo();
$mtls_info = getMTLSInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Advanced PHP / HAProxy Test Suite v2</title>
<style>
* { box-sizing: border-box; }
body { background: #1e1e1e; color: #c5c5c5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin:0; padding:0; }
header { padding:20px; border-bottom:2px solid #333; background:#2b2b2b; box-shadow:0 2px 5px rgba(0,0,0,0.5); }
header h1 { margin:0; color:#00ffff; font-size:1.8em; }
.header-line { margin-top:8px; font-size:0.9em; }
.container { max-width: 1400px; margin: 0 auto; padding: 20px; }
.tabs { display: flex; background: #2b2b2b; border-bottom: 2px solid #444; margin-bottom: 20px; flex-wrap: wrap; }
.tab { padding: 12px 20px; cursor: pointer; background: #333; border: none; color: #c5c5c5; transition: all 0.3s; border-right: 1px solid #444; font-size: 0.9em; }
.tab:hover { background: #444; }
.tab.active { background: #6272a4; color: #f8f8f2; }
.tab-content { display: none; }
.tab-content.active { display: block; }
.section { border:1px solid #444; margin:10px 0; border-radius:5px; box-shadow:0 2px 5px rgba(0,0,0,0.5); background: #252525; }
.section-header { padding:12px 15px; cursor:pointer; background:#333; border-bottom:1px solid #444; display: flex; justify-content: space-between; align-items: center; }
.section-header:hover { background:#444; }
.section-content { padding:15px; display:none; background:#1e1e1e; }
.section-icon { transition: transform 0.3s; }
.key { color:#ffb86c; font-weight:bold; }
.value { color:#8be9fd; }
pre { background:#2b2b2b; padding:15px; border-radius:5px; overflow:auto; margin: 10px 0; }
.form-group { margin: 15px 0; }
.form-group label { display: block; margin-bottom: 5px; color: #ffb86c; font-weight: bold; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; background: #2b2b2b; border: 1px solid #444; color: #c5c5c5; border-radius: 3px; font-family: monospace; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #6272a4; }
.btn { background:#6272a4; color:#f8f8f2; border:none; padding:10px 20px; cursor:pointer; border-radius:5px; font-size:1em; transition: all 0.3s; }
.btn:hover { background:#7888b4; }
.btn:disabled { background:#444; cursor: not-allowed; }
.btn-danger { background: #ff5555; }
.btn-danger:hover { background: #ff6b6b; }
.btn-success { background: #50fa7b; color: #1e1e1e; }
.btn-success:hover { background: #69ff94; }
.btn-warning { background: #f1fa8c; color: #1e1e1e; }
.btn-warning:hover { background: #ffff9d; }
.grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
.card { background: #2b2b2b; padding: 15px; border-radius: 5px; border: 1px solid #444; }
.progress-bar { width: 100%; height: 30px; background: #2b2b2b; border-radius: 5px; overflow: hidden; margin: 10px 0; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #6272a4, #8be9fd); transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; }
.log { background: #1e1e1e; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 0.85em; margin: 10px 0; }
.log-entry { padding: 5px; border-bottom: 1px solid #333; }
.log-entry.success { color: #50fa7b; }
.log-entry.error { color: #ff5555; }
.log-entry.info { color: #8be9fd; }
.log-entry.warning { color: #f1fa8c; }
.cookie-list { margin-top: 10px; }
.cookie-item { background: #333; padding: 10px; margin: 5px 0; border-radius: 3px; display: flex; justify-content: space-between; align-items: center; }
.inline-group { display: flex; gap: 10px; align-items: flex-end; }
.inline-group .form-group { flex: 1; margin: 0; }
.status-indicator { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; }
.status-indicator.connected { background: #50fa7b; box-shadow: 0 0 8px #50fa7b; }
.status-indicator.disconnected { background: #ff5555; }
.status-indicator.connecting { background: #f1fa8c; animation: pulse 1s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.header-row { display: flex; justify-content: space-between; align-items: center; margin: 5px 0; padding: 5px; background: #333; border-radius: 3px; }
.badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 0.85em; font-weight: bold; }
.badge.http1 { background: #6272a4; color: #fff; }
.badge.http2 { background: #50fa7b; color: #1e1e1e; }
.badge.http3 { background: #ff79c6; color: #1e1e1e; }
.badge.ssl { background: #50fa7b; color: #1e1e1e; }
.badge.no-ssl { background: #ff5555; color: #fff; }
</style>
</head>
<body>
<header>
<h1>🚀 Advanced PHP / HAProxy Test Suite v2</h1>
<div class="header-line">
    <span class="key">Server:</span> <span class="value"><?=htmlspecialchars($metrics['hostname'])?> | IP: <?=htmlspecialchars($metrics['server_ip'])?> | Port: <?=htmlspecialchars($metrics['server_port'])?></span>
    <span class="badge <?=strtolower(str_replace(['/', '.'], '', $http_version))?>"><?=$http_version?></span>
    <?php if ($ssl_info['enabled']): ?>
        <span class="badge ssl">🔒 <?=$ssl_info['protocol']?></span>
    <?php else: ?>
        <span class="badge no-ssl">No SSL</span>
    <?php endif; ?>
</div>
<div class="header-line"><span class="key">Time:</span> <span class="value"><?=$metrics['current_time']?></span></div>
<div class="header-line"><span class="key">Client IP:</span> <span class="value"><?=htmlspecialchars($metrics['client_ip'])?></span></div>
<div class="header-line"><span class="key">Session ID:</span> <span class="value"><?=htmlspecialchars($metrics['tracking_id'])?></span> | <span class="key">Page served:</span> <span class="value"><?=$metrics['page_count']?> times</span> | <span class="key">Memory:</span> <span class="value"><?=$metrics['memory_usage']?></span></div>
</header>

<div class="container">
    <div class="tabs">
        <button class="tab active" data-tab="info">📊 Server Info</button>
        <button class="tab" data-tab="download">⬇️ Download</button>
        <button class="tab" data-tab="upload">⬆️ Upload</button>
        <button class="tab" data-tab="stress">🔥 Stress Test</button>
        <button class="tab" data-tab="websocket">🔌 WebSocket</button>
        <button class="tab" data-tab="headers">📋 Headers</button>
        <button class="tab" data-tab="sticky">🔄 Session Sticky</button>
        <button class="tab" data-tab="parallel">⚡ Parallel</button>
        <button class="tab" data-tab="health">💚 Health Check</button>
        <button class="tab" data-tab="cookies">🍪 Cookies</button>
        <button class="tab" data-tab="useragent">🌐 User-Agent</button>
    </div>

    <!-- SERVER INFO TAB -->
    <div id="info" class="tab-content active">
        <?php include 'sections/server-info-sections.php'; ?>
    </div>

    <!-- DOWNLOAD TEST TAB -->
    <div id="download" class="tab-content">
        <div class="card">
            <h2>Download Speed Test</h2>
            <p>Generate and download a file of specified size to test download speeds and server performance.</p>
            
            <div class="form-group">
                <label>Download Size</label>
                <select id="downloadSize">
                    <option value="1">1 MB</option>
                    <option value="10">10 MB</option>
                    <option value="50">50 MB</option>
                    <option value="100" selected>100 MB</option>
                    <option value="500">500 MB</option>
                    <option value="1024">1 GB</option>
                </select>
            </div>

            <div class="form-group">
                <label>File Type</label>
                <select id="downloadType">
                    <option value="random">Random Data</option>
                    <option value="zeros">Zeros (Compressible)</option>
                    <option value="text">Text Data</option>
                </select>
            </div>

            <button class="btn" onclick="startDownload()">Start Download</button>
            
            <div class="progress-bar" style="display:none;" id="downloadProgress">
                <div class="progress-fill" id="downloadProgressFill">0%</div>
            </div>
            
            <div id="downloadLog" class="log" style="display:none;"></div>
        </div>
    </div>

    <!-- UPLOAD TEST TAB -->
    <div id="upload" class="tab-content">
        <div class="card">
            <h2>Upload Speed Test</h2>
            <p>Generate and upload data of specified size to test upload speeds and server handling.</p>
            
            <div class="form-group">
                <label>Upload Size</label>
                <select id="uploadSize">
                    <option value="1">1 MB</option>
                    <option value="10" selected>10 MB</option>
                    <option value="50">50 MB</option>
                    <option value="100">100 MB</option>
                </select>
            </div>

            <div class="form-group">
                <label>Chunk Size (KB)</label>
                <input type="number" id="chunkSize" value="1024" min="64" max="4096">
            </div>

            <button class="btn" onclick="startUpload()">Start Upload</button>
            
            <div class="progress-bar" style="display:none;" id="uploadProgress">
                <div class="progress-fill" id="uploadProgressFill">0%</div>
            </div>
            
            <div id="uploadLog" class="log" style="display:none;"></div>
        </div>
    </div>

    <!-- STRESS TEST TAB -->
    <div id="stress" class="tab-content">
        <div class="card">
            <h2>HTTP Stress Test</h2>
            <p>Send multiple concurrent HTTP requests to test server capacity and load balancing.</p>
            
            <div class="grid">
                <div class="form-group">
                    <label>Number of Requests</label>
                    <input type="number" id="stressRequests" value="100" min="1" max="10000">
                </div>

                <div class="form-group">
                    <label>Concurrent Connections</label>
                    <input type="number" id="stressConcurrent" value="10" min="1" max="100">
                </div>

                <div class="form-group">
                    <label>Request Method</label>
                    <select id="stressMethod">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="HEAD">HEAD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Target Endpoint</label>
                    <input type="text" id="stressEndpoint" value="api/stress-test.php" placeholder="/api/endpoint">
                </div>
            </div>

            <button class="btn" onclick="startStressTest()" id="stressBtn">Start Stress Test</button>
            <button class="btn btn-danger" onclick="stopStressTest()" id="stopStressBtn" style="display:none;">Stop Test</button>
            
            <div class="progress-bar" style="display:none;" id="stressProgress">
                <div class="progress-fill" id="stressProgressFill">0%</div>
            </div>
            
            <div id="stressStats" style="display:none; margin-top: 15px;">
                <h3>Statistics</h3>
                <div class="grid">
                    <div><span class="key">Total Requests:</span> <span class="value" id="statTotal">0</span></div>
                    <div><span class="key">Successful:</span> <span class="value" id="statSuccess">0</span></div>
                    <div><span class="key">Failed:</span> <span class="value" id="statFailed">0</span></div>
                    <div><span class="key">Avg Response Time:</span> <span class="value" id="statAvgTime">0ms</span></div>
                </div>
            </div>
            
            <div id="stressLog" class="log" style="display:none;"></div>
        </div>
    </div>

    <!-- WEBSOCKET TEST TAB -->
    <div id="websocket" class="tab-content">
        <div class="card">
            <h2>WebSocket Connection Test</h2>
            <p>Test WebSocket connectivity through your proxy/load balancer.</p>
            
            <div class="form-group">
                <label>WebSocket URL</label>
                <input type="text" id="wsUrl" value="wss://echo.websocket.org/" placeholder="ws://your-server:port or wss://...">
                <small style="color: #8be9fd; display: block; margin-top: 5px;">Default uses public echo server for testing</small>
            </div>

            <div style="margin: 15px 0;">
                <span class="key">Status:</span> 
                <span class="status-indicator disconnected" id="wsStatus"></span>
                <span id="wsStatusText">Disconnected</span>
            </div>

            <button class="btn" onclick="wsConnect()" id="wsConnectBtn">Connect</button>
            <button class="btn btn-danger" onclick="wsDisconnect()" id="wsDisconnectBtn" style="display:none;">Disconnect</button>

            <div style="margin-top: 20px;">
                <h3>Send Message</h3>
                <div class="inline-group">
                    <div class="form-group" style="flex: 3;">
                        <input type="text" id="wsMessage" placeholder="Type message to send..." disabled>
                    </div>
                    <button class="btn" onclick="wsSendMessage()" id="wsSendBtn" disabled>Send</button>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <h3>Connection Info</h3>
                <div id="wsInfo" class="card" style="background: #1e1e1e;">
                    <pre id="wsInfoContent">Not connected</pre>
                </div>
            </div>

            <div id="wsLog" class="log" style="display:block; margin-top: 15px;"></div>
        </div>
    </div>

    <!-- HEADERS MANIPULATION TAB -->
    <div id="headers" class="tab-content">
        <div class="card">
            <h2>Custom Request Headers</h2>
            <p>Add custom headers to test how they're handled by your server and proxy.</p>
            
            <div id="headersList"></div>

            <h3 style="margin-top: 20px;">Add New Header</h3>
            <div class="inline-group">
                <div class="form-group">
                    <label>Header Name</label>
                    <input type="text" id="headerName" placeholder="X-Custom-Header">
                </div>
                <div class="form-group">
                    <label>Header Value</label>
                    <input type="text" id="headerValue" placeholder="value">
                </div>
                <button class="btn" onclick="addCustomHeader()">Add Header</button>
            </div>

            <div style="margin-top: 20px;">
                <button class="btn" onclick="testCustomHeaders()">Test Request with Custom Headers</button>
                <button class="btn btn-warning" onclick="clearCustomHeaders()">Clear All</button>
            </div>

            <div id="headersLog" class="log" style="display:none; margin-top: 15px;"></div>
        </div>
    </div>

    <!-- SESSION STICKINESS TAB -->
    <div id="sticky" class="tab-content">
        <div class="card">
            <h2>Session Stickiness Validator</h2>
            <p>Test if load balancer maintains session stickiness across multiple requests.</p>
            
            <div class="form-group">
                <label>Number of Requests</label>
                <input type="number" id="stickyRequests" value="20" min="5" max="100">
            </div>

            <div class="form-group">
                <label>Test Endpoint</label>
                <input type="text" id="stickyEndpoint" value="api/sticky-test.php">
            </div>

            <button class="btn" onclick="testStickiness()">Test Session Stickiness</button>

            <div id="stickyResults" style="display:none; margin-top: 20px;">
                <h3>Results</h3>
                <div class="grid">
                    <div><span class="key">Requests Sent:</span> <span class="value" id="stickyTotal">0</span></div>
                    <div><span class="key">Unique Servers:</span> <span class="value" id="stickyServers">0</span></div>
                    <div><span class="key">Stickiness:</span> <span class="value" id="stickyStatus">-</span></div>
                    <div><span class="key">Session Cookie:</span> <span class="value" id="stickyCookie">-</span></div>
                </div>
                <div style="margin-top: 15px;">
                    <h4>Server Distribution:</h4>
                    <div id="stickyDistribution"></div>
                </div>
            </div>

            <div id="stickyLog" class="log" style="display:none;"></div>
        </div>
    </div>

    <!-- PARALLEL CONNECTIONS TAB -->
    <div id="parallel" class="tab-content">
        <div class="card">
            <h2>Parallel Connection Test</h2>
            <p>Open multiple connections simultaneously to test connection limits and proxy behavior.</p>
            
            <div class="grid">
                <div class="form-group">
                    <label>Number of Connections</label>
                    <input type="number" id="parallelCount" value="10" min="1" max="100">
                </div>

                <div class="form-group">
                    <label>Hold Time (seconds)</label>
                    <input type="number" id="parallelHoldTime" value="5" min="1" max="60">
                </div>

                <div class="form-group">
                    <label>Request Type</label>
                    <select id="parallelType">
                        <option value="http">HTTP (Fetch)</option>
                        <option value="xhr">XMLHttpRequest</option>
                        <option value="img">Image Loading</option>
                    </select>
                </div>
            </div>

            <button class="btn" onclick="testParallelConnections()" id="parallelBtn">Start Test</button>
            <button class="btn btn-danger" onclick="stopParallelTest()" id="parallelStopBtn" style="display:none;">Stop</button>

            <div id="parallelStats" style="display:none; margin-top: 15px;">
                <h3>Connection Status</h3>
                <div class="grid">
                    <div><span class="key">Active:</span> <span class="value" id="parallelActive">0</span></div>
                    <div><span class="key">Completed:</span> <span class="value" id="parallelCompleted">0</span></div>
                    <div><span class="key">Failed:</span> <span class="value" id="parallelFailed">0</span></div>
                    <div><span class="key">Max Concurrent:</span> <span class="value" id="parallelMax">0</span></div>
                </div>
            </div>

            <div id="parallelLog" class="log" style="display:none;"></div>
        </div>
    </div>

    <!-- HEALTH CHECK TAB -->
    <div id="health" class="tab-content">
        <div class="card">
            <h2>Health Check Simulator</h2>
            <p>Simulate different health check responses to test load balancer behavior.</p>
            
            <div class="form-group">
                <label>Response Status Code</label>
                <select id="healthStatus">
                    <option value="200">200 OK</option>
                    <option value="204">204 No Content</option>
                    <option value="301">301 Moved Permanently</option>
                    <option value="302">302 Found</option>
                    <option value="400">400 Bad Request</option>
                    <option value="403">403 Forbidden</option>
                    <option value="404">404 Not Found</option>
                    <option value="500">500 Internal Server Error</option>
                    <option value="503">503 Service Unavailable</option>
                </select>
            </div>

            <div class="form-group">
                <label>Response Delay (ms)</label>
                <input type="number" id="healthDelay" value="0" min="0" max="10000" step="100">
            </div>

            <div class="form-group">
                <label>Response Body</label>
                <textarea id="healthBody" rows="3" placeholder="Optional response body...">{"status":"healthy"}</textarea>
            </div>

            <button class="btn" onclick="testHealthCheck()">Test Health Check</button>
            <button class="btn btn-success" onclick="startHealthMonitor()">Start Monitoring</button>
            <button class="btn btn-danger" onclick="stopHealthMonitor()" style="display:none;" id="healthStopBtn">Stop Monitoring</button>

            <div id="healthStats" style="display:none; margin-top: 15px;">
                <h3>Health Check Statistics</h3>
                <div class="grid">
                    <div><span class="key">Total Checks:</span> <span class="value" id="healthTotal">0</span></div>
                    <div><span class="key">Successful:</span> <span class="value" id="healthSuccess">0</span></div>
                    <div><span class="key">Failed:</span> <span class="value" id="healthFailed">0</span></div>
                    <div><span class="key">Avg Response:</span> <span class="value" id="healthAvg">0ms</span></div>
                </div>
            </div>

            <div id="healthLog" class="log" style="display:none;"></div>
        </div>
    </div>

    <!-- COOKIE MANAGER TAB -->
    <div id="cookies" class="tab-content">
        <div class="card">
            <h2>Cookie Manager</h2>
            <p>Create, modify, and delete cookies for testing session management and cookie handling.</p>
            
            <div class="inline-group">
                <div class="form-group">
                    <label>Cookie Name</label>
                    <input type="text" id="cookieName" placeholder="test_cookie">
                </div>

                <div class="form-group">
                    <label>Cookie Value</label>
                    <input type="text" id="cookieValue" placeholder="cookie_value">
                </div>

                <div class="form-group">
                    <label>Expires (days)</label>
                    <input type="number" id="cookieExpires" value="7" min="0" max="365">
                </div>

                <button class="btn" onclick="setCookie()">Set Cookie</button>
            </div>

            <h3 style="margin-top: 20px;">Current Cookies</h3>
            <div id="cookieList" class="cookie-list"></div>
            
            <button class="btn btn-danger" onclick="clearAllCookies()" style="margin-top: 15px;">Clear All Cookies</button>
        </div>
    </div>

    <!-- USER-AGENT TAB -->
    <div id="useragent" class="tab-content">
        <div class="card">
            <h2>User-Agent Spoofing</h2>
            <p>Change your browser's user-agent string to test server behavior with different clients.</p>
            
            <div class="form-group">
                <label>Current User-Agent</label>
                <textarea id="currentUA" rows="3" readonly><?=htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'N/A')?></textarea>
            </div>

            <div class="form-group">
                <label>Select Preset</label>
                <select id="uaPreset" onchange="loadUAPreset()">
                    <option value="">-- Select a preset --</option>
                    <option value="chrome-win">Chrome (Windows)</option>
                    <option value="chrome-mac">Chrome (macOS)</option>
                    <option value="firefox-win">Firefox (Windows)</option>
                    <option value="safari-mac">Safari (macOS)</option>
                    <option value="safari-ios">Safari (iOS)</option>
                    <option value="chrome-android">Chrome (Android)</option>
                    <option value="edge">Edge (Windows)</option>
                    <option value="bot-google">Googlebot</option>
                    <option value="bot-bing">Bingbot</option>
                    <option value="curl">cURL</option>
                </select>
            </div>

            <div class="form-group">
                <label>Custom User-Agent</label>
                <textarea id="customUA" rows="3" placeholder="Enter custom user-agent string..."></textarea>
            </div>

            <button class="btn" onclick="testUserAgent()">Test Request with This User-Agent</button>
            
            <div id="uaLog" class="log" style="display:none;"></div>
        </div>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>