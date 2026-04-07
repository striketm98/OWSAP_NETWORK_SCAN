<?php
// scan.php logic - Simple integration example
if (isset($_POST['scan'])) {
    $target = escapeshellarg($_POST['target']);
    // This command tells Docker to run nmap inside the engine container
    $output = shell_exec("docker exec vanguard-engine nmap -T4 -F $target");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Vanguard PT Integration Tool</title>
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">
        <h2>🛡️ Vanguard Network PT Dashboard</h2>
        <div class="card bg-secondary p-4 mt-4">
            <form method="POST">
                <input type="text" name="target" class="form-control" placeholder="Enter Target IP (e.g. 192.168.1.1)">
                <button name="scan" class="btn btn-danger mt-3">Launch Nmap Scan</button>
            </form>
        </div>
        
        <?php if ($output): ?>
        <div class="mt-4">
            <h4>Scan Results:</h4>
            <pre class="bg-black p-3 border border-success"><?php echo $output; ?></pre>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>