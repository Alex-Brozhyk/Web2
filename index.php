<?php
require_once 'db.php';

$pageTitle = 'Лабораторна робота №7 — Калькулятор (POST + MySQL)';
$errors = [];
$successMessage = '';
$calcResult = '—';

$num1Input = '';
$num2Input = '';
$operatorInput = '+';
$saveHistoryInput = 'yes';
$emailInput = '';

// Обробка POST-запиту після відправки форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num1Raw = trim($_POST['num1'] ?? '');
    $num2Raw = trim($_POST['num2'] ?? '');
    $operatorInput = $_POST['operator'] ?? '+';
    $saveHistoryInput = $_POST['save_history'] ?? 'yes';
    $emailInput = trim($_POST['user_email'] ?? '');

    $num1Input = htmlspecialchars($num1Raw);
    $num2Input = htmlspecialchars($num2Raw);

    // Валідація числових полів
    if ($num1Raw === '' || !is_numeric($num1Raw)) {
        $errors[] = 'Будь ласка, введіть коректне перше число.';
    }
    if ($num2Raw === '' || !is_numeric($num2Raw)) {
        $errors[] = 'Будь ласка, введіть коректне друге число.';
    }

    // Валідація оператора
    $allowedOperators = ['+', '-', '*', '/'];
    if (!in_array($operatorInput, $allowedOperators, true)) {
        $errors[] = 'Обрано невірну математичну операцію.';
    }

    // Валідація email
    if ($emailInput === '') {
        $errors[] = 'Введіть ваш E-mail для журналу логів.';
    } elseif (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некоректний формат електронної пошти (E-mail).';
    }

    // Перевірка ділення на нуль
    if (empty($errors) && $operatorInput === '/' && (float)$num2Raw == 0.0) {
        $errors[] = 'Помилка: ділення на нуль неможливе!';
    }

    // Якщо помилок немає — виконуємо обчислення та зберігаємо в MySQL
    if (empty($errors)) {
        $a = (float)$num1Raw;
        $b = (float)$num2Raw;

        switch ($operatorInput) {
            case '+':
                $resVal = $a + $b;
                break;
            case '-':
                $resVal = $a - $b;
                break;
            case '*':
                $resVal = $a * $b;
                break;
            case '/':
                $resVal = round($a / $b, 4);
                break;
        }

        $calcResult = (string)$resVal;

        if ($saveHistoryInput === 'yes') {
            if (saveCalculation($pdo, $a, $b, $operatorInput, $calcResult, htmlspecialchars($emailInput))) {
                $successMessage = "Обчислення ($a $operatorInput $b = $calcResult) успішно виконано та збережено в базу даних MySQL!";
            } else {
                $errors[] = "Не вдалося зберегти запис у базу даних.";
            }
        } else {
            $successMessage = "Обчислення виконано (без збереження в базу даних).";
        }
    }
}

// Отримуємо останні 5 записів для швидкого перегляду праворуч
$recentLogs = array_slice(getCalculations($pdo, 'created_at', 'DESC'), 0, 5);

include 'header.php';
?>

<h1 class="text-3xl font-bold text-customBlue mb-6">Панель керування контентом — Лабораторна №7</h1>

<!-- Блок повідомлень про помилки або успіх -->
<?php if (!empty($errors)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow" role="alert">
        <p class="font-bold">Виникли помилки валідації:</p>
        <ul class="list-disc pl-5 mt-1 text-sm">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($successMessage !== ''): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded shadow" role="alert">
        <p class="font-semibold"><?= htmlspecialchars($successMessage) ?></p>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- Секція 1: Форма калькулятора (POST) -->
    <section id="calculator-section" class="bg-white p-6 rounded-lg shadow border border-gray-200">
        <h2 class="text-xl font-bold mb-4 text-customBlue">Калькулятор (Надсилання в MySQL)</h2>
        
        <form action="index.php" method="POST" id="calc-form" class="space-y-4">
            <!-- Поле 1 -->
            <div>
                <label for="num1" class="block text-sm font-semibold text-gray-700 mb-1">Перше число:</label>
                <input type="number" step="any" id="num1" name="num1" value="<?= $num1Input ?>" placeholder="Введіть число..." required
                       class="custom-input w-full p-2 border border-gray-300 rounded">
            </div>

            <!-- Поле 2 -->
            <div>
                <label for="num2" class="block text-sm font-semibold text-gray-700 mb-1">Друге число:</label>
                <input type="number" step="any" id="num2" name="num2" value="<?= $num2Input ?>" placeholder="Введіть число..." required
                       class="custom-input w-full p-2 border border-gray-300 rounded">
            </div>

            <!-- Вибір операції (select) -->
            <div>
                <label for="operator" class="block text-sm font-semibold text-gray-700 mb-1">Математична операція:</label>
                <select id="operator" name="operator" class="custom-input w-full p-2 border border-gray-300 rounded bg-white">
                    <option value="+" <?= $operatorInput === '+' ? 'selected' : '' ?>>Додавання (+)</option>
                    <option value="-" <?= $operatorInput === '-' ? 'selected' : '' ?>>Віднімання (-)</option>
                    <option value="*" <?= $operatorInput === '*' ? 'selected' : '' ?>>Множення (*)</option>
                    <option value="/" <?= $operatorInput === '/' ? 'selected' : '' ?>>Ділення (/)</option>
                </select>
            </div>

            <!-- Поле Email -->
            <div>
                <label for="user_email" class="block text-sm font-semibold text-gray-700 mb-1">E-mail автора обчислення:</label>
                <input type="email" id="user_email" name="user_email" value="<?= htmlspecialchars($emailInput) ?>" placeholder="student@example.com" required
                       class="custom-input w-full p-2 border border-gray-300 rounded">
            </div>

            <!-- Випадаючий список "Зберегти до історії" -->
            <div>
                <label for="save-history" class="block text-sm font-semibold text-gray-700 mb-1">Зберегти лог у базу даних:</label>
                <select id="save-history" name="save_history" class="custom-input w-full p-2 border border-gray-300 rounded bg-white">
                    <option value="yes" <?= $saveHistoryInput === 'yes' ? 'selected' : '' ?>>Так, зберегти в MySQL</option>
                    <option value="no" <?= $saveHistoryInput === 'no' ? 'selected' : '' ?>>Ні, лише обчислити</option>
                </select>
            </div>

            <!-- Кнопка відправки форми -->
            <div>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-blue-700 transition shadow">
                    Обчислити та відправити (POST)
                </button>
            </div>

            <!-- Поле для відображення результату на формі -->
            <div class="bg-blue-50 p-4 rounded border-l-4 border-blue-500">
                <span class="text-sm font-semibold text-blue-800">Результат виконання: </span>
                <span id="calc-result" class="text-lg font-bold text-black"><?= htmlspecialchars($calcResult) ?></span>
            </div>
        </form>
    </section>

    <!-- Секція 2: Останні записи з MySQL -->
    <section id="history-section" class="bg-white p-6 rounded-lg shadow border border-gray-200 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-customBlue">Останні 5 записів у БД</h2>
                <a href="history.php" class="bg-gray-800 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Уся таблиця &rarr;
                </a>
            </div>
            
            <?php if (empty($recentLogs)): ?>
                <p class="text-gray-600 text-sm">База даних поки що порожня. Виконайте перше обчислення ліворуч.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($recentLogs as $row): ?>
                        <li class="p-3 bg-gray-100 rounded border-l-4 border-green-500 text-sm flex justify-between items-center">
                            <div>
                                <span class="font-bold">
                                    <?= htmlspecialchars($row['num1']) ?> <?= htmlspecialchars($row['operator']) ?> <?= htmlspecialchars($row['num2']) ?> = <?= htmlspecialchars($row['result']) ?>
                                </span>
                                <span class="block text-xs text-gray-500"><?= htmlspecialchars($row['user_email']) ?></span>
                            </div>
                            <span class="text-xs text-gray-600"><?= htmlspecialchars($row['created_at']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="mt-6 pt-4 border-t border-gray-200 text-xs text-gray-500">
            Дані автоматично завантажуються з таблиці <code>calculations</code> бази даних <code>web2_lab07</code>.
        </div>
    </section>

</div>

<?php include 'footer.php'; ?>
