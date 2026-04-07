<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#08111f">
    <title>Vanguard PT | Saved Logs</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="page-shell">
        <header class="topbar">
            <div>
                <p class="eyebrow">Vanguard PT</p>
                <h1>Saved Scan Logs</h1>
                <p class="lede">Browse the scan history saved in this browser and jump back to the dashboard when you need to launch another run.</p>
            </div>
            <div class="topbar-actions">
                <div class="status-pill">
                    <span class="status-dot"></span>
                    <span>History view</span>
                </div>
                <button type="button" id="theme-toggle" class="ghost-btn theme-toggle" aria-pressed="false">Light mode</button>
            </div>
        </header>

        <main class="layout">
            <nav class="utility-bar" aria-label="Dashboard navigation">
                <a class="utility-link" href="index.php">Dashboard</a>
                <a class="utility-link active" href="logs.php">Saved logs</a>
            </nav>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="section-label">Stored locally</p>
                        <h3>Scan log table</h3>
                    </div>
                    <div class="actions">
                        <button type="button" id="clear-history" class="ghost-btn">Clear history</button>
                    </div>
                </div>

                <div id="logs-empty" class="empty-state">
                    <p>No saved scans yet. Run a scan from the dashboard and it will appear here.</p>
                </div>

                <div class="results-table-wrap">
                    <table class="results-table" aria-label="Saved scan logs">
                        <thead>
                            <tr>
                                <th>Target</th>
                                <th>Profile</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody id="logs-table-body"></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>
