<?php
 // Datos de la bd
class Conexion {
    private $host = "localhost";
    private $dbname = "inces1";
    private $username = "root";
    private $password = "";
    private $conn;

    public function __construct() {
        try {
            $this->host = "localhost";
            $this->dbname = "inces1";
            $this->username = "root";
            $this->password = "";
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion() {
        return $this->conn;
    }
}

