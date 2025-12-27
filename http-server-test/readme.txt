# Advanced PHP/HAProxy Test Suite

A comprehensive web-based tool for testing HTTP servers, reverse proxies, load balancers, and network performance.

## Features

### 🔍 Server Information
- Detailed server metrics and configuration
- Request/response header inspection
- SSL/TLS certificate information
- Environment variables
- Session management details
- HAProxy-specific headers

### ⬇️ Download Speed Test
- Generate files from 1MB to 1GB
- Three data types:
  - Random data (incompressible)
  - Zeros (compressible)
  - Text data
- Real-time progress tracking
- Speed calculation

### ⬆️ Upload Speed Test
- Chunked upload simulation
- Configurable file sizes (1MB - 100MB)
- Adjustable chunk sizes
- Progress monitoring
- Speed metrics

### 🔥 Stress Testing
- Concurrent HTTP request testing
- Configurable request count and concurrency
- Support for GET, POST, HEAD methods
- Real-time statistics:
  - Success/failure rates
  - Average response times
  - Request completion tracking
- Load balancer verification

### 🍪 Cookie Manager
- Create custom cookies
- Set expiration times
- View all active cookies
- Delete individual or all cookies
- Test session persistence

### 🌐 User-Agent Spoofing
- Preset user-agents for:
  - Chrome (Windows, macOS, Android)
  - Firefox (Windows)
  - Safari (macOS, iOS)
  - Edge
  - Googlebot, Bingbot
  - cURL
- Custom user-agent strings
- Server-side detection testing

## Directory Structure

```
/project-root
├── index.php                    # Main page
├── sections/
│   └── server-info-sections.php # Server information sections
├── api/
│   ├── download.php             # Download speed test endpoint
│   ├── upload.php               # Upload speed test endpoint
│   ├── stress-test.php          # Stress test endpoint
│   └── user-agent-test.php      # User-agent testing endpoint
├── assets/
│   └── js/
│       └── main.js              # JavaScript functionality
└── README.md                    # This file
```

## Installation

### Requirements
- PHP 7.4 or higher
- Apache or Nginx web server
- PHP extensions:
  - `curl` (optional, for enhanced features)
  - `json`
  - `mbstring`

### Setup Steps

1. **Clone or download** this project to your web server directory:
   ```bash
   cd /var/www/html
   git clone <repository-url> server-test
   ```

2. **Set proper permissions**:
   ```bash
   chmod 755 -R server-test/
   chown www-data:www-data -R server-test/
   ```

3. **Configure PHP** (edit `/etc/php/7.4/apache2/php.ini` or similar):
   ```ini
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 300
   memory_limit = 512M
   ```

4. **Restart web server**:
   ```bash
   sudo systemctl restart apache2
   # or
   sudo systemctl restart nginx
   ```

5. **Access the application**:
   ```
   http://your-server-ip/server-test/
   ```

## HAProxy Configuration (Optional)

To enable HAProxy-specific headers, add these to your HAProxy configuration:

```haproxy
frontend http_front
    bind *:80
    
    # Add custom headers
    http-request set-header X-HAProxy-Server %[srv_name]
    http-request set-header X-HAProxy-Backend %[be_name]
    http-request set-header X-HAProxy-Session %[src]
    
    default_backend http_back

backend http_back
    balance roundrobin
    option httpchk GET /
    
    server web1 192.168.1.10:80 check
    server web2 192.168.1.11:80 check
```

## Usage Examples

### Testing Download Speeds
1. Navigate to the **Download Test** tab
2. Select file size (e.g., 100MB)
3. Choose data type (random for realistic testing)
4. Click "Start Download"
5. Monitor speed and progress

### Stress Testing Load Balancer
1. Navigate to the **Stress Test** tab
2. Set number of requests (e.g., 1000)
3. Set concurrent connections (e.g., 50)
4. Select method (GET recommended)
5. Click "Start Stress Test"
6. Observe which backend servers handle requests
7. Check response time statistics

### Testing Cookie Persistence
1. Navigate to the **Cookie Manager** tab
2. Create a test cookie (e.g., `session_test`)
3. Set value and expiration
4. Make requests and verify cookie is sent
5. Check if load balancer maintains session stickiness

### User-Agent Testing
1. Navigate to the **User-Agent Spoofing** tab
2. Select a preset (e.g., Googlebot)
3. Click "Test Request"
4. Review server's detection of the user-agent
5. Test how your server handles different clients

## Advanced Features

### PROXY Protocol Support
To add PROXY protocol support (for preserving original client IP through proxies):

1. Update `index.php` to include:
```php
// Check for PROXY protocol header
if (isset($_SERVER['HTTP_X_PROXY_PROTOCOL'])) {
    // Parse PROXY protocol v1 or v2
    // Implementation depends on your proxy setup
}
```

2. Configure your proxy to send PROXY headers

### Custom Metrics
Add custom metrics by modifying `sections/server-info-sections.php`:

```php
// Example: Add disk space check
$disk_html = '<pre>';
$disk_html .= "<span class='key'>Total Space:</span> <span class='value'>" . 
              disk_total_space('/') / 1024 / 1024 / 1024 . " GB</span>\n";
$disk_html .= "<span class='key'>Free Space:</span> <span class='value'>" . 
              disk_free_space('/') / 1024 / 1024 / 1024 . " GB</span>\n";
$disk_html .= '</pre>';
render_section('💾 Disk Space', $disk_html);
```

## Security Considerations

⚠️ **Important**: This tool is designed for testing environments.

**For production use:**
1. Add authentication:
   ```php
   // Add to index.php
   session_start();
   if (!isset($_SESSION['authenticated'])) {
       // Redirect to login page
   }
   ```

2. Rate limiting:
   ```php
   // Add to API endpoints
   $ip = $_SERVER['REMOTE_ADDR'];
   // Check request count from IP in last minute
   ```

3. Restrict access by IP:
   ```apache
   # .htaccess
   Order Deny,Allow
   Deny from all
   Allow from 192.168.1.0/24
   ```

4. Disable in production:
   ```php
   // index.php
   if ($_SERVER['SERVER_NAME'] === 'production.example.com') {
       die('Testing tools disabled in production');
   }
   ```

## Troubleshooting

### Upload fails immediately
- Check `upload_max_filesize` and `post_max_size` in php.ini
- Verify PHP has write permissions to temp directory
- Check Apache/Nginx request size limits

### Download generates empty files
- Check `memory_limit` in php.ini
- Verify `max_execution_time` is sufficient
- Check for output buffering issues

### Stress test shows all failures
- Verify API endpoint path is correct
- Check CORS settings if testing across domains
- Ensure firewall allows the connections

### HAProxy headers not showing
- Verify HAProxy configuration includes custom headers
- Check that headers are being forwarded through any intermediate proxies
- Ensure PHP can read the headers (`$_SERVER` array)

## Contributing

Contributions welcome! Areas for improvement:
- WebSocket testing
- HTTP/2 and HTTP/3 support
- PROXY protocol v2 implementation
- GraphQL endpoint testing
- Performance metrics graphs
- Export test results

## License

MIT License - Feel free to use and modify for your needs.

## Support

For issues or questions, please create an issue in the repository or contact the maintainer.