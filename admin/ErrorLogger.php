<?php
class ErrorLogger {
    private $pdo;

    public function __construct($host, $db, $user, $pass) {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function log($message, $file, $line) {
        $sql = "INSERT INTO error_logs (message, error_file, error_line) VALUES (:message, :error_file, :error_line)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'message' => $message,
            'error_file' => $file,
            'error_line' => $line
        ]);
    }
}