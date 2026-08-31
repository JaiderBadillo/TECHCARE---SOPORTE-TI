# DOCUMENTO DE ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS) & ARQUITECTURA MVC
**Proyecto:** TechCare Soporte TI - Mesa de Ayuda Inteligente con Diagnóstico y Analítica Predictiva de IA  
**Patrón de Diseño:** MVC (Modelo - Vista - Controlador) + Capa de Servicios + Módulo de Autenticación RBAC  
**Versión:** 2.1.0  

---

## 1. ESTRUCTURA DEL PROYECTO (ARQUITECTURA MVC)

```text
03-Desarrollo/
│
├── config/                      # Conexión y credenciales seguras
│   ├── config.php               # Variables de entorno y API Keys
│   ├── config.example.php       # Plantilla pública
│   └── database.php             # Conexión PDO / MySQLi
│
├── src/                         # Backend y Lógica de Negocio
│   ├── Controllers/             # Controladores HTTP
│   │   ├── AuthController.php   # Control de sesiones y autenticación
│   │   ├── TicketController.php # Registro y gestión de ciclo de vida
│   │   └── IAController.php     # Despacho de consultas a IA
│   │
│   ├── Models/                  # Modelos de Datos
│   │   ├── User.php             # Gestión y autenticación de técnicos/admins
│   │   └── Ticket.php           # CRUD y agregación de métricas
│   │
│   └── Services/                # Servicios de IA
│       ├── GeminiService.php    # Integración con Google Gemini Cloud
│       └── LocalExpertService.php # Motor Heurístico Experto (0 Tokens)
│
├── views/                       # Vistas / Frontend
│   ├── login.php                # Vista de acceso administrativo
│   ├── formulario.php           # Vista pública de radicación
│   ├── dashboard.php            # Vista protegida del panel gerencial
│   └── partials/                # Componentes comunes (Header, Footer, Modal)
│
├── public/                      # Archivos públicos estáticos
│   ├── css/style.css            # Estilos personalizados
│   └── js/                      # Scripts de cliente (Dashboard, Form)
│
└── database/schema.sql          # Estructura SQL (tablas: solicitudes, usuarios)
```

---

## 2. SEGURIDAD Y CONTROL DE ACCESO (AUTENTICACIÓN)

* **Portal Público de Radicación (`views/formulario.php`):** Accesible libremente por cualquier usuario o empleado para reportar incidentes técnicos sin requerir inicio de sesión.
* **Panel Gerencial & Diagnóstico IA (`views/dashboard.php`):** Protegido mediante middleware de autenticación (`AuthController::requireAuth()`). Solo personal técnico y administradores con credenciales válidas pueden ingresar.
* **Credenciales por Defecto del Sistema:**
  * **Correo:** `admin@techcare.com`
  * **Contraseña:** `admin123`
  * **Cifrado:** BCRYPT (`password_hash`) con almacenamiento seguro en base de datos.
