# DOCUMENTO DE ESPECIFICACIÓN DE REQUERIMIENTOS DE SOFTWARE (SRS) & ARQUITECTURA MVC
**Proyecto:** TechCare Soporte TI - Mesa de Ayuda Inteligente con Diagnóstico y Analítica Predictiva de IA  
**Patrón de Diseño:** MVC (Modelo - Vista - Controlador) + Capa de Servicios  
**Versión:** 2.0.0  

---

## 1. ESTRUCTURA DEL PROYECTO (ARQUITECTURA MVC)

```text
├── config/                      # Conexión y credenciales seguras
│   ├── config.php               # Variables de entorno y API Keys
│   ├── config.example.php       # Plantilla pública
│   └── database.php             # Conexión PDO / MySQLi
│
├── src/                         # Backend y Lógica de Negocio
│   ├── Controllers/             # Controladores HTTP
│   │   ├── TicketController.php # Registro y gestión de ciclo de vida
│   │   └── IAController.php     # Despacho de consultas a IA
│   │
│   ├── Models/                  # Modelos de Datos
│   │   └── Ticket.php           # CRUD y agregación de métricas
│   │
│   └── Services/                # Servicios de IA
│       ├── GeminiService.php    # Integración con Google Gemini Cloud
│       └── LocalExpertService.php # Motor Heurístico Experto (0 Tokens)
│
├── views/                       # Vistas / Frontend
│   ├── formulario.php           # Vista de radicación
│   ├── dashboard.php            # Vista del panel gerencial
│   └── partials/                # Componentes comunes (Header, Footer, Modal)
│
├── public/                      # Archivos públicos estáticos
│   ├── css/style.css            # Estilos personalizados
│   └── js/                      # Scripts de cliente (Dashboard, Form)
│
└── database/schema.sql          # Estructura SQL
```
