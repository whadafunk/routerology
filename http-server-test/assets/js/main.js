// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', function() {
    console.log('Server Test Suite initialized');
    initializeTabs();
    initializeSections();
    updateCookieList();
    updateHeadersList();
});

// ==================== TAB MANAGEMENT ====================
function initializeTabs() {
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

// ==================== SECTION TOGGLE ====================
function initializeSections() {
    document.querySelectorAll('.section-header').forEach(header => {
        header.addEventListener('click', function() {
            const sectionId = this.getAttribute('data-section-id');
            toggleSection(sectionId);
        });
    });
}

function toggleSection(id) {
    const content = document.getElementById(id);
    const icon = document.getElementById(id + '_icon');
    
    if (!content) return;
    
    if (content.style.display === 'block') {
        content.style.display = 'none';
        if (icon) icon.textContent = '▼';
    } else {
        content.style.display = 'block';
        if (icon) icon.textContent = '▲';
    }
}

// ==================== DOWNLOAD TEST ====================
function startDownload() {
    const size = document.getElementById('downloadSize').value;
    const type = document.getElementById('downloadType').value;
    
    const progressBar = document.getElementById('downloadProgress');
    const progressFill = document.getElementById('downloadProgressFill');
    const log = document.getElementById('downloadLog');
    
    progressBar.style.display = 'block';
    log.style.display = 'block';
    log.innerHTML = '';
    
    addLog(log, 'info', `Starting download of ${size}MB (${type})...`);
    
    const startTime = Date.now();
    const url = `api/download.php?size=${size}&type=${type}`;
    
    fetch(url)
        .then(response => {
            const reader = response.body.getReader();
            const contentLength = response.headers.get('Content-Length');
            let receivedLength = 0;
            const chunks = [];
            
            return reader.read().then(function processChunk({ done, value }) {
                if (done) {
                    const elapsed = ((Date.now() - startTime) / 1000).toFixed(2);
                    const speed = (size / elapsed).toFixed(2);
                    addLog(log, 'success', `Download complete! Time: ${elapsed}s, Speed: ${speed} MB/s`);
                    progressFill.style.width = '100%';
                    progressFill.textContent = '100%';
                    
                    const blob = new Blob(chunks);
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = `test_${size}MB_${type}.bin`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(downloadUrl);
                    
                    return;
                }
                
                chunks.push(value);
                receivedLength += value.length;
                
                if (contentLength) {
                    const progress = (receivedLength / contentLength * 100).toFixed(0);
                    progressFill.style.width = progress + '%';
                    progressFill.textContent = progress + '%';
                }
                
                return reader.read().then(processChunk);
            });
        })
        .catch(error => {
            addLog(log, 'error', 'Download failed: ' + error.message);
        });
}

// ==================== UPLOAD TEST ====================
function startUpload() {
    const size = parseInt(document.getElementById('uploadSize').value);
    const chunkSize = parseInt(document.getElementById('chunkSize').value) * 1024;
    
    const progressBar = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('uploadProgressFill');
    const log = document.getElementById('uploadLog');
    
    progressBar.style.display = 'block';
    log.style.display = 'block';
    log.innerHTML = '';
    
    addLog(log, 'info', `Starting upload of ${size}MB with ${chunkSize/1024}KB chunks...`);
    
    const startTime = Date.now();
    const totalBytes = size * 1024 * 1024;
    let uploadedBytes = 0;
    
    function uploadChunk() {
        if (uploadedBytes >= totalBytes) {
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(2);
            const speed = (size / elapsed).toFixed(2);
            addLog(log, 'success', `Upload complete! Time: ${elapsed}s, Speed: ${speed} MB/s`);
            return;
        }
        
        const currentChunkSize = Math.min(chunkSize, totalBytes - uploadedBytes);
        const chunk = new Uint8Array(currentChunkSize);
        
        for (let i = 0; i < currentChunkSize; i++) {
            chunk[i] = Math.floor(Math.random() * 256);
        }
        
        const formData = new FormData();
        formData.append('chunk', new Blob([chunk]));
        formData.append('uploaded', uploadedBytes);
        formData.append('total', totalBytes);
        
        fetch('api/upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            uploadedBytes += currentChunkSize;
            const progress = (uploadedBytes / totalBytes * 100).toFixed(0);
            progressFill.style.width = progress + '%';
            progressFill.textContent = progress + '%';
            
            uploadChunk();
        })
        .catch(error => {
            addLog(log, 'error', 'Upload failed: ' + error.message);
        });
    }
    
    uploadChunk();
}

// ==================== STRESS TEST ====================
let stressTestRunning = false;
let stressTestStats = { total: 0, success: 0, failed: 0, times: [] };

function startStressTest() {
    const totalRequests = parseInt(document.getElementById('stressRequests').value);
    const concurrent = parseInt(document.getElementById('stressConcurrent').value);
    const method = document.getElementById('stressMethod').value;
    const endpoint = document.getElementById('stressEndpoint').value;
    
    const progressBar = document.getElementById('stressProgress');
    const progressFill = document.getElementById('stressProgressFill');
    const log = document.getElementById('stressLog');
    const stats = document.getElementById('stressStats');
    
    stressTestRunning = true;
    document.getElementById('stressBtn').style.display = 'none';
    document.getElementById('stopStressBtn').style.display = 'inline-block';
    
    progressBar.style.display = 'block';
    log.style.display = 'block';
    stats.style.display = 'block';
    log.innerHTML = '';
    
    stressTestStats = { total: 0, success: 0, failed: 0, times: [] };
    
    addLog(log, 'info', `Starting stress test: ${totalRequests} requests, ${concurrent} concurrent...`);
    
    let completedRequests = 0;
    let requestQueue = totalRequests;
    
    function makeRequest() {
        if (!stressTestRunning || requestQueue <= 0) return;
        
        requestQueue--;
        const startTime = Date.now();
        
        fetch(endpoint, { method: method })
            .then(response => {
                const time = Date.now() - startTime;
                stressTestStats.times.push(time);
                
                if (response.ok) {
                    stressTestStats.success++;
                } else {
                    stressTestStats.failed++;
                }
                
                return response.text();
            })
            .catch(error => {
                stressTestStats.failed++;
            })
            .finally(() => {
                completedRequests++;
                stressTestStats.total = completedRequests;
                
                updateStressStats();
                
                const progress = (completedRequests / totalRequests * 100).toFixed(0);
                progressFill.style.width = progress + '%';
                progressFill.textContent = `${progress}% (${completedRequests}/${totalRequests})`;
                
                if (completedRequests >= totalRequests) {
                    finishStressTest();
                } else if (stressTestRunning) {
                    makeRequest();
                }
            });
    }
    
    for (let i = 0; i < Math.min(concurrent, totalRequests); i++) {
        makeRequest();
    }
}

function stopStressTest() {
    stressTestRunning = false;
    document.getElementById('stressBtn').style.display = 'inline-block';
    document.getElementById('stopStressBtn').style.display = 'none';
    addLog(document.getElementById('stressLog'), 'info', 'Stress test stopped by user.');
}

function finishStressTest() {
    stressTestRunning = false;
    document.getElementById('stressBtn').style.display = 'inline-block';
    document.getElementById('stopStressBtn').style.display = 'none';
    addLog(document.getElementById('stressLog'), 'success', 'Stress test completed!');
}

function updateStressStats() {
    document.getElementById('statTotal').textContent = stressTestStats.total;
    document.getElementById('statSuccess').textContent = stressTestStats.success;
    document.getElementById('statFailed').textContent = stressTestStats.failed;
    
    if (stressTestStats.times.length > 0) {
        const avg = stressTestStats.times.reduce((a, b) => a + b, 0) / stressTestStats.times.length;
        document.getElementById('statAvgTime').textContent = avg.toFixed(0) + 'ms';
    }
}

// ==================== WEBSOCKET TEST ====================
let ws = null;
let wsConnected = false;

function wsConnect() {
    const url = document.getElementById('wsUrl').value;
    const log = document.getElementById('wsLog');
    const statusIndicator = document.getElementById('wsStatus');
    const statusText = document.getElementById('wsStatusText');
    
    if (wsConnected) {
        addLog(log, 'warning', 'Already connected');
        return;
    }
    
    addLog(log, 'info', `Connecting to ${url}...`);
    statusIndicator.className = 'status-indicator connecting';
    statusText.textContent = 'Connecting...';
    
    try {
        ws = new WebSocket(url);
        
        ws.onopen = function() {
            wsConnected = true;
            addLog(log, 'success', 'Connected successfully!');
            statusIndicator.className = 'status-indicator connected';
            statusText.textContent = 'Connected';
            
            document.getElementById('wsConnectBtn').style.display = 'none';
            document.getElementById('wsDisconnectBtn').style.display = 'inline-block';
            document.getElementById('wsMessage').disabled = false;
            document.getElementById('wsSendBtn').disabled = false;
            
            updateWSInfo();
        };
        
        ws.onmessage = function(event) {
            addLog(log, 'info', `Received: ${event.data}`);
        };
        
        ws.onerror = function(error) {
            addLog(log, 'error', 'WebSocket error occurred');
            statusIndicator.className = 'status-indicator disconnected';
        };
        
        ws.onclose = function() {
            wsConnected = false;
            addLog(log, 'warning', 'Connection closed');
            statusIndicator.className = 'status-indicator disconnected';
            statusText.textContent = 'Disconnected';
            
            document.getElementById('wsConnectBtn').style.display = 'inline-block';
            document.getElementById('wsDisconnectBtn').style.display = 'none';
            document.getElementById('wsMessage').disabled = true;
            document.getElementById('wsSendBtn').disabled = true;
        };
        
    } catch (error) {
        addLog(log, 'error', `Connection failed: ${error.message}`);
        statusIndicator.className = 'status-indicator disconnected';
        statusText.textContent = 'Failed';
    }
}

function wsDisconnect() {
    if (ws) {
        ws.close();
        ws = null;
    }
}

function wsSendMessage() {
    const message = document.getElementById('wsMessage').value;
    const log = document.getElementById('wsLog');
    
    if (!wsConnected || !ws) {
        addLog(log, 'error', 'Not connected');
        return;
    }
    
    if (!message) {
        addLog(log, 'warning', 'Message is empty');
        return;
    }
    
    try {
        ws.send(message);
        addLog(log, 'success', `Sent: ${message}`);
        document.getElementById('wsMessage').value = '';
    } catch (error) {
        addLog(log, 'error', `Send failed: ${error.message}`);
    }
}

function updateWSInfo() {
    const info = document.getElementById('wsInfoContent');
    if (ws) {
        info.textContent = `URL: ${ws.url}\nProtocol: ${ws.protocol || 'default'}\nReady State: ${getWSReadyState(ws.readyState)}\nExtensions: ${ws.extensions || 'none'}`;
    }
}

function getWSReadyState(state) {
    const states = ['CONNECTING', 'OPEN', 'CLOSING', 'CLOSED'];
    return states[state] || 'UNKNOWN';
}

// ==================== CUSTOM HEADERS ====================
let customHeaders = {};

function addCustomHeader() {
    const name = document.getElementById('headerName').value.trim();
    const value = document.getElementById('headerValue').value.trim();
    
    if (!name) {
        alert('Please enter a header name');
        return;
    }
    
    // Ensure custom header prefix
    const headerName = name.startsWith('X-Custom-') ? name : `X-Custom-${name}`;
    customHeaders[headerName] = value;
    
    updateHeadersList();
    
    document.getElementById('headerName').value = '';
    document.getElementById('headerValue').value = '';
}

function removeCustomHeader(name) {
    delete customHeaders[name];
    updateHeadersList();
}

function clearCustomHeaders() {
    customHeaders = {};
    updateHeadersList();
}

function testCustomHeaders() {
    const log = document.getElementById('headersLog');
    log.style.display = 'block';
    log.innerHTML = '';
    
    if (Object.keys(customHeaders).length === 0) {
        addLog(log, 'warning', 'No custom headers to test');
        return;
    }
    
    addLog(log, 'info', 'Sending request with custom headers...');
    
    fetch('api/headers-test.php', {
        headers: customHeaders
    })
    .then(response => response.json())
    .then(data => {
        addLog(log, 'success', 'Request successful!');
        addLog(log, 'info', `Custom headers detected: ${data.analysis.custom_headers_count}`);
        addLog(log, 'info', `Behind proxy: ${data.analysis.behind_proxy ? 'Yes' : 'No'}`);
        
        if (data.headers.custom) {
            addLog(log, 'info', 'Custom headers received by server:');
            Object.entries(data.headers.custom).forEach(([key, val]) => {
                addLog(log, 'info', `  ${key}: ${val}`);
            });
        }
    })
    .catch(error => {
        addLog(log, 'error', 'Request failed: ' + error.message);
    });
}

// ==================== SESSION STICKINESS ====================
function testStickiness() {
    const requests = parseInt(document.getElementById('stickyRequests').value);
    const endpoint = document.getElementById('stickyEndpoint').value;
    const log = document.getElementById('stickyLog');
    const results = document.getElementById('stickyResults');
    
    log.style.display = 'block';
    results.style.display = 'block';
    log.innerHTML = '';
    
    addLog(log, 'info', `Testing session stickiness with ${requests} requests...`);
    
    const serverCounts = {};
    let sessionId = null;
    let completed = 0;
    
    function makeRequest() {
        return fetch(endpoint, {
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            const serverId = data.server.identifier;
            
            if (!sessionId) {
                sessionId = data.session_id;
            }
            
            if (!serverCounts[serverId]) {
                serverCounts[serverId] = 0;
            }
            serverCounts[serverId]++;
            
            completed++;
            addLog(log, 'info', `Request ${completed}/${requests}: Server ${serverId}`);
            
            return data;
        });
    }
    
    const promises = [];
    for (let i = 0; i < requests; i++) {
        promises.push(makeRequest());
    }
    
    Promise.all(promises).then(() => {
        const uniqueServers = Object.keys(serverCounts).length;
        const isSticky = uniqueServers === 1;
        
        document.getElementById('stickyTotal').textContent = requests;
        document.getElementById('stickyServers').textContent = uniqueServers;
        document.getElementById('stickyStatus').textContent = isSticky ? '✓ STICKY' : '✗ NOT STICKY';
        document.getElementById('stickyStatus').style.color = isSticky ? '#50fa7b' : '#ff5555';
        document.getElementById('stickyCookie').textContent = sessionId || 'None';
        
        const distribution = document.getElementById('stickyDistribution');
        distribution.innerHTML = '';
        Object.entries(serverCounts).forEach(([server, count]) => {
            const percentage = ((count / requests) * 100).toFixed(1);
            const bar = document.createElement('div');
            bar.style.cssText = 'margin: 5px 0; padding: 5px; background: #333; border-radius: 3px;';
            bar.innerHTML = `<span class="key">${server}:</span> <span class="value">${count} requests (${percentage}%)</span>`;
            distribution.appendChild(bar);
        });
        
        addLog(log, 'success', `Test completed! Stickiness: ${isSticky ? 'YES' : 'NO'}`);
    }).catch(error => {
        addLog(log, 'error', 'Test failed: ' + error.message);
    });
}

// ==================== PARALLEL CONNECTIONS ====================
let parallelTestRunning = false;
let parallelStats = { active: 0, completed: 0, failed: 0, maxConcurrent: 0 };

function testParallelConnections() {
    const count = parseInt(document.getElementById('parallelCount').value);
    const holdTime = parseInt(document.getElementById('parallelHoldTime').value);
    const type = document.getElementById('parallelType').value;
    const log = document.getElementById('parallelLog');
    const stats = document.getElementById('parallelStats');
    
    parallelTestRunning = true;
    parallelStats = { active: 0, completed: 0, failed: 0, maxConcurrent: 0 };
    
    document.getElementById('parallelBtn').style.display = 'none';
    document.getElementById('parallelStopBtn').style.display = 'inline-block';
    log.style.display = 'block';
    stats.style.display = 'block';
    log.innerHTML = '';
    
    addLog(log, 'info', `Starting ${count} parallel connections (hold: ${holdTime}s)...`);
    
    const promises = [];
    
    for (let i = 0; i < count; i++) {
        if (!parallelTestRunning) break;
        
        const promise = makeParallelRequest(i, holdTime, type, log);
        promises.push(promise);
    }
    
    Promise.allSettled(promises).then(() => {
        parallelTestRunning = false;
        document.getElementById('parallelBtn').style.display = 'inline-block';
        document.getElementById('parallelStopBtn').style.display = 'none';
        addLog(log, 'success', 'All connections completed!');
    });
}

function makeParallelRequest(id, holdTime, type, log) {
    parallelStats.active++;
    updateParallelStats();
    
    const url = `api/parallel-test.php?hold=${holdTime}&id=${id}`;
    
    return fetch(url)
        .then(response => response.json())
        .then(data => {
            parallelStats.completed++;
            addLog(log, 'success', `Connection ${id} completed (${data.response_time_ms}ms)`);
        })
        .catch(error => {
            parallelStats.failed++;
            addLog(log, 'error', `Connection ${id} failed: ${error.message}`);
        })
        .finally(() => {
            parallelStats.active--;
            updateParallelStats();
        });
}

function updateParallelStats() {
    if (parallelStats.active > parallelStats.maxConcurrent) {
        parallelStats.maxConcurrent = parallelStats.active;
    }
    
    document.getElementById('parallelActive').textContent = parallelStats.active;
    document.getElementById('parallelCompleted').textContent = parallelStats.completed;
    document.getElementById('parallelFailed').textContent = parallelStats.failed;
    document.getElementById('parallelMax').textContent = parallelStats.maxConcurrent;
}

function stopParallelTest() {
    parallelTestRunning = false;
    addLog(document.getElementById('parallelLog'), 'warning', 'Test stopped by user');
}

// ==================== HEALTH CHECK ====================
let healthMonitorInterval = null;
let healthStats = { total: 0, success: 0, failed: 0, times: [] };

function testHealthCheck() {
    const status = document.getElementById('healthStatus').value;
    const delay = document.getElementById('healthDelay').value;
    const body = encodeURIComponent(document.getElementById('healthBody').value);
    const log = document.getElementById('healthLog');
    
    log.style.display = 'block';
    
    addLog(log, 'info', `Testing health check (status: ${status}, delay: ${delay}ms)...`);
    
    const startTime = Date.now();
    
    fetch(`api/health-check.php?status=${status}&delay=${delay}&body=${body}`)
        .then(response => {
            const responseTime = Date.now() - startTime;
            return response.json().then(data => ({ response, data, responseTime }));
        })
        .then(({ response, data, responseTime }) => {
            if (response.ok) {
                addLog(log, 'success', `Health check passed (${responseTime}ms)`);
            } else {
                addLog(log, 'warning', `Health check returned ${response.status} (${responseTime}ms)`);
            }
            addLog(log, 'info', `Server: ${data.server}, Uptime: ${data.uptime}`);
        })
        .catch(error => {
            addLog(log, 'error', 'Health check failed: ' + error.message);
        });
}

function startHealthMonitor() {
    const log = document.getElementById('healthLog');
    const stats = document.getElementById('healthStats');
    
    log.style.display = 'block';
    stats.style.display = 'block';
    healthStats = { total: 0, success: 0, failed: 0, times: [] };
    
    addLog(log, 'info', 'Starting health check monitoring (every 5 seconds)...');
    document.getElementById('healthStopBtn').style.display = 'inline-block';
    
    healthMonitorInterval = setInterval(() => {
        const startTime = Date.now();
        
        fetch('api/health-check.php')
            .then(response => {
                const responseTime = Date.now() - startTime;
                healthStats.times.push(responseTime);
                healthStats.total++;
                
                if (response.ok) {
                    healthStats.success++;
                    addLog(log, 'success', `✓ Health check OK (${responseTime}ms)`);
                } else {
                    healthStats.failed++;
                    addLog(log, 'error', `✗ Health check failed: ${response.status}`);
                }
                
                updateHealthStats();
            })
            .catch(error => {
                healthStats.total++;
                healthStats.failed++;
                addLog(log, 'error', `✗ Health check error: ${error.message}`);
                updateHealthStats();
            });
    }, 5000);
}

function stopHealthMonitor() {
    if (healthMonitorInterval) {
        clearInterval(healthMonitorInterval);
        healthMonitorInterval = null;
        addLog(document.getElementById('healthLog'), 'info', 'Health monitoring stopped');
        document.getElementById('healthStopBtn').style.display = 'none';
    }
}

function updateHealthStats() {
    document.getElementById('healthTotal').textContent = healthStats.total;
    document.getElementById('healthSuccess').textContent = healthStats.success;
    document.getElementById('healthFailed').textContent = healthStats.failed;
    
    if (healthStats.times.length > 0) {
        const avg = healthStats.times.reduce((a, b) => a + b, 0) / healthStats.times.length;
        document.getElementById('healthAvg').textContent = avg.toFixed(0) + 'ms';
    }
}

// ==================== COOKIE MANAGER ====================
function setCookie() {
    const name = document.getElementById('cookieName').value;
    const value = document.getElementById('cookieValue').value;
    const days = parseInt(document.getElementById('cookieExpires').value);
    
    if (!name) {
        alert('Please enter a cookie name');
        return;
    }
    
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = `${name}=${value};${expires};path=/`;
    
    updateCookieList();
    
    document.getElementById('cookieName').value = '';
    document.getElementById('cookieValue').value = '';
}

function deleteCookie(name) {
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/`;
    updateCookieList();
}

function clearAllCookies() {
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
        const name = cookie.split('=')[0].trim();
        deleteCookie(name);
    }
    updateCookieList();
}

function updateCookieList() {
    const list = document.getElementById('cookieList');
    const cookies = document.cookie.split(';').filter(c => c.trim());
    
    if (cookies.length === 0) {
        list.innerHTML = '<p>No cookies set</p>';
        return;
    }
    
    list.innerHTML = '';
    cookies.forEach(cookie => {
        const [name, value] = cookie.split('=').map(s => s.trim());
        const item = document.createElement('div');
        item.className = 'cookie-item';
        item.innerHTML = `
            <div>
                <span class="key">${name}:</span> 
                <span class="value">${value}</span>
            </div>
            <button class="btn btn-danger" style="padding: 5px 10px;" onclick="deleteCookie('${name}')">Delete</button>
        `;
        list.appendChild(item);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateCookieList();
    updateHeadersList();
});

// ==================== USER-AGENT SPOOFING ====================
const userAgents = {
    'chrome-win': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'chrome-mac': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'firefox-win': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
    'safari-mac': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Safari/605.1.15',
    'safari-ios': 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1 Mobile/15E148 Safari/604.1',
    'chrome-android': 'Mozilla/5.0 (Linux; Android 13) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.43 Mobile Safari/537.36',
    'edge': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.2210.61',
    'bot-google': 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    'bot-bing': 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
    'curl': 'curl/7.88.1'
};

function loadUAPreset() {
    const preset = document.getElementById('uaPreset').value;
    const customUA = document.getElementById('customUA');
    
    if (preset && userAgents[preset]) {
        customUA.value = userAgents[preset];
    }
}

function testUserAgent() {
    const customUA = document.getElementById('customUA').value;
    const log = document.getElementById('uaLog');
    
    log.style.display = 'block';
    log.innerHTML = '';
    
    if (!customUA) {
        addLog(log, 'error', 'Please enter a user-agent string');
        return;
    }
    
    addLog(log, 'info', 'Sending request with custom user-agent...');
    
    fetch('api/user-agent-test.php', {
        headers: {
            'X-Custom-User-Agent': customUA
        }
    })
    .then(response => response.json())
    .then(data => {
        addLog(log, 'success', 'Request successful!');
        addLog(log, 'info', `Server detected: ${data.user_agent}`);
        addLog(log, 'info', `Browser: ${data.client_info.browser} ${data.client_info.version}`);
        addLog(log, 'info', `OS: ${data.client_info.os}`);
        addLog(log, 'info', `Type: ${data.client_info.type}`);
    })
    .catch(error => {
        addLog(log, 'error', 'Request failed: ' + error.message);
    });
}

// ==================== UTILITY FUNCTIONS ====================
function addLog(logElement, type, message) {
    const entry = document.createElement('div');
    entry.className = `log-entry ${type}`;
    entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
    logElement.appendChild(entry);
    logElement.scrollTop = logElement.scrollHeight;
}