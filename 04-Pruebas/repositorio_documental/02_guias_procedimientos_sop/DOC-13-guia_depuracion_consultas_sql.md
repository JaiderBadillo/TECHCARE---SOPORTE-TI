# 🗄️ SOP-DB-004: Diagnóstico y Mitigación de Bloqueos en MySQL / MariaDB
**Código:** SOP-DB-004 | **Área:** Infraestructura y Base de Datos

## 1. Identificación de Consultas Lentas y Bloqueos
Cuando la aplicación TechCare o el ERP presenten lentitud generalizada:

```sql
-- 1. Consultar todos los hilos y transacciones en ejecución
SHOW FULL PROCESSLIST;

-- 2. Inspeccionar transacciones InnoDB en espera de cerrojos
SELECT 
    r.trx_id waiting_trx_id,
    r.trx_mysql_thread_id waiting_thread,
    b.trx_id blocking_trx_id,
    b.trx_mysql_thread_id blocking_thread
FROM performance_schema.data_lock_waits w
INNER JOIN information_schema.innodb_trx b ON b.trx_id = w.blocking_engine_transaction_id
INNER JOIN information_schema.innodb_trx r ON r.trx_id = w.requesting_engine_transaction_id;

-- 3. Finalizar hilo causante del bloqueo (sustituir por el ID correspondiente)
KILL 4210;
```
