<?php
/**
 * Vanguard-PT | Network Scan Orchestrator
 * Security Lead: Tamal
 */

header('Content-Type: application/json');

// --- 1. Security Input Validation ---
$target = $_POST['target'] ?? '';
$tool   = $_POST['tool'] ?? 'nmap';

// Prevent Command Injection: Sanitize IP/Hostname
if (!filter_var($target, FILTER_VALIDATE_IP) && !preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $target)) {
    echo json_encode(["status" => "error", "message" => "Invalid Target Format"]);
    exit;
}

// --- 2. Tool Mapping (Whitelist) ---
$allowed_tools = [
    "nmap"   => "nmap -T4 -F",
    "nikto"  => "nikto -h",
    "sqlmap" => "sqlmap -u",
    "ping"   => "ping -c 4"
];

if (!array_key_exists($tool, $allowed_tools)) {
    echo json_encode(["status" => "error", "message" => "Tool Not Authorized"]);
    exit;
}

// --- 3. Docker Execution Logic ---
$command_prefix = "docker exec vanguard-engine";
$full_command = $command_prefix . " " . $allowed_tools[$tool] . " " . escapeshellarg($target);

// Execute and capture both output and return code
exec($full_command . " 2>&1", $output, $return_var);

// --- 4. Response Generation ---
if ($return_var === 0) {
    echo json_encode([
        "status" => "success",
        "tool" => $tool,
        "target" => $target,
        "raw_output" => implode("\n", $output),
        "timestamp" => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Scan Failed",
        "details" => implode("\n", $output)
    ]);
}