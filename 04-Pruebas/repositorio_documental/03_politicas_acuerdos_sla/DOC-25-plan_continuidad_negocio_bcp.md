# 🔄 Plan de Continuidad del Negocio (BCP) y Recuperación ante Desastres (DRP)
**Alcance:** Mesa de Ayuda TechCare e Infraestructura de TI

## 1. Objetivos de Tiempo y Punto de Recuperación
* **RTO (Recovery Time Objective):** Máximo 2 horas para restablecimiento completo del servicio de Mesa de Ayuda ante caída total.
* **RPO (Recovery Point Objective):** Pérdida máxima admisible de datos de 15 minutos mediante replicación continua de base de datos MySQL.

## 2. Estrategia de Redundancia Híbrida
En caso de fallo masivo de conectividad externa a la nube de Google Gemini, el sistema continuará prestando diagnósticos y triaje mediante el **Motor Experto Local NLP a 0 Tokens**, garantizando continuidad operativa autónoma.
