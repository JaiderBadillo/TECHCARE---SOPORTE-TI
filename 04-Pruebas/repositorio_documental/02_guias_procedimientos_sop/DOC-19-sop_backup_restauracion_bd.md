# 💾 SOP-DB-008: Rutina de Respaldo y Restauración de Base de Datos
**Código:** SOP-DB-008 | **Periodicidad:** Diaria (Incremental) / Semanal (Completa)

## 1. Comando de Respaldo Lógico Seguro
```bash
# Respaldo completo de la base de datos soporte_db con rutinas y triggers
mysqldump -u root -p --single-transaction --routines --triggers soporte_db > /backup/soporte_db_$(date +%Y%m%d).sql
```

## 2. Prueba de Restauración en Servidor de Pruebas
```bash
# Restaurar sobre base de datos de pruebas limpia
mysql -u root -p -e "DROP DATABASE IF EXISTS soporte_db_test; CREATE DATABASE soporte_db_test;"
mysql -u root -p soporte_db_test < /backup/soporte_db_20260901.sql
```
