# 🛠️ TechCare Soporte TI — Mesa de Ayuda Inteligente con IA Generativa y Analítica Predictiva

Sistema web de gestión de incidentes técnicos con **Arquitectura MVC**, **Módulo de Autenticación de Administradores** y **doble motor de Inteligencia Artificial**:
1. **Copiloto Técnico con IA Generativa (Google Gemini 3.x Cloud)**: Diagnósticos técnicos contextuales paso a paso, causa raíz y comandos listos para ejecutar.
2. **Motor Experto Heurístico (Local - 0 Tokens)**: Analizador semántico con 7 sub-dominios técnicos que opera 100% offline sin consumir cuotas de API.
3. **Hub Estratégico Ejecutivo de TI**: Prescribe contrataciones de personal, compras de infraestructura y capacitaciones a partir de los datos reales de tickets.

---

## 🚀 Características Principales

* **Portal Público de Radicación**: Formulario abierto para que cualquier usuario o cliente reporte su problema sin necesidad de cuenta.
* **Módulo de Autenticación y Seguridad (Login)**: Protege el Dashboard y los diagnósticos con IA contra accesos no autorizados.
* **Dashboard Gerencial & KPIs**: Gráficos interactivos con **Chart.js**, métricas de resolución, casos críticos y distribución de carga.
* **Diagnóstico Multi-Enfoque**:
  * *Enfoque #1*: Mitigación Rápida en Caliente (Nivel 1 / Nivel 2).
  * *Enfoque #2*: Análisis Forense y Causa Raíz (Nivel 3).
  * *Enfoque #3*: Reconfiguración Arquitectural y Endurecimiento Definitivo.
* **Selector Híbrido Cloud / Local**: Botones para alternar entre Google Gemini y el Motor Local para optimizar el consumo de tokens.
* **Alta Disponibilidad y Resiliencia**: Conmutación por fallo (*fallback*) automática si la API de Gemini no responde.

---

## 💻 Stack Tecnológico

* **Backend**: PHP 8.2+ (Arquitectura MVC con PDO / MySQLi)
* **Base de Datos**: MySQL 5.7+ / MariaDB 10.4+
* **Frontend**: HTML5, CSS3 Glassmorphism, Bootstrap 5.3, Bootstrap Icons, Chart.js 4.4
* **Inteligencia Artificial**: Google Gemini API (`gemini-3.7-flash` / `gemini-3.1-pro-preview`)

---

## ⚙️ Instalación y Puesta en Marcha

### 1. Iniciar la Aplicación
* En Windows: Haz doble clic en el archivo **`iniciar_servidor.bat`**.
* O manualmente desde la terminal:
```bash
cd 03-Desarrollo
php -S 127.0.0.1:8000
```

### 2. Credenciales de Acceso Administrativo
* **Portal de Radicación (Público):** [http://localhost:8000](http://localhost:8000)
* **Acceso Administrativo (Login):** [http://localhost:8000/index.php?route=login](http://localhost:8000/index.php?route=login)
* **Usuario por Defecto:** `admin@techcare.com`
* **Contraseña por Defecto:** `admin123`

---

## 📁 Estructura del Proyecto (MVC)

```
03-Desarrollo/
├── config/             # Conexión MySQL y configuración
├── database/           # Script SQL (schema.sql)
├── docs/               # Documentación SRS y Requerimientos
├── public/             # Estilos CSS y Scripts JS
├── src/                # Controladores, Modelos y Servicios IA
├── views/              # Vistas HTML/PHP (Dashboard, Formulario, Login)
├── index.php           # Enrutador Front Controller
└── iniciar_servidor.bat# Lanzador del servidor PHP
```

---

## 📄 Licencia
Distribuido bajo la licencia MIT.
