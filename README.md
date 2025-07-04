# Sistema de Préstamo de Equipos

Este proyecto es una aplicación web en PHP para la gestión de préstamos de dispositivos (portátiles, docking stations, monitores, etc.) dentro de una organización.

## 🛠 Tecnologías usadas

- PHP (con programación orientada a controladores)
- MariaDB / MySQL
- HTML + CSS
- FPDF (generación de PDF)
- Git para control de versiones

## 📂 Estructura principal

- `controllers/` – Lógica de control para cada función (prestamos, usuarios, etc.)
- `helpers/` – Funciones auxiliares (autenticación, tareas, correos)
- `config/database.php` – Conexión a base de datos
- `libs/fpdf/` – Librería FPDF para PDFs
- `index.php` – Punto de entrada principal

## ⚙ Instalación

1. Clona el repositorio:
   ```bash
   git clone https://github.com/tu_usuario/prestamo_equipo.git
   ```
2. Crea una base de datos `prestamos_equipo` y ejecuta el archivo `.sql` incluido.
3. Configura la conexión en `config/database.php`.
4. Asegúrate de tener habilitado Apache y PHP (se recomienda XAMPP o similar).

## 🔒 Seguridad

- No subas el archivo `database.php` si contiene contraseñas reales.
- Usa `.gitignore` para excluirlo del control de versiones.

## ✉ Contacto

Para soporte o mejoras, puedes contactar al administrador del sistema.
