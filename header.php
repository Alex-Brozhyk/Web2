<?php
// header.php - Спільний шаблон шапки та навігації
$pageTitle = $pageTitle ?? 'Лабораторна робота №7 — PHP та MySQL';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Підключаємо існуючий Tailwind CSS та додаткові стилі з Лабораторної №2-3 -->
    <link rel="stylesheet" href="./output.css">
    <link rel="stylesheet" href="./custom.css">
</head>
<body class="bg-gray-100 font-sans m-0 p-0">

    <!-- Головний контейнер для двоколонкового макета -->
    <div class="flex flex-col md:flex-row min-h-screen">
        
        <!-- Ліве меню -->
        <aside class="w-full md:w-64 bg-darkSidebar text-white p-6 flex flex-col justify-between">
            <div>
                <!-- Логотип -->
                <div class="bg-white text-black font-bold p-3 text-center rounded mb-8 text-xl shadow">
                    LOGO
                </div>
                
                <!-- Навігаційні секції -->
                <nav class="flex flex-col space-y-2" id="sidebar-nav">
                    <div class="nav-item">
                        <a href="index.php" class="block p-2 rounded transition font-semibold <?= $currentPage === 'index.php' ? 'bg-blue-600 text-white' : 'hover:bg-gray-700' ?>">
                            Калькулятор (Форма POST)
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="history.php" class="block p-2 rounded transition font-semibold <?= $currentPage === 'history.php' ? 'bg-blue-600 text-white' : 'hover:bg-gray-700' ?>">
                            Історія в БД (GET-сортування)
                        </a>
                    </div>

                    <div class="nav-item pt-4 border-t border-gray-700">
                        <button type="button" class="nav-toggle w-full text-left p-2 rounded hover:bg-gray-700 transition flex justify-between items-center font-semibold">
                            <span>Довідка по лабі</span>
                            <span class="arrow transition-transform duration-200">▼</span>
                        </button>
                        <div class="nav-content pl-4 py-2 text-sm text-gray-300 hidden">
                            <p class="py-1">Лабораторна №7</p>
                            <p class="py-1">PHP + MySQL (PDO)</p>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="text-xs text-gray-400 mt-8">
                IP: <?= htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1') ?>
            </div>
        </aside>

        <!-- Права частина з контентом -->
        <main class="flex-1 p-6 md:p-8">
