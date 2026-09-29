# 🚀 Guía de Despliegue en la Nube con Render — TechCare Soporte TI
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con IA y Automatización  
**Desarrollador:** Jaider Augusto Niño Badillo  
**Plataforma de Despliegue:** Render.com (Plan Free)  
**Versión:** 2.2.0  

---

## 🏗️ Arquitectura del Despliegue en la Nube

Para que el sistema funcione 100% en la nube sin depender de tu computadora local, Render albergará dos servicios interconectados:

```
[Cliente Web / Navegador] 
         │
         ▼
[Servicio 1: TechCare PHP + Apache]  (Render Web Service con Dockerfile)
   │                      │
   ▼                      ▼
[(DB MySQL Nube)]      [Servicio 2: n8n Engine] (Render Docker Web Service)
(Clever Cloud / Aiven)            │
                                  ▼
                        [Despacho de Correos Gmail] ➔ 📩 [Usuario]
```

---

## 📋 PASO 1: Desplegar n8n en Render (Servicio de Automatización)

1. Ingresa a tu panel de **Render** ([https://dashboard.render.com](https://dashboard.render.com)).
2. Haz clic en el botón azul superior **"New +"** y selecciona **"Web Service"**.
3. En la pantalla que aparece, selecciona la opción:  
   👉 **"Deploy an existing image"** (Desplegar imagen existente).
4. En el campo **Image URL**, escribe la imagen oficial de n8n:
   ```text
   docker.io/n8nio/n8n:latest
   ```
   y haz clic en **Next**.
5. Configura los detalles del servicio:
   * **Name:** `techcare-n8n` (o el nombre que prefieras).
   * **Region:** *Oregon (US West)* o *Frankfurt (EU)*.
   * **Instance Type:** **Free**.
6. Desplázate hacia abajo hasta la sección **"Environment Variables"** y agrega estas 3 variables:

| Variable (Key) | Valor (Value) |
| :--- | :--- |
| `N8N_PORT` | `5678` |
| `N8N_ENFORCE_SETTINGS_FILE_PERMISSIONS` | `true` |
| `WEBHOOK_URL` | `https://techcare-n8n.onrender.com/` *(sustituye por la URL que Render te asigne arriba)* |

7. Haz clic en **"Create Web Service"**.
8. Espera 1-2 minutos a que termine de compilar. Cuando diga **"Live"**, abre tu URL en el navegador:  
   👉 `https://techcare-n8n.onrender.com`
9. **Importa tu flujo:**  
   * Crea tu usuario inicial en n8n.
   * Clic en **Add workflow** ➔ **Import from file** ➔ Selecciona `n8n/workflow_techcare_notificacion_tickets.json`.
   * En el nodo de correo, pon tus credenciales de Gmail (con tu contraseña de aplicación de 16 letras) y dale a **Publish**.

---

## 📋 PASO 2: Base de Datos MySQL en la Nube (Gratis)

Render no incluye MySQL gratuito nativo (incluye PostgreSQL). Puedes crear tu base de datos MySQL en la nube gratis en 2 minutos usando **Clever Cloud** o **Aiven**:

### Opción recomendada: Clever Cloud (MySQL Gratis)
1. Ve a [https://www.clever-cloud.com](https://www.clever-cloud.com) y crea tu cuenta gratuita.
2. Clic en **Create** ➔ **an add-on** ➔ Selecciona **MySQL**.
3. Elige el plan **Free (Dev 10MB/50MB)**.
4. Obtendrás tus credenciales de conexión:
   * **Host:** (ej: `bxxxxxxxxxx.mysql.services.clever-cloud.com`)
   * **Database / Name:** (ej: `bxxxxxxxxxx`)
   * **User:** (ej: `uxxxxxxxxxx`)
   * **Password:** (ej: `pxxxxxxxxxx`)
   * **Port:** `3306`
5. Conéctate a esa base de datos (con phpMyAdmin, DBeaver o Workbench) y ejecuta el script:  
   📄 `03-Desarrollo/database/schema.sql` (para crear las tablas `usuarios` y `solicitudes` y el usuario admin).

---

## 📋 PASO 3: Desplegar TechCare en Render (Servicio Principal)

1. En tu panel de Render ([dashboard.render.com](https://dashboard.render.com)), haz clic en **New +** ➔ **Web Service**.
2. Selecciona **"Build and deploy from a Git repository"** y haz clic en **Next**.
3. Conecta tu repositorio de GitHub:  
   👉 **`JaiderBadillo/TECHCARE---SOPORTE-TI`**
4. Render detectará automáticamente el archivo **`Dockerfile`** que ya dejamos en la raíz del proyecto.
5. Configura los datos básicos:
   * **Name:** `techcare-soporte`
   * **Region:** La misma que elegiste para n8n (ej: *Oregon*).
   * **Instance Type:** **Free**.
6. Baja a la sección **"Environment Variables"** y haz clic en **"Add Environment Variable"** para ingresar las credenciales:

| Variable (Key) | Valor (Value) |
| :--- | :--- |
| `DB_HOST` | Host de tu MySQL en la nube (de Clever Cloud o Aiven) |
| `DB_USER` | Usuario de MySQL en la nube |
| `DB_PASSWORD` | Contraseña de MySQL en la nube |
| `DB_NAME` | Nombre de la base de datos MySQL |
| `DB_PORT` | `3306` |
| `N8N_WEBHOOK_ENABLED` | `true` |
| `N8N_WEBHOOK_URL` | `https://techcare-n8n.onrender.com/webhook/techcare-ticket-creado` |
| `N8N_WEBHOOK_RESOLVED_URL` | `https://techcare-n8n.onrender.com/webhook/techcare-ticket-creado` |
| `APP_URL` | `https://techcare-soporte.onrender.com` |
| `GEMINI_API_KEY` | Tu API Key de Google Gemini |

7. Haz clic en **"Create Web Service"**.
8. Render construirá el contenedor Docker con PHP 8.2 y Apache automáticamente.
9. En unos 2 minutos verás el mensaje **"Live"** con un enlace público (ej: `https://techcare-soporte.onrender.com`).

---

## 🧪 Verificación Final

1. Abre la URL pública de tu aplicación: `https://techcare-soporte.onrender.com/index.php?action=formulario`.
2. Radica un ticket de prueba con tu correo electrónico.
3. El sistema registrará el ticket en la base de datos de la nube, emitirá el Webhook a tu n8n en Render y te llegará el correo de confirmación de inmediato.
4. Ingresa como administrador al Dashboard (`admin@techcare.com` / `admin123`) y cambia el estado del ticket a **Resuelto**.
5. ¡Te llegará el correo de felicitación con el ticket resuelto!
