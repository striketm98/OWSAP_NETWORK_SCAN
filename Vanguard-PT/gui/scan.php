<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
    exit;
}

$target = trim((string)($_POST['target'] ?? ''));
$profile = trim((string)($_POST['profile'] ?? 'quick'));

$isValidTarget = filter_var($target, FILTER_VALIDATE_IP) !== false
    || preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $target)
    || preg_match('/^[a-z0-9][a-z0-9.-]{0,251}$/i', $target);

if ($target === '' || !$isValidTarget) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid target format'
    ]);
    exit;
}

$profiles = [
    'quick' => ['label' => 'Quick Nmap', 'command' => 'nmap -T4 -F'],
    'standard' => ['label' => 'Standard service scan', 'command' => 'nmap -sV -T4'],
    'intense' => ['label' => 'Intense discovery', 'command' => 'nmap -Pn -sV -O --top-ports 100'],
    'ping' => ['label' => 'Ping check', 'command' => 'ping -c 4']
];

if (!array_key_exists($profile, $profiles)) {
    http_response_code(422);
    echo json_encode([
        'status' => 'error',
        'message' => 'Unknown scan profile'
    ]);
    exit;
}

$dockerBinary = trim((string)shell_exec('command -v docker 2>/dev/null'));
if ($dockerBinary === '') {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Docker CLI is not available in the GUI container'
    ]);
    exit;
}

$command = sprintf(
    'docker exec vanguard-engine %s %s',
    $profiles[$profile]['command'],
    escapeshellarg($target)
);

$output = [];
$returnVar = 0;
exec($command . ' 2>&1', $output, $returnVar);

if ($returnVar !== 0) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Scan failed',
        'details' => implode("\n", $output)
    ]);
    exit;
}

echo json_encode([
    'status' => 'success',
    'profile' => $profile,
    'profile_label' => $profiles[$profile]['label'],
    'target' => $target,
    'raw_output' => implode("\n", $output),
    'timestamp' => date('Y-m-d H:i:s')
]);
