# 🛠️ TechCare Soporte TI — Mesa de Ayuda Inteligente con IA Generativa y Analítica Predictiva

Sistema web de gestión de incidentes técnicos con **doble motor de Inteligencia Artificial**:
1. **Copiloto Técnico con IA Generativa (Google Gemini 3.x Cloud)**: Diagnósticos técnicos contextuales paso a paso, causa raíz y comandos listos para ejecutar.
2. **Motor Experto Heurístico (Local - 0 Tokens)**: Analizador semántico con 7 sub-dominios técnicos que opera 100% offline sin consumir cuotas de API.
3. **Hub Estratégico Ejecutivo de TI**: Prescribe contrataciones de personal, compras de infraestructura y capacitaciones a partir de los datos reales de tickets.

---

## 🚀 Características Principales

* **Portal de Radicación de Incidentes**: Formulario ágil con categorización (`RED`, `SOFTWARE`, `HARDWARE`, `SEGURIDAD`, `CLOUD_SERVIDORES`, `BASE_DE_DATOS`) y niveles de criticidad.
* **Dashboard Gerencial & KPIs**: Gráficos interactivos con **Chart.js**, métricas de resolución, casos críticos y distribución de carga.
* **Diagnóstico Multi-Enfoque**:
  * *Enfoque #1*: Mitigación Rápida en Caliente (Nivel 1 / Nivel 2).
  * *Enfoque #2*: Análisis Forense y Causa Raíz (Nivel 3).
  * *Enfoque #3*: Reconfiguración Arquitectural y Endurecimiento Definitivo.
* **Selector Híbrido Cloud / Local**: Botones para alternar entre Google Gemini y el Motor Local para optimizar el consumo de tokens.
* **Alta Disponibilidad y Resiliencia**: Conmutación por fallo (*fallback*) automática si la API de Gemini no responde.

---

## 💻 Stack Tecnológico

* **Backend**: PHP 8.2+ con soporte cURL y MySQLi
* **Base de Datos**: MySQL 5.7+ / MariaDB 10.4+
* **Frontend**: HTML5, CSS3 Glassmorphism, Bootstrap 5.3, Bootstrap Icons, Chart.js 4.4
* **Inteligencia Artificial**: Google Gemini API (`gemini-3.7-flash` / `gemini-3.1-pro-preview`)

---

## ⚙️ Instalación y Puesta en Marcha

### 1. Clonar el Repositorio
```bash
git clone https://github.com/TU_USUARIO/soporte-ti-ia.git
cd soporte-ti-ia
```

### 2. Configurar la Base de Datos
1. Inicia tu servidor MySQL (por ejemplo, con XAMPP, WampServer o MySQL Server).
2. Crea la base de datos `soporte_db`.
3. Importa el archivo `schema.sql`:
```bash
mysql -u root -p soporte_db < schema.sql
```

### 3. Configurar la Clave de API de Gemini
1. Copia el archivo `config.example.php` como `config.php`:
```bash
cp config.example.php config.php
```
2. Edita `config.php` y coloca tu API Key de Google Gemini ([Obtenla gratis aquí](https://aistudio.google.com/app/apikey)):
```php
define('GEMINI_API_KEY', 'TU_API_KEY_AQUI');
define('GEMINI_MODEL', 'gemini-3.7-flash');
```

### 4. Iniciar la Aplicación
* En Windows: Haz doble clic en el archivo **`iniciar_servidor.bat`**.
* O manualmente desde la terminal:
```bash
php -S 127.0.0.1:8000
```
Accede desde tu navegador a: **[http://localhost:8000](http://localhost:8000)**

---

## 📁 Estructura del Proyecto

```
├── config.example.php      # Plantilla de configuración
├── db.php                  # Conexión segura a la base de datos MySQL
├── gemini_client.php       # Cliente HTTP con cURL para Google Gemini API
├── solucionar_ticket_ia.php# Diagnósticos técnicos con Gemini y Motor Local
├── analizar_ia.php         # Analítica estratégica y Business Intelligence
├── formulario.html         # Formulario de radicación para usuarios
├── guardar.php             # Controlador para registrar nuevos tickets
├── reporte.php             # Dashboard gerencial y modal de diagnóstico
├── actualizar_estado.php   # Actualización asíncrona de estados (AJAX)
├── schema.sql              # Esquema de la tabla 'solicitudes'
├── iniciar_servidor.bat    # Script de arranque rápido para Windows
└── DOCUMENTACION_PROYECTO_SRS.md # Documento formal de requerimientos de software
```

---

## 📄 Licencia
Distribuido bajo la licencia MIT. Consulta `LICENSE` para más detalles.
