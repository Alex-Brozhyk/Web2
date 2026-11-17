<?php
// db.php - Конфігурація та функції роботи з базою даних MySQL
$host = 'localhost';
$db   = 'web2_lab07';
$user = 'root';
$pass = ''; // Вкажіть пароль від MySQL (наприклад, для Docker)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Помилка підключення до бази даних: " . htmlspecialchars($e->getMessage()));
}

/**
 * Зберігає новий запис обчислення в базу даних за допомогою Prepared Statement
 */
function saveCalculation(PDO $pdo, float $num1, float $num2, string $operator, string $result, string $userEmail): bool {
    $sql = "INSERT INTO calculations (num1, num2, operator, result, user_email) VALUES (:num1, :num2, :operator, :result, :user_email)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':num1'       => $num1,
        ':num2'       => $num2,
        ':operator'   => $operator,
        ':result'     => $result,
        ':user_email' => $userEmail
    ]);
}

/**
 * Отримує список усіх обчислень із можливістю сортування через безпечний білий список полів
 */
function getCalculations(PDO $pdo, string $sortBy = 'created_at', string $order = 'DESC'): array {
    $allowedSortFields = ['id', 'num1', 'num2', 'operator', 'result', 'user_email', 'created_at'];
    if (!in_array($sortBy, $allowedSortFields, true)) {
        $sortBy = 'created_at';
    }

    $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

    $sql = "SELECT id, num1, num2, operator, result, user_email, created_at FROM calculations ORDER BY $sortBy $order";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Очищає всю таблицю логів
 */
function clearCalculations(PDO $pdo): bool {
    $stmt = $pdo->prepare("TRUNCATE TABLE calculations");
    return $stmt->execute();
}
