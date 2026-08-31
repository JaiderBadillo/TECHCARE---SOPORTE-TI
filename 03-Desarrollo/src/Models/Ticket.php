<?php
/**
 * Modelo de Datos: Ticket (Solicitud de Soporte)
 * Capa de Acceso a Datos (DAO / Active Record)
 */

require_once __DIR__ . '/../../config/database.php';

class Ticket {

    /**
     * Asegura que las columnas usuario_id y empresa existan en la tabla solicitudes
     */
    public static function ensureTableSchema() {
        $conn = Database::getConnection();
        
        // Comprobar columna usuario_id
        $rUser = $conn->query("SHOW COLUMNS FROM solicitudes LIKE 'usuario_id'");
        if ($rUser && $rUser->num_rows === 0) {
            @$conn->query("ALTER TABLE solicitudes ADD COLUMN usuario_id INT NULL AFTER id");
        }

        // Comprobar columna empresa
        $rEmp = $conn->query("SHOW COLUMNS FROM solicitudes LIKE 'empresa'");
        if ($rEmp && $rEmp->num_rows === 0) {
            @$conn->query("ALTER TABLE solicitudes ADD COLUMN empresa VARCHAR(150) NULL AFTER email");
        }
    }
    
    /**
     * Crear una nueva solicitud de soporte
     */
    public static function create($nombre, $email, $asunto, $tipo, $prioridad, $mensaje, $empresa = null, $usuario_id = null) {
        self::ensureTableSchema();
        $conn = Database::getConnection();
        
        $tiposValidos = ['RED', 'SOFTWARE', 'HARDWARE', 'SEGURIDAD', 'CLOUD_SERVIDORES', 'BASE_DE_DATOS'];
        $prioridadesValidas = ['baja', 'media', 'alta', 'critica'];
        
        if (!in_array($tipo, $tiposValidos, true)) $tipo = 'SOFTWARE';
        if (!in_array($prioridad, $prioridadesValidas, true)) $prioridad = 'media';
        
        $stmt = $conn->prepare("INSERT INTO solicitudes (usuario_id, nombre, email, empresa, asunto, tipo_problema, prioridad, mensaje, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
        
        if (!$stmt) {
            // Fallback si la columna no existiera aún
            $stmtFallback = $conn->prepare("INSERT INTO solicitudes (nombre, email, asunto, tipo_problema, prioridad, mensaje, estado) VALUES (?, ?, ?, ?, ?, ?, 'pendiente')");
            if ($stmtFallback) {
                $stmtFallback->bind_param("ssssss", $nombre, $email, $asunto, $tipo, $prioridad, $mensaje);
                $success = $stmtFallback->execute();
                $nuevoId = $stmtFallback->insert_id;
                $stmtFallback->close();
                return ['ok' => $success, 'id' => $nuevoId];
            }
            return ['ok' => false, 'error' => 'Error al preparar inserción: ' . $conn->error];
        }

        $stmt->bind_param("isssssss", $usuario_id, $nombre, $email, $empresa, $asunto, $tipo, $prioridad, $mensaje);
        $success = $stmt->execute();
        $nuevoId = $stmt->insert_id;
        $error = $stmt->error;
        $stmt->close();
        
        if ($success) {
            return ['ok' => true, 'id' => $nuevoId];
        } else {
            return ['ok' => false, 'error' => $error];
        }
    }

    /**
     * Obtener un ticket por su ID
     */
    public static function getById($id) {
        self::ensureTableSchema();
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM solicitudes WHERE id = ?");
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $ticket = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $ticket;
    }

    /**
     * Obtener todos los tickets de un usuario específico (Portal de Cliente)
     */
    public static function getByUser($usuario_id, $email = '') {
        self::ensureTableSchema();
        $conn = Database::getConnection();

        $sql = "SELECT * FROM solicitudes WHERE (usuario_id = ? AND usuario_id IS NOT NULL AND usuario_id > 0) OR (email = ? AND email != '') ORDER BY fecha_creacion DESC";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            // Fallback buscando solo por email
            $stmtFallback = $conn->prepare("SELECT * FROM solicitudes WHERE email = ? ORDER BY fecha_creacion DESC");
            if ($stmtFallback) {
                $stmtFallback->bind_param("s", $email);
                $stmtFallback->execute();
                $res = $stmtFallback->get_result();
                $tickets = [];
                while ($row = $res->fetch_assoc()) $tickets[] = $row;
                $stmtFallback->close();
                return $tickets;
            }
            return [];
        }

        $stmt->bind_param("is", $usuario_id, $email);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $tickets = [];
        while ($row = $res->fetch_assoc()) {
            $tickets[] = $row;
        }
        $stmt->close();
        return $tickets;
    }

    /**
     * Actualizar el estado de un ticket
     */
    public static function updateStatus($id, $estado) {
        $conn = Database::getConnection();
        $estadosValidos = ['pendiente', 'en_proceso', 'resuelto'];
        
        if (!in_array($estado, $estadosValidos, true)) {
            return ['ok' => false, 'error' => 'Estado no válido'];
        }
        
        $stmt = $conn->prepare("UPDATE solicitudes SET estado = ? WHERE id = ?");
        if (!$stmt) return ['ok' => false, 'error' => $conn->error];

        $stmt->bind_param("si", $estado, $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return ['ok' => $success];
    }

    /**
     * Guardar la solución diagnóstica generada por IA
     */
    public static function saveIASolution($id, $solucionData) {
        $conn = Database::getConnection();
        $jsonStr = json_encode($solucionData, JSON_UNESCAPED_UNICODE);
        
        $stmt = $conn->prepare("UPDATE solicitudes SET solucion_ia = ? WHERE id = ?");
        if (!$stmt) return ['ok' => false, 'error' => $conn->error];

        $stmt->bind_param("si", $jsonStr, $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return ['ok' => $success];
    }

    /**
     * Obtener listado de tickets con filtros opcionales (Dashboard Administrativo)
     */
    public static function getAll($filtroEstado = '', $filtroTipo = '', $filtroPrioridad = '', $filtroBusqueda = '') {
        self::ensureTableSchema();
        $conn = Database::getConnection();
        
        $where = [];
        $params = [];
        $types = '';

        if (!empty($filtroEstado)) {
            $where[] = "estado = ?";
            $params[] = $filtroEstado;
            $types .= 's';
        }
        if (!empty($filtroTipo)) {
            $where[] = "tipo_problema = ?";
            $params[] = $filtroTipo;
            $types .= 's';
        }
        if (!empty($filtroPrioridad)) {
            $where[] = "prioridad = ?";
            $params[] = $filtroPrioridad;
            $types .= 's';
        }
        if (!empty($filtroBusqueda)) {
            $where[] = "(nombre LIKE ? OR email LIKE ? OR asunto LIKE ?)";
            $busq = "%$filtroBusqueda%";
            $params[] = $busq;
            $params[] = $busq;
            $params[] = $busq;
            $types .= 'sss';
        }

        $sql = "SELECT * FROM solicitudes";
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY fecha_creacion DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) return [];

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        
        $tickets = [];
        while ($row = $res->fetch_assoc()) {
            $tickets[] = $row;
        }
        $stmt->close();
        
        return $tickets;
    }

    /**
     * Obtener métricas agregadas completas para el Dashboard y Hub de IA
     */
    public static function getMetrics() {
        self::ensureTableSchema();
        $conn = Database::getConnection();
        
        // Total global
        $rTotal = $conn->query("SELECT COUNT(*) as total FROM solicitudes");
        $rowTotal = $rTotal ? $rTotal->fetch_assoc() : null;
        $total = (int)($rowTotal['total'] ?? 0);
        
        // Este mes
        $rMes = $conn->query("SELECT COUNT(*) as total_mes FROM solicitudes WHERE MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())");
        $rowMes = $rMes ? $rMes->fetch_assoc() : null;
        $esteMes = (int)($rowMes['total_mes'] ?? 0);
        
        // Estados
        $rEstados = $conn->query("SELECT estado, COUNT(*) as c FROM solicitudes GROUP BY estado");
        $estados = ['pendiente' => 0, 'en_proceso' => 0, 'resuelto' => 0];
        if ($rEstados) {
            while ($row = $rEstados->fetch_assoc()) {
                $estados[$row['estado']] = (int)$row['c'];
            }
        }
        
        // Prioridades
        $rPrios = $conn->query("SELECT prioridad, COUNT(*) as c FROM solicitudes GROUP BY prioridad");
        $prioridades = ['baja' => 0, 'media' => 0, 'alta' => 0, 'critica' => 0];
        if ($rPrios) {
            while ($row = $rPrios->fetch_assoc()) {
                $prioridades[$row['prioridad']] = (int)$row['c'];
            }
        }
        
        // Distribución por Tipo de Problema con métricas de resolución
        $rTipos = $conn->query("
            SELECT 
                tipo_problema, 
                COUNT(*) as cantidad,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'resuelto' THEN 1 ELSE 0 END) as resueltos,
                SUM(CASE WHEN prioridad = 'critica' THEN 1 ELSE 0 END) as criticos,
                SUM(CASE WHEN prioridad = 'alta' THEN 1 ELSE 0 END) as altos
            FROM solicitudes 
            GROUP BY tipo_problema
            ORDER BY cantidad DESC
        ");
        
        $datosTipos = [];
        $tipoMayorDemanda = 'N/A';
        $maxTipoCant = -1;
        
        if ($rTipos) {
            while ($row = $rTipos->fetch_assoc()) {
                $tipo = $row['tipo_problema'];
                $cant = (int)$row['cantidad'];
                $pct = $total > 0 ? round(($cant / $total) * 100, 1) : 0;
                
                if ($cant > $maxTipoCant) {
                    $maxTipoCant = $cant;
                    $tipoMayorDemanda = $tipo;
                }
                
                $datosTipos[$tipo] = [
                    'tipo' => $tipo,
                    'cantidad' => $cant,
                    'porcentaje' => $pct,
                    'pendientes' => (int)$row['pendientes'],
                    'resueltos' => (int)$row['resueltos'],
                    'criticos' => (int)$row['criticos'],
                    'altos' => (int)$row['altos']
                ];
            }
        }
        
        // Tendencia mensual (últimos 6 meses)
        $rMeses = $conn->query("
            SELECT DATE_FORMAT(fecha_creacion, '%Y-%m') as mes, COUNT(*) as total 
            FROM solicitudes 
            GROUP BY mes 
            ORDER BY mes ASC 
            LIMIT 6
        ");
        $mesesLabels = [];
        $mesesData = [];
        if ($rMeses) {
            while ($row = $rMeses->fetch_assoc()) {
                $mesesLabels[] = $row['mes'];
                $mesesData[] = (int)$row['total'];
            }
        }
        
        $tasaResolucion = $total > 0 ? round(($estados['resuelto'] / $total) * 100, 1) : 0;
        
        return [
            'total' => $total,
            'esteMes' => $esteMes,
            'estados' => $estados,
            'prioridades' => $prioridades,
            'tipos' => $datosTipos,
            'tipoMayorDemanda' => $tipoMayorDemanda,
            'tasaResolucion' => $tasaResolucion,
            'tendenciaMeses' => [
                'labels' => $mesesLabels,
                'data' => $mesesData
            ]
        ];
    }
}
