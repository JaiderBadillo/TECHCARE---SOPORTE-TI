# 🚨 SOP-SEC-008: Aislamiento Inmediato de Endpoint ante Sospecha de Intrusión
**Código:** SOP-SEC-008 | **Nivel de Urgencia:** Crítica (Nivel 1)

## 1. Acción Física Inmediata (0 a 2 Minutos)
1. Indicar al usuario que desconecte el cable de red RJ-45 de la estación de trabajo.
2. Apagar el interruptor de red WiFi del equipo portátil o desactivar el modo avión.
3. **NO** apagar ni formatear el equipo inmediatamente, para preservar la memoria RAM y evidencia forense volátil.

## 2. Contención por Consola Remota EDR
```powershell
# Aislar el host de la red manteniendo solo comunicación con consola de seguridad
Invoke-RestMethod -Uri "https://edr-cloud.demo/api/v1/hosts/WS-042/isolate" -Method Post

# Adquirir volcado de memoria de procesos sospechosos
Get-Process | Where-Object {$_.CPU -gt 50} | Out-File "C:\forense_procesos.txt"
```
