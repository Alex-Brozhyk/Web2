<?php
require_once 'db.php';

$pageTitle = 'Історія обчислень (MySQL + GET-сортування)';

// Обробка очищення історії (POST-запит з цієї сторінки)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear') {
    clearCalculations($pdo);
    header("Location: history.php?cleared=1");
    exit;
}

// Зчитування параметрів сортування з URL ($_GET)
$sortBy = $_GET['sort'] ?? 'created_at';
$order  = $_GET['order'] ?? 'DESC';

// Визначаємо протилежний напрямок сортування для кнопок
$nextOrder = (strtoupper($order) === 'ASC') ? 'DESC' : 'ASC';

// Отримуємо відсортовані записи
$records = getCalculations($pdo, $sortBy, $order);

include 'header.php';
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-3xl font-bold text-customBlue">Журнал обчислень у базі даних</h1>
        <p class="text-sm text-gray-600 mt-1">Поточне сортування: <strong><?= htmlspecialchars($sortBy) ?></strong> (<?= htmlspecialchars(strtoupper($order)) ?>)</p>
    </div>

    <div class="flex flex-wrap gap-2">
        <!-- Кнопки сортування через URL (?sort=...&order=...) -->
        <a href="history.php?sort=created_at&order=<?= $sortBy === 'created_at' ? $nextOrder : 'DESC' ?>" 
           class="px-4 py-2 rounded-lg text-sm font-semibold transition <?= $sortBy === 'created_at' ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100' ?>">
            За датою <?= $sortBy === 'created_at' ? ($order === 'ASC' ? '↑' : '↓') : '' ?>
        </a>

        <a href="history.php?sort=result&order=<?= $sortBy === 'result' ? $nextOrder : 'DESC' ?>" 
           class="px-4 py-2 rounded-lg text-sm font-semibold transition <?= $sortBy === 'result' ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100' ?>">
            За результатом <?= $sortBy === 'result' ? ($order === 'ASC' ? '↑' : '↓') : '' ?>
        </a>

        <a href="history.php?sort=user_email&order=<?= $sortBy === 'user_email' ? $nextOrder : 'ASC' ?>" 
           class="px-4 py-2 rounded-lg text-sm font-semibold transition <?= $sortBy === 'user_email' ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100' ?>">
            За E-mail <?= $sortBy === 'user_email' ? ($order === 'ASC' ? '↑' : '↓') : '' ?>
        </a>

        <!-- Форма для очищення БД -->
        <form action="history.php" method="POST" onsubmit="return confirm('Ви дійсно хочете видалити всі записи з бази даних?');">
            <input type="hidden" name="action" value="clear">
            <button type="submit" class="bg-red-500 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-red-700 transition">
                Очистити БД
            </button>
        </form>
    </div>
</div>

<?php if (isset($_GET['cleared'])): ?>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 mb-6 rounded shadow">
        Таблицю логів успішно очищено!
    </div>
<?php endif; ?>

<section class="bg-white rounded-lg shadow border border-gray-200 overflow-x-auto">
    <?php if (empty($records)): ?>
        <div class="p-8 text-center text-gray-600">
            <p class="mb-4">У базі даних ще немає жодного запису.</p>
            <a href="index.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                Перейти до калькулятора
            </a>
        </div>
    <?php else: ?>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-800 text-white text-sm uppercase">
                    <th class="p-3 border-b">ID</th>
                    <th class="p-3 border-b">Вираз</th>
                    <th class="p-3 border-b">Результат</th>
                    <th class="p-3 border-b">E-mail користувача</th>
                    <th class="p-3 border-b">Дата та час запису</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
                <?php foreach ($records as $row): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3 font-semibold text-gray-700"><?= htmlspecialchars($row['id']) ?></td>
                        <td class="p-3">
                            <?= htmlspecialchars($row['num1']) ?> 
                            <span class="font-bold text-blue-600"><?= htmlspecialchars($row['operator']) ?></span> 
                            <?= htmlspecialchars($row['num2']) ?>
                        </td>
                        <td class="p-3 font-bold text-green-700"><?= htmlspecialchars($row['result']) ?></td>
                        <td class="p-3 text-gray-600"><?= htmlspecialchars($row['user_email']) ?></td>
                        <td class="p-3 text-gray-500"><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
