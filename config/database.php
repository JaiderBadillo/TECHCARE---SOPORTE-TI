<?php
/**
 * Conexión centralizada a la base de datos MySQL con Auto-Detección de Puertos
 * TechCare Soporte TI
 */

mysqli_report(MYSQLI_REPORT_OFF);

class Database {
    private static $host = '127.0.0.1';
    private static $user = 'root';
    private static $pass = '';
    private static $db   = 'soporte_db';
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn !== null) {
            return self::$conn;
        }

        $ports = [3306, 3307, 3308];
        $conn = null;
        $connected = false;

        foreach ($ports as $port) {
            try {
                $conn = @new mysqli(self::$host, self::$user, self::$pass, self::$db, $port);
                if (!$conn->connect_error) {
                    $connected = true;
                    break;
                }

                // Si falló por no existir la BD soporte_db, intentar crearla
                $connRoot = @new mysqli(self::$host, self::$user, self::$pass, '', $port);
                if (!$connRoot->connect_error) {
                    $connRoot->query("CREATE DATABASE IF NOT EXISTS " . self::$db . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $connRoot->close();
                    
                    $conn = @new mysqli(self::$host, self::$user, self::$pass, self::$db, $port);
                    if (!$conn->connect_error) {
                        $connected = true;
                        break;
                    }
                }
            } catch (Exception $e) {
                // Continuar probando siguiente puerto
            }
        }

        // Intento con socket / localhost estándar
        if (!$connected) {
            try {
                $conn = @new mysqli('localhost', self::$user, self::$pass, self::$db);
                if (!$conn->connect_error) {
                    $connected = true;
                }
            } catch (Exception $e) {}
        }

        if (!$connected || !$conn || $conn->connect_error) {
            if (isset($_GET['action']) || isset($_POST['action']) || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok' => false,
                    'error' => 'No se pudo conectar a MySQL. Asegúrate de que XAMPP o el servicio de MySQL esté iniciado.'
                ]);
                exit;
            }

            // Vista amigable para el usuario si MySQL está apagado
            http_response_code(503);
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
              <meta charset="UTF-8">
              <title>Servidor MySQL no disponible - TechCare</title>
              <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
              <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
              <style>body { font-family: system-ui, sans-serif; background: #0f172a; color: #f8fafc; display: flex; align-items: center; min-height: 100vh; }</style>
            </head>
            <body>
              <div class="container py-5">
                <div class="row justify-content-center">
                  <div class="col-md-7 col-lg-6 text-center">
                    <div class="card bg-dark border border-secondary border-opacity-50 p-4 p-md-5 rounded-4 shadow-lg text-white">
                      <div class="mb-3 text-warning">
                        <i class="bi bi-database-fill-exclamation" style="font-size: 3.5rem;"></i>
                      </div>
                      <h3 class="fw-bold mb-2">Servidor MySQL no disponible</h3>
                      <p class="text-white-50 small mb-4">
                        No se pudo establecer conexión con la base de datos MySQL (puertos 3306 / 3307).
                      </p>
                      <div class="alert bg-black bg-opacity-50 border border-secondary text-start small mb-4">
                        <strong class="text-info d-block mb-1"><i class="bi bi-tools me-1"></i> ¿Cómo solucionarlo?</strong>
                        <ol class="mb-0 ps-3 text-white-70">
                          <li>Abre tu panel de <strong>XAMPP</strong>, <strong>WampServer</strong> o <strong>Servicios de Windows</strong>.</li>
                          <li>Haz clic en el botón <strong>Start</strong> en el módulo <strong>MySQL</strong>.</li>
                          <li>Una vez iniciado en verde, recarga esta página.</li>
                        </ol>
                      </div>
                      <button onclick="location.reload()" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reintentar Conexión
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </body>
            </html>
            <?php
            exit;
        }

        $conn->set_charset('utf8mb4');
        self::$conn = $conn;
        return self::$conn;
    }
}
