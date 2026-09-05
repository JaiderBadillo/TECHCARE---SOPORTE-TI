# 🛡️ Informe Forense: Simulacro de Contención de Malware
**Identificador del Simulacro:** SIM-CYBER-2026-03  
**Fecha:** 3 de Septiembre de 2026  
**Responsable Técnico:** Especialista de Soporte TI y Seguridad  

## 1. Resumen del Escenario de Prueba
Se simuló la descarga inadvertida de una muestra controlada tipo *Ransomware Eicar / Canary File* en la estación de trabajo de pruebas `WS-TEST-LAB-04`.

## 2. Indicadores de Compromiso (IoCs)
* **Archivo origen:** `factura_proveedor_agosto.pdf.exe`
* **Directorio de ejecución:** `C:\Users\Usuario_Prueba\Downloads`
* **Hash SHA-256:** `e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855`
* **Comportamiento anómalo:** Intento de modificación masiva de extensiones a `.locked`.

## 3. Protocolo de Respuesta Ejecutado
1. **Aislamiento de Red:** Deshabilitación de tarjeta de red mediante comando `netsh interface set interface "Ethernet" disable`.
2. **Identificación de Proceso:** Captura de identificador de proceso activo `PID 5524` y corte inmediato.
3. **Restauración:** Restauración de instantánea limpia desde Volume Shadow Copy (VSS) en menos de 10 minutos.
