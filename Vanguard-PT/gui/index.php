<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#08111f">
    <title>Vanguard PT | Network Scan Dashboard</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="page-shell">
        <header class="topbar">
            <div>
                <p class="eyebrow">Vanguard PT</p>
                <h1>Network Scan Dashboard</h1>
                <p class="lede">Launch controlled scans against approved targets, review output instantly, and keep the workflow mobile friendly.</p>
            </div>
            <div class="topbar-actions">
                <div class="status-pill">
                    <span class="status-dot"></span>
                    <span>Engine ready</span>
                </div>
                <button type="button" id="theme-toggle" class="ghost-btn theme-toggle" aria-pressed="false">Light mode</button>
            </div>
        </header>

        <main class="layout">
            <nav class="utility-bar" aria-label="Dashboard navigation">
                <a class="utility-link active" href="index.php">Dashboard</a>
                <a class="utility-link" href="logs.php">Saved logs</a>
            </nav>

            <section class="hero-card">
                <div class="hero-copy">
                    <p class="section-label">Operational view</p>
                    <h2>Clean UI, fast feedback, and a responsive layout that works from desktop to phone.</h2>
                    <p>Use the scan form below to trigger the engine container. Results render in a structured panel with a local scan history for quick review.</p>
                </div>

                <div class="stats-grid">
                    <article class="stat-card">
                        <span class="stat-value">3</span>
                        <span class="stat-label">Scan profiles</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-value">Live</span>
                        <span class="stat-label">AJAX output</span>
                    </article>
                    <article class="stat-card">
                        <span class="stat-value">Mobile</span>
                        <span class="stat-label">Responsive layout</span>
                    </article>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <div>
                        <p class="section-label">Launch scan</p>
                        <h3>Target controls</h3>
                    </div>
                    <p class="panel-note">Only approved targets should be scanned.</p>
                </div>

                <form id="scan-form" class="scan-form" autocomplete="off">
                    <label class="field">
                        <span>Target</span>
                        <input
                            type="text"
                            id="target"
                            name="target"
                            placeholder="192.168.1.1 or host.example.com"
                            required
                            inputmode="text"
                        >
                    </label>

                    <label class="field">
                        <span>Profile</span>
                        <select id="profile" name="profile">
                            <option value="quick">Quick Nmap</option>
                            <option value="standard">Standard service scan</option>
                            <option value="intense">Intense discovery</option>
                            <option value="ping">Ping check</option>
                        </select>
                    </label>

                    <div class="actions">
                        <button type="submit" id="scan-button" class="primary-btn">Run scan</button>
                        <button type="button" id="clear-history" class="ghost-btn">Clear history</button>
                    </div>
                </form>
            </section>

            <section class="output-grid">
                <article class="panel output-panel">
                    <div class="panel-header">
                        <div>
                            <p class="section-label">Result stream</p>
                            <h3>Latest output</h3>
                        </div>
                        <span id="result-status" class="result-status muted">Waiting for a scan</span>
                    </div>

                    <div id="result-empty" class="empty-state">
                        <p>Run a scan to see formatted output here.</p>
                    </div>

                    <div id="result-error" class="message-box error-box" hidden></div>

                    <pre id="scan-output" class="output-block" hidden></pre>

                    <div class="output-meta">
                        <span id="scan-meta-target">Target: -</span>
                        <span id="scan-meta-profile">Profile: -</span>
                        <span id="scan-meta-time">Last run: -</span>
                    </div>

                    <div class="results-table-wrap">
                        <table class="results-table" aria-label="Scan summary">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody id="scan-summary">
                                <tr>
                                    <td>Target</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>Profile</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>Last run</td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="panel history-panel">
                    <div class="panel-header">
                        <div>
                            <p class="section-label">Recent scans</p>
                            <h3>Session history</h3>
                        </div>
                        <span class="panel-note">Stored locally in this browser</span>
                    </div>

                    <div id="history-empty" class="empty-state compact">
                        <p>No scans yet. Your last few runs will appear here.</p>
                    </div>

                    <ul id="scan-history" class="history-list" aria-live="polite"></ul>
                </article>
            </section>
        </main>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>
