<?php
/**
 * Net Handler - Production Version
 * Fixes: Streaming Stability, Auto-Flush, and Accurate Port Testing
 */

// Mencegah PHP menghentikan skrip jika proses CMD berjalan lama (seperti tracert)
set_time_limit(0); 

// Memastikan output langsung terkirim baris demi baris
if (ob_get_level()) ob_end_clean();
header('Content-Type: text/event-stream'); // Gunakan event-stream agar koneksi lebih stabil
header('Cache-Control: no-cache');
header('Connection: keep-alive');

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['mode'])) {
    // Mendukung input dari POST (AJAX) maupun GET (EventSource)
    $mode = $_REQUEST['mode'] ?? '';
    $ip   = $_REQUEST['ip'] ?? '';
    $port = $_REQUEST['port'] ?? null;
    $ip_safe = escapeshellarg($ip);
    $os   = strtoupper(substr(PHP_OS, 0, 3));

    // --- LOGIKA PAPING (NATIVE PHP) ---
    if ($mode === 'paping' && $port) {
        echo "data: Papinging $ip on TCP port $port:\n\n";
        flush();
        
        for ($i = 1; $i <= 10; $i++) {
            $start = microtime(true);
            $fp = @fsockopen($ip, (int)$port, $errno, $errstr, 2);
            $end = microtime(true);
            $latency = round(($end - $start) * 1000);
            
            if ($fp) {
                echo "data: [$i] Connection Success: port=$port time={$latency}ms\n\n";
                fclose($fp);
            } else {
                echo "data: [$i] Connection Failed: port=$port (Timed out)\n\n";
            }
            flush();
            usleep(100000); 
        }
    } 
    // --- LOGIKA PING & TRACERT ---
    else {
        if ($mode === 'ping') {
            $cmd = ($os === 'WIN') ? "ping -n 4 $ip_safe" : "ping -c 4 $ip_safe";
        } elseif ($mode === 'tracert') {
            $cmd = ($os === 'WIN') ? "tracert -d $ip_safe" : "traceroute -n $ip_safe";
        }

        if (isset($cmd)) {
            $process = popen($cmd . ' 2>&1', 'r');
            while (!feof($process)) {
                $line = fgets($process);
                if ($line !== false) {
                    // Kirim per baris dengan format data SSE
                    echo "data: " . trim($line) . "\n\n";
                    flush();
                }
            }
            pclose($process);
        }
    }
    
    // Tanda proses selesai agar JavaScript tahu kapan harus berhenti
    echo "data: [DONE]\n\n";
    flush();
}