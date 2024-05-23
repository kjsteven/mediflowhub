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

    public function logLoginEvent($userId) {
        $eventName = "User Login";
        $eventDescription = "User with ID $userId logged in";
        $this->logEvent($eventName, $eventDescription, $userId);
    }

    public function logLogoutEvent($userId) {
        $eventName = "User Logout";
        $eventDescription = "User with ID $userId logged out";
        $this->logEvent($eventName, $eventDescription, $userId);
    }

    public function logAppointmentCreation($userId, $appointmentId) {
        $eventName = "Appointment Creation";
        $eventDescription = "User with ID $userId booked an appointment with ID $appointmentId";
        $this->logEvent($eventName, $eventDescription, $userId);
    }

    public function logPasswordChangeEvent($userId) {
        $eventName = "Password Change";
        $eventDescription = "User with ID $userId changed their password";
        $this->logEvent($eventName, $eventDescription, $userId);
    }

    public function logAddressChangeEvent($userId, $oldAddress, $newAddress) {
        $eventName = "Address Change";
        $eventDescription = "User with ID $userId changed their address from $oldAddress to $newAddress";
        $this->logEvent($eventName, $eventDescription, $userId);
    }

    private function logEvent($eventName, $eventDescription, $userId = null) {
        $sql = "INSERT INTO event_logs (event_name, event_description, user_id) VALUES (:event_name, :event_description, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'event_name' => $eventName,
            'event_description' => $eventDescription,
            'user_id' => $userId
        ]);
    }
}

?>
