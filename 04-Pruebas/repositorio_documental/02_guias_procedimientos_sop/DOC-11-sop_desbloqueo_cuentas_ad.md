# 📘 SOP-TI-001: Procedimiento Operativo Estándar para Desbloqueo de Cuentas y Blanqueo de Contraseñas
**Código:** SOP-TI-001 | **Revisión:** v3.2 | **Área:** Mesa de Ayuda TI

## 1. Propósito
Establecer el procedimiento seguro y estandarizado para atender solicitudes de usuarios que hayan bloqueado su acceso al Directorio Activo o hayan olvidado su contraseña corporativa.

## 2. Flujo de Validación de Identidad Obligatorio
Antes de modificar cualquier parámetro de autenticación:
1. El técnico debe verificar que la solicitud provenga de un ticket formal radicado en TechCare.
2. Solicitar al usuario confirmación de su número de identificación corporativo y jefatura inmediata.
3. No se procesarán solicitudes recibidas por canales no auditables (WhatsApp personal, pasillo).

## 3. Pasos Técnicos de Ejecución
```powershell
# 1. Comprobar estado de bloqueo en Active Directory
Get-ADUser -Identity "usuario.demo" -Properties LockedOut | Select-Object Name, LockedOut

# 2. Desbloquear la cuenta corporativa
Unlock-ADUser -Identity "usuario.demo"

# 3. Restablecer contraseña temporal forzando cambio en primer inicio
Set-ADAccountPassword -Identity "usuario.demo" -NewPassword (ConvertTo-SecureString "TempPass2026#" -AsPlainText -Force)
Set-ADUser -Identity "usuario.demo" -ChangePasswordAtLogon $true
```
