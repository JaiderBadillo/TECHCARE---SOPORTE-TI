# 🚀 Carpeta 05 - Implementación y Despliegue en Producción
**Proyecto:** TechCare Soporte TI — Mesa de Ayuda Inteligente  
**Fase:** Implementación, Despliegue en la Nube y DevOps  
**Desarrollador:** Jaider Augusto Niño Badillo  
**Versión:** 2.2.0  

---

## 📂 Contenido del Directorio

En esta carpeta se encuentran los manuales de puesta en marcha y guías de despliegue en entornos cloud:

| Documento | Descripción |
| :--- | :--- |
| 📘 **[Guia_Despliegue_Render.md](Guia_Despliegue_Render.md)** | **Guía Oficial de Despliegue en Render:** Procedimiento completo paso a paso para desplegar TechCare (PHP 8.2 + Apache con Docker), el motor de automatización n8n en la nube y la base de datos MySQL remota. |

---

## 🐳 Archivos de Contenerización en la Raíz del Proyecto

* 📄 **[`Dockerfile`](../Dockerfile):** Imagen oficial de PHP 8.2 con Apache, extensiones MySQLi, PDO MySQL, cURL, MBString y adaptación dinámica de puerto (`$PORT`) para Render.
* 📄 **[`.dockerignore`](../.dockerignore):** Optimización de tamaño de imagen para despliegues rápidos en Render.
