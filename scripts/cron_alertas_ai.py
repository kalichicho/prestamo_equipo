#!/usr/bin/env python
# scripts/cron_alertas_ai.py

import socket
import feedparser
import time
import re
import mysql.connector
from datetime import datetime, timezone, timedelta

# 0) Timeout global de 10s para evitar bloqueos
socket.setdefaulttimeout(10)

# 1) Configuración MySQL
db_cfg = {
    'host':     '127.0.0.1',
    'user':     'root',
    'password': '',
    'database': 'prestamos_equipo',
    'charset':  'utf8mb4'
}

# 2) Fuentes RSS activas y relevantes
RSS_FEEDS = {
    'NVD':       'https://nvd.nist.gov/feeds/xml/cve/2.0/nvdcve-2.0-recent.xml',
    'CISA KEV':  'https://www.cisa.gov/sites/default/files/feeds/known_exploited_vulnerabilities.xml',
    'HackerNews':'https://feeds.feedburner.com/TheHackersNews',
}

# 3) Software a vigilar
SOFTWARES = [
    '7zip','chrome','firefox','edge',
    'forticlient','aruba','crearpass',
    'office 2016','office 2010','office 365',
    'windows 10','windows 11','q-gis',
    'snowflake-connector-net','winrar', 'VS Code'
]

# 4) Conectar a MySQL
cnx    = mysql.connector.connect(**db_cfg)
cursor = cnx.cursor()

# 5) Patrón único de vulnerabilidades
vuln_pattern = re.compile(
    r'\bvulnerabilidad\b|'
    r'\bCVE-\d{4}-\d+\b|'
    r'\bexploit\b|'
    r'\bRCE\b|'
    r'\bXSS\b|'
    r'\bDoS\b|'
    r'\boverflow\b|'
    r'\bescalad[ao] de privilegios\b',
    re.IGNORECASE
)

# 6) Procesar cada feed
for fuente, url in RSS_FEEDS.items():
    print(f"[DEBUG] Conectando a {fuente}")
    try:
        feed = feedparser.parse(url, request_headers={'User-Agent':'Mozilla/5.0'})
    except Exception as e:
        print(f"[WARN] No se pudo parsear {fuente}: {e}")
        continue

    if getattr(feed, 'bozo', False):
        print(f"[WARN] bozo_exception en {fuente}: {feed.bozo_exception}")
        continue

    print(f"[DEBUG] {fuente} → {len(feed.entries)} items")

    for entry in feed.entries:
        title = entry.get('title','').strip()
        link  = entry.get('link','').strip()
        desc  = entry.get('description', entry.get('summary','')).strip()
        texto = f"{title}. {desc}"

        # 6.1) Fecha y filtro 30 días
        pub = entry.get('published_parsed') or entry.get('updated_parsed')
        dt  = datetime.fromtimestamp(time.mktime(pub), timezone.utc) if pub else datetime.now(timezone.utc)
        if dt < datetime.now(timezone.utc) - timedelta(days=30):
            continue

        # 6.2) FILTRO VULN: solo si habla de vulnerabilidades
        if not vuln_pattern.search(texto):
            continue

        # 6.3) Filtrar por software
        key = next((sw for sw in SOFTWARES if sw.lower() in texto.lower()), None)
        if not key:
            continue
        print(f"[DEBUG] {fuente} → coincide con {key}: {title}")

        # 6.4) Detectar CVE y criticidad
        cve_match = re.search(r'(CVE-\d{4}-\d+)', texto, re.IGNORECASE)
        cve       = cve_match.group(1).upper() if cve_match else None
        severity  = 'crítica' if re.search(r'\bcritical\b|\bcrítico\b', texto, re.IGNORECASE) else 'importante'

        # 6.5) Generar mensaje
        msg  = "¡Alerta de seguridad detectada!\n"
        msg += f"Se ha publicado una vulnerabilidad {severity}"
        if cve: msg += f" ({cve})"
        msg += f" que afecta a {key.title()}.\n"
        snippet = re.sub(r'\s+', ' ', desc)[:150].rstrip('…') + '…'
        msg += f"{snippet}\nSe recomienda actualizar inmediatamente.\n"
        msg += f"Fuente: {fuente} – {dt.strftime('%d/%m/%Y %H:%M')}"

        # 6.6) Insertar en BD
        try:
            cursor.execute("""
              INSERT IGNORE INTO notificaciones
                (software, fuente, titulo, enlace, descripcion, fecha)
              VALUES (%s,%s,%s,%s,%s,%s)
            """, (
              key.title(), fuente, title[:255], link, msg,
              dt.strftime("%Y-%m-%d %H:%M:%S")
            ))
            cnx.commit()
            print(f"[+] Alerta insertada: {key.title()} {cve or ''}")
        except mysql.connector.IntegrityError:
            pass

# 7) Cerrar conexión
cursor.close()
cnx.close()
