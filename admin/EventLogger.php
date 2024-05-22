<?php

class EventLogger {
    private $pdo;

    public function __construct() {
        require '../config/databaseconfig.php';

        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        } catch (PDOException $e) {
            throw new RuntimeException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function logEvent($eventName, $eventDescription = '', $eventUser = '') {
        $sql = "INSERT INTO event_logs (event_name, event_description, event_user) VALUES (:event_name, :event_description, :event_user)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'event_name' => $eventName,
            'event_description' => $eventDescription,
            'event_user' => $eventUser
        ]);
    }
}

?>
