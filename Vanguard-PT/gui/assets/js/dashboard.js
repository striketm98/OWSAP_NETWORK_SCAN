const root = document.documentElement;
const historyKey = "vanguard_scan_history";
const themeKey = "vanguard_theme";

const profileLabels = {
    quick: "Quick Nmap",
    standard: "Standard service scan",
    intense: "Intense discovery",
    ping: "Ping check"
};

function safeParse(value, fallback) {
    try {
        return JSON.parse(value);
    } catch (error) {
        return fallback;
    }
}

function loadHistory() {
    try {
        const raw = localStorage.getItem(historyKey);
        const parsed = raw ? safeParse(raw, []) : [];
        return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        return [];
    }
}

function saveHistory(history) {
    try {
        localStorage.setItem(historyKey, JSON.stringify(history.slice(0, 12)));
    } catch (error) {
        // History is optional; scanning still works if storage is blocked.
    }
}

function formatTimestamp(value) {
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? value : date.toLocaleString();
}

function setTheme(theme) {
    const resolvedTheme = theme === "light" ? "light" : "dark";
    root.setAttribute("data-theme", resolvedTheme);

    const toggle = document.getElementById("theme-toggle");
    if (toggle) {
        toggle.textContent = resolvedTheme === "dark" ? "Light mode" : "Dark mode";
        toggle.setAttribute("aria-pressed", String(resolvedTheme === "light"));
    }

    try {
        localStorage.setItem(themeKey, resolvedTheme);
    } catch (error) {
        // Theme preference is optional.
    }
}

function initTheme() {
    let storedTheme = "dark";

    try {
        storedTheme = localStorage.getItem(themeKey) || "dark";
    } catch (error) {
        storedTheme = "dark";
    }

    setTheme(storedTheme);

    const toggle = document.getElementById("theme-toggle");
    if (toggle) {
        toggle.addEventListener("click", () => {
            const nextTheme = root.getAttribute("data-theme") === "dark" ? "light" : "dark";
            setTheme(nextTheme);
        });
    }
}

function renderHistoryList() {
    const historyList = document.getElementById("scan-history");
    const historyEmpty = document.getElementById("history-empty");

    if (!historyList || !historyEmpty) {
        return;
    }

    const history = loadHistory();
    historyList.innerHTML = "";
    historyEmpty.hidden = history.length > 0;

    history.forEach((item) => {
        const li = document.createElement("li");
        li.className = "history-item";

        const title = document.createElement("strong");
        title.textContent = `${item.target} - ${item.profileLabel}`;

        const meta = document.createElement("small");
        meta.textContent = `${item.status} - ${item.timestamp}`;

        li.append(title, meta);
        historyList.appendChild(li);
    });
}

function pushHistory(entry) {
    const history = loadHistory();
    history.unshift(entry);
    saveHistory(history);
    renderHistoryList();
    renderLogsTable();
}

function renderSummary(data) {
    const summary = document.getElementById("scan-summary");

    if (!summary) {
        return;
    }

    summary.innerHTML = "";

    const rows = [
        ["Target", data.target || "-"],
        ["Profile", profileLabels[data.profile] || data.profile || "-"],
        ["Status", data.status === "success" ? "Completed" : "Failed"],
        ["Last run", formatTimestamp(data.timestamp)],
        ["Output lines", String(data.raw_output || "").split(/\r?\n/).filter(Boolean).length || 0]
    ];

    rows.forEach(([label, value]) => {
        const row = document.createElement("tr");
        const labelCell = document.createElement("td");
        const valueCell = document.createElement("td");

        labelCell.textContent = label;
        valueCell.textContent = String(value);

        row.append(labelCell, valueCell);
        summary.appendChild(row);
    });
}

function setStatus(kind, text) {
    const resultStatus = document.getElementById("result-status");
    if (!resultStatus) {
        return;
    }
    resultStatus.className = `result-status ${kind}`;
    resultStatus.textContent = text;
}

function setBusy(isBusy) {
    const scanButton = document.getElementById("scan-button");
    if (!scanButton) {
        return;
    }
    scanButton.disabled = isBusy;
    scanButton.textContent = isBusy ? "Scanning..." : "Run scan";
}

function showResult(data) {
    const resultEmpty = document.getElementById("result-empty");
    const scanOutput = document.getElementById("scan-output");
    const resultError = document.getElementById("result-error");
    const scanMetaTarget = document.getElementById("scan-meta-target");
    const scanMetaProfile = document.getElementById("scan-meta-profile");
    const scanMetaTime = document.getElementById("scan-meta-time");

    if (resultEmpty) {
        resultEmpty.hidden = true;
    }
    if (scanOutput) {
        scanOutput.hidden = false;
        scanOutput.textContent = data.raw_output || data.message || "No output was returned.";
    }
    if (resultError) {
        resultError.hidden = true;
    }
    if (scanMetaTarget) {
        scanMetaTarget.textContent = `Target: ${data.target}`;
    }
    if (scanMetaProfile) {
        scanMetaProfile.textContent = `Profile: ${profileLabels[data.profile] || data.profile}`;
    }
    if (scanMetaTime) {
        scanMetaTime.textContent = `Last run: ${formatTimestamp(data.timestamp)}`;
    }

    renderSummary(data);
    setStatus(data.status === "success" ? "success" : "error", data.status === "success" ? "Scan completed" : "Scan failed");
}

function showError(message) {
    const resultEmpty = document.getElementById("result-empty");
    const scanOutput = document.getElementById("scan-output");
    const resultError = document.getElementById("result-error");
    const scanSummary = document.getElementById("scan-summary");

    if (resultEmpty) {
        resultEmpty.hidden = true;
    }
    if (scanOutput) {
        scanOutput.hidden = true;
    }
    if (resultError) {
        resultError.hidden = false;
        resultError.textContent = message;
    }
    if (scanSummary) {
        scanSummary.innerHTML = `
            <tr><td>Target</td><td>-</td></tr>
            <tr><td>Profile</td><td>-</td></tr>
            <tr><td>Status</td><td>Failed</td></tr>
            <tr><td>Last run</td><td>-</td></tr>
            <tr><td>Output lines</td><td>0</td></tr>
        `;
    }
    setStatus("error", "Scan failed");
}

function renderLogsTable() {
    const tableBody = document.getElementById("logs-table-body");
    const logsEmpty = document.getElementById("logs-empty");

    if (!tableBody || !logsEmpty) {
        return;
    }

    const history = loadHistory();
    tableBody.innerHTML = "";
    logsEmpty.hidden = history.length > 0;

    history.forEach((item) => {
        const row = document.createElement("tr");
        const cells = [
            item.target,
            item.profileLabel || profileLabels[item.profile] || item.profile,
            item.status,
            item.timestamp
        ];

        cells.forEach((value) => {
            const cell = document.createElement("td");
            cell.textContent = String(value);
            row.appendChild(cell);
        });

        tableBody.appendChild(row);
    });
}

function initDashboard() {
    const form = document.getElementById("scan-form");
    const targetInput = document.getElementById("target");
    const profileSelect = document.getElementById("profile");
    const clearHistoryButton = document.getElementById("clear-history");

    if (!form || !targetInput || !profileSelect) {
        return;
    }

    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const target = targetInput.value.trim();
        const profile = profileSelect.value;

        if (!target) {
            showError("Please enter a target host or IP address.");
            targetInput.focus();
            return;
        }

        setBusy(true);
        setStatus("muted", "Scanning...");

        const resultError = document.getElementById("result-error");
        if (resultError) {
            resultError.hidden = true;
        }

        try {
            const response = await fetch("scan.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
                },
                body: new URLSearchParams({ target, profile }).toString()
            });

            const data = await response.json();

            if (!response.ok || data.status !== "success") {
                throw new Error(data.message || data.details || "Scan request failed.");
            }

            showResult(data);
            pushHistory({
                target: data.target,
                profile: data.profile,
                profileLabel: profileLabels[data.profile] || data.profile,
                status: "Success",
                timestamp: formatTimestamp(data.timestamp)
            });
        } catch (error) {
            const message = error instanceof Error ? error.message : "Unable to complete the scan.";
            showError(message);
            pushHistory({
                target,
                profile,
                profileLabel: profileLabels[profile] || profile,
                status: "Failed",
                timestamp: new Date().toLocaleString()
            });
        } finally {
            setBusy(false);
        }
    });

    if (clearHistoryButton) {
        clearHistoryButton.addEventListener("click", () => {
            try {
                localStorage.removeItem(historyKey);
            } catch (error) {
                // Ignore storage errors and keep the UI responsive.
            }
            renderHistoryList();
            renderLogsTable();
            const historyEmpty = document.getElementById("history-empty");
            const logsEmpty = document.getElementById("logs-empty");
            if (historyEmpty) {
                historyEmpty.hidden = false;
            }
            if (logsEmpty) {
                logsEmpty.hidden = false;
            }
        });
    }

    targetInput.value = "";
    renderHistoryList();
}

initTheme();
initDashboard();
renderLogsTable();
