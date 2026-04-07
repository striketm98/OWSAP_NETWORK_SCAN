FROM kalilinux/kali-rolling

RUN apt-get update && apt-get install -y \
    nmap \
    nikto \
    sqlmap \
    dnsutils \
    netcat-traditional \
    && rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["/bin/bash"]