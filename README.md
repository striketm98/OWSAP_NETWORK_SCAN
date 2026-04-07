This is a sophisticated architectural challenge. To create a **Network Penetration Testing Integration Tool** that is containerized, has a GUI, and uses a LAMP stack for management, you need a multi-container orchestration.

We will use **Docker Compose** to link a "Scanner" container (Kali-based) with a "Management" container (LAMP-based GUI).

---

## 🏗️ The Architecture: "Vanguard-PT-Suite"

The system consists of two primary services:
1.  **Vanguard-GUI (LAMP):** A web dashboard to launch scans, view reports (PHP/MySQL), and manage targets.
2.  **Vanguard-Engine (Kali-Headless):** A background container containing tools like `Nmap`, `Nikto`, and `SQLmap` that executes commands triggered by the GUI.

---

## 📂 Project Structure
```text
Vanguard-PT/
├── docker-compose.yml
├── gui/
│   ├── index.php      (The PT Dashboard)
│   ├── scan.php       (The API to trigger Docker commands)
│   └── Dockerfile     (Custom LAMP Image)
└── engine/
    └── Dockerfile     (Custom Security Tools Image)
```

---

## 🚀 1. The Orchestrator (`docker-compose.yml`)
This file links your GUI and your scanning tools. Crucially, we mount the **Docker Socket** so the GUI can "talk" to other containers.

```yaml
version: '3.8'

services:
  # Management GUI (LAMP Stack)
  vanguard-gui:
    build: ./gui
    ports:
      - "8080:80"
    volumes:
      - ./gui:/var/www/html
      - /var/run/docker.sock:/var/run/docker.sock # Allows GUI to trigger Engine
    networks:
      - pt-network

  # Security Engine (Toolbox)
  vanguard-engine:
    build: ./engine
    tty: true
    stdin_open: true
    networks:
      - pt-network

networks:
  pt-network:
    driver: bridge
```

---

## 🛡️ 2. The Engine (`engine/Dockerfile`)
We use a slim Kali image and install only the essential PT tools.

```dockerfile
FROM kalilinux/kali-rolling

RUN apt-get update && apt-get install -y \
    nmap \
    nikto \
    sqlmap \
    dnsutils \
    netcat-traditional \
    && rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["/bin/bash"]
```

---

## 🖥️ 3. The GUI Logic (`gui/index.php`)
A "Pro" feel dashboard using Bootstrap. It uses PHP's `exec()` to send commands to the engine container via Docker's CLI.

```php
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
```

---

## ⚙️ 4. Security Hardening (Permissions)
For the PHP container to run `docker exec`, the `www-data` user needs permissions. In your **GUI Dockerfile**, you must add:

```dockerfile
FROM php:7.4-apache

# Install Docker CLI inside the LAMP container
RUN apt-get update && apt-get install -y docker.io

# Add www-data to the docker group
RUN usermod -aG docker www-data

# Enable Apache Mod_Rewrite for your "Pro" feel routes
RUN a2enmod rewrite
```

---

## 🏁 Deployment Workflow
1.  **Build & Launch:** `docker-compose up --build -d`
2.  **Access GUI:** Open your browser at `http://localhost:8080`.
3.  **Scan:** Enter an IP and hit "Launch". The LAMP container will send the request to the Kali engine, and the results will display in your browser.

### **Pro Features to Add Next:**
* **Database Integration:** Use the MariaDB image to store scan history and target logs.
* **GVM (OpenVAS) Integration:** Mount a volume to the engine to export `.xml` reports and parse them in the PHP GUI.
* **Security GRC Audit:** Add a module that compares Nmap results against a CVE database (CSV or API).

**Does this architectural approach align with your vision for the PT tool?**