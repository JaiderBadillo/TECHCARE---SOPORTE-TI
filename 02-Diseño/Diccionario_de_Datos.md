# 📖 Diccionario de Datos — TechCare Soporte TI
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente con IA  
**Base de Datos:** MySQL / MariaDB (`soporte_db`)  
**Motor de Almacenamiento:** InnoDB  
**Cotejamiento / Charset:** `utf8mb4_unicode_ci`  
**Versión:** 2.2.0  

---

## 1. Introducción
El presente diccionario de datos describe detalladamente la estructura física y lógica de las tablas que componen la base de datos `soporte_db`. Especifica para cada columna el tipo de dato, longitud, restricciones de nulidad, llaves primarias/foráneas, valores por defecto y su propósito funcional en la plataforma.

---

## 2. Catálogo de Tablas del Sistema

| Nombre de Tabla | Descripción | Registros Estimados | Llave Primaria |
| :--- | :--- | :---: | :---: |
| **`usuarios`** | Almacena los datos y credenciales de acceso de clientes, técnicos y administradores. | Dinámico | `id` |
| **`solicitudes`** | Almacena los tickets de incidentes técnicos reportados, su estado y el diagnóstico generado por IA. | Dinámico | `id` |

---

## 3. Especificación Detallada de Tablas

### 3.1. Tabla: `usuarios`
Gestiona la autenticación y el control de acceso basado en roles (RBAC) para el portal de clientes y el dashboard administrativo.

| Columna | Tipo de Dato | Longitud | Permite Nulo | Llave | Valor por Defecto | Descripción y Reglas de Negocio |
| :--- | :--- | :---: | :---: | :---: | :---: | :--- |
| **`id`** | `INT` | 11 | **NO** | **PK** | *AUTO_INCREMENT* | Identificador único secuencial e irrepetible del usuario. |
| **`nombre`** | `VARCHAR` | 100 | **NO** | &mdash; | &mdash; | Nombre y apellidos completos del usuario o técnico. |
| **`email`** | `VARCHAR` | 150 | **NO** | **UNIQUE (UK)** | &mdash; | Correo electrónico corporativo único. Se utiliza como identificador de inicio de sesión. |
| **`password`** | `VARCHAR` | 255 | **NO** | &mdash; | &mdash; | Hash criptográfico generado con algoritmo BCRYPT (`password_hash`). Nunca se almacena en texto plano. |
| **`empresa`** | `VARCHAR` | 150 | **SÍ** | &mdash; | `NULL` | Razón social o nombre comercial de la empresa u organización a la que pertenece el usuario. |
| **`cargo_empresa`** | `VARCHAR` | 100 | **SÍ** | &mdash; | `NULL` | Cargo funcional desempeñado dentro de la empresa (Ej. *Líder de Contabilidad*, *Gerente de Operaciones*). |
| **`rol`** | `ENUM` | &mdash; | **NO** | &mdash; | `'cliente'` | Nivel de permisos dentro del sistema:<br/>• `'cliente'`: Acceso al formulario de radicación y su historial propio.<br/>• `'tecnico'`: Acceso a diagnóstico y gestión de solicitudes.<br/>• `'admin'`: Acceso total al Dashboard, KPIs y Decisiones IA. |
| **`activo`** | `TINYINT` | 1 | **SÍ** | &mdash; | `1` | Estado lógico de la cuenta:<br/>• `1`: Activo (Permite login).<br/>• `0`: Inactivo (Bloqueado). |
| **`fecha_creacion`** | `DATETIME` | &mdash; | **SÍ** | &mdash; | `CURRENT_TIMESTAMP` | Fecha y hora exacta del registro del usuario en el sistema. |

---

### 3.2. Tabla: `solicitudes`
Almacena el ciclo de vida completo de cada incidencia de TI, su criticidad, clasificación y el diagnóstico generado por los motores de inteligencia artificial.

| Columna | Tipo de Dato | Longitud | Permite Nulo | Llave | Valor por Defecto | Descripción y Reglas de Negocio |
| :--- | :--- | :---: | :---: | :---: | :--- |
| **`id`** | `INT` | 11 | **NO** | **PK** | *AUTO_INCREMENT* | Número consecutivo oficial de la solicitud / ticket de soporte. |
| **`usuario_id`** | `INT` | 11 | **SÍ** | **FK** | `NULL` | Llave foránea que referencia a `usuarios(id)`. Permite vincular la solicitud con la cuenta del usuario cliente que la radicó. |
| **`nombre`** | `VARCHAR` | 100 | **NO** | &mdash; | &mdash; | Nombre del solicitante que reporta el incidente. |
| **`email`** | `VARCHAR` | 150 | **NO** | &mdash; | &mdash; | Correo electrónico de contacto y notificación para el solicitante. |
| **`empresa`** | `VARCHAR` | 150 | **SÍ** | &mdash; | `NULL` | Empresa u organización de procedencia del solicitante. |
| **`asunto`** | `VARCHAR` | 150 | **NO** | &mdash; | &mdash; | Título sintético del fallo o requerimiento técnico reportado. |
| **`tipo_problema`** | `ENUM` | &mdash; | **NO** | &mdash; | `'SOFTWARE'` | Clasificación técnica por área de especialidad:<br/>• `'SOFTWARE'`: Fallas en Office, ERP, SaaS, navegadores.<br/>• `'RED'`: Caídas de enlace, túneles VPN, WiFi, Proxy.<br/>• `'HARDWARE'`: Daños físicos, periféricos, fuentes, BSOD.<br/>• `'SEGURIDAD'`: Phishing, bloqueos 2FA, intrusión, malware.<br/>• `'CLOUD_SERVIDORES'`: AWS, Docker, Azure, VMs, Nginx.<br/>• `'BASE_DE_DATOS'`: Deadlocks, lentitud SQL, timeouts. |
| **`prioridad`** | `ENUM` | &mdash; | **NO** | &mdash; | `'media'` | Nivel de criticidad e impacto operacional:<br/>• `'baja'`: No interrumpe las actividades diarias.<br/>• `'media'`: Problema individual manejable.<br/>• `'alta'`: Afecta operaciones clave del departamento.<br/>• `'critica'`: Sistema detenido o impacto general en la empresa. |
| **`mensaje`** | `TEXT` | 65,535 | **NO** | &mdash; | &mdash; | Descripción narrativa completa de la falla, síntomas y pasos previos. |
| **`estado`** | `ENUM` | &mdash; | **SÍ** | &mdash; | `'pendiente'` | Estado del ciclo de vida del ticket:<br/>• `'pendiente'`: En cola de espera de triaje.<br/>• `'en_proceso'`: En atención o diagnóstico técnico activo.<br/>• `'resuelto'`: Incidente solucionado y cerrado. |
| **`solucion_ia`** | `TEXT` | 65,535 | **SÍ** | &mdash; | `NULL` | Estructura JSON generada por Google Gemini o el Motor Local con el dictamen de causa raíz, plan paso a paso y comandos. |
| **`asignado_a`** | `VARCHAR` | 100 | **SÍ** | &mdash; | `NULL` | Nombre o identificación del especialista técnico asignado al ticket. |
| **`fecha_creacion`** | `DATETIME` | &mdash; | **SÍ** | &mdash; | `CURRENT_TIMESTAMP` | Estampa de tiempo en que se radicó formalmente la solicitud. |

---

## 4. Estructura del Objeto JSON Almacenado en `solucion_ia`

El atributo `solucion_ia` almacena un documento JSON con el siguiente esquema:

| Clave JSON | Tipo de Dato | Descripción | Ejemplo |
| :--- | :--- | :--- | :--- |
| **`enfoque_nombre`** | `String` | Nombre de la variante o nivel técnico aplicado. | `"Solución Rápida en Caliente & Mitigación Inmediata (N1/N2)"` |
| **`categoria`** | `String` | Sub-dominio técnico detectado por el motor semántico. | `"Microsoft 365 & Licenciamiento Cloud"` |
| **`diagnostico`** | `String` | Explicación técnica de la anomalía observada. | `"Token de activación de Office caducado en Azure AD..."` |
| **`causa_probable`** | `String` | Enumeración de las causas raíz más probables. | `"1. Desincronización de token OAuth. 2. Licencia no renovada."` |
| **`tiempo_estimado`** | `String` | Rango estimado para completar la mitigación. | `"15 - 30 minutos"` |
| **`pasos_resolucion`** | `Array<String>` | Lista ordenada de pasos técnicos para el técnico. | `["Paso 1: Abrir CMD como admin...", "Paso 2: ..."]` |
| **`comandos_herramientas`** | `Array<String>` | Comandos de consola o utilitarios específicos. | `["cscript OSPP.VBS /dstatus", "dsregcmd /status"]` |
| **`accion_preventiva`** | `String` | Medida de mitigación a largo plazo para evitar recurrencias. | `"Configurar alertas de renovación con 30 días de anticipación."` |
| **`motor_ia`** | `String` | Identificador del motor que generó el diagnóstico. | `"Google Gemini 3.7 Flash (Cloud)"` o `"Motor Experto TI (Local)"` |
| **`variante_index`** | `Integer` | Índice numérico del enfoque (1, 2 o 3). | `1` |
| **`fecha_generacion`** | `String` | Hora en formato `HH:MM:SS` en que se computó la solución. | `"15:45:22"` |

---

## 5. Reglas de Integridad y Restricciones

1. **Integridad Referencial (`FOREIGN KEY`):**
   * La relación entre `solicitudes.usuario_id` y `usuarios.id` posee la regla `ON DELETE SET NULL`, lo cual garantiza que si una cuenta de usuario es eliminada, sus tickets históricos no se borran y permanecen para fines de auditoría y métricas de soporte.
2. **Índice Único de Correo (`UNIQUE KEY`):**
   * La columna `usuarios.email` previene duplicados en el registro de cuentas.
3. **Cifrado Obligatorio de Contraseñas:**
   * La columna `usuarios.password` almacena exclusivamente cadenas hash de 60+ caracteres procesadas mediante BCRYPT.
4. **Conjunto de Caracteres UTF-8 Extendido:**
   * La base de datos utiliza `utf8mb4` para admitir caracteres especiales en español (tildes, eñes) y emojis técnicos en descripciones y diagnósticos.
