<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class Conexion {
    private $conexion;

    public function __construct() {
        $this->conectar();
    }

    private function conectar() {
        try {
            // Cargar las variables del archivo .env
            $dotenv = Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();

            // Usar las variables de entorno
            $host = $_ENV['DB_HOST'];
            $usuario = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];
            $db = $_ENV['DB_NAME'];

            // Crear conexión PDO
            $this->conexion = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $usuario, $password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            die();
        } catch (Exception $e) {
            echo "Error al cargar el archivo .env: " . $e->getMessage();
            die();
        }
    }

    public function getConexion() {
        return $this->conexion;
    }

    public function cerrarConexion() {
        $this->conexion = null;
    }
}
?>
