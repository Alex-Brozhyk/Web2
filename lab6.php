<?php
// ==========================================
// ЛАБОРАТОРНА РОБОТА №6: Комплексний PHP CLI скрипт
// ==========================================

// Функція для перевірки цілочисельного введення
function validateNumber($input) {
    return filter_var(trim($input), FILTER_VALIDATE_INT) !== false;
}

// ------------------------------------------
// ЗАВДАННЯ 1: Гра "Guess a Number"
// ------------------------------------------
function playGuessNumber() {
    echo "\n=== Гра 'Guess a Number' (1 - 100) ===\n";
    echo "Введіть 'exit' у будь-який момент для повернення в меню.\n";
    
    $secret = rand(1, 100);
    $maxAttempts = 7;
    
    // Анонімна функція для порівняння
    $compare = function($userNum, $secretNum) {
        if ($userNum > $secretNum) return "Спробуй менше.";
        if ($userNum < $secretNum) return "Спробуй більше.";
        return "Вгадано!";
    };

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $input = trim(readline("Спроба {$attempt}/{$maxAttempts}: "));
        
        if (strtolower($input) === 'exit') {
            echo "Вихід із гри...\n";
            return;
        }
        
        if (!validateNumber($input)) {
            echo "Помилка: введіть ціле число!\n";
            $attempt--;
            continue;
        }

        $num = (int)$input;
        $result = $compare($num, $secret);
        echo $result . "\n";

        if ($num === $secret) {
            echo " Вітаю! Ви вгадали загадане число за {$attempt} спроб(и).\n";
            return;
        }
    }
    echo " Ви програли! Вичерпано 7 спроб. Загадане число було: {$secret}\n";
}

// ------------------------------------------
// ЗАВДАННЯ 2: Гра "Камінь, Ножиці, Папір"
// ------------------------------------------
function playRPS() {
    echo "\n=== Гра 'Камінь, Ножиці, Папір' (3 раунди) ===\n";
    echo "Введіть 'exit' для повернення в меню.\n";
    
    $choices = ['камінь', 'ножиці', 'папір'];
    $userScore = 0;
    $compScore = 0;

    // Анонімна функція для генерації вибору комп'ютера
    $getCompChoice = function() use ($choices) {
        return $choices[array_rand($choices)];
    };

    for ($round = 1; $round <= 3; $round++) {
        echo "\nРаунд {$round}. Обери варіант:\n";
        echo "[0] камінь\n[1] ножиці\n[2] папір\n";
        $input = trim(readline("-> "));

        if (strtolower($input) === 'exit') {
            echo "Вихід із гри...\n";
            return;
        }

        if (!in_array($input, ['0', '1', '2']) && !in_array(strtolower($input), $choices)) {
            echo "Помилка: невірний вибір. Спробуйте знову.\n";
            $round--;
            continue;
        }

        $userChoice = is_numeric($input) ? $choices[(int)$input] : strtolower($input);
        $compChoice = $getCompChoice();
        
        echo "Комп'ютер вибрав: {$compChoice}\n";
        echo "Ви вибрали: {$userChoice}\n";

        if ($userChoice === $compChoice) {
            echo "Нічия в цьому раунді!\n";
        } elseif (
            ($userChoice === 'камінь' && $compChoice === 'ножиці') ||
            ($userChoice === 'ножиці' && $compChoice === 'папір') ||
            ($userChoice === 'папір' && $compChoice === 'камінь')
        ) {
            echo " Ви виграли цей раунд!\n";
            $userScore++;
        } else {
            echo " Комп'ютер виграв цей раунд!\n";
            $compScore++;
        }
    }

    echo "\n--- ПІДСУМОК ГРИ ---\n";
    echo "Рахунок — Ви: {$userScore} | Комп'ютер: {$compScore}\n";
    if ($userScore > $compScore) {
        echo " Вітаю, ви переможець матчу!\n";
    } elseif ($userScore < $compScore) {
        echo " Переміг комп'ютер!\n";
    } else {
        echo " Нічия за підсумками трьох раундів!\n";
    }
}

// ------------------------------------------
// ЗАВДАННЯ 3: Консольний Калькулятор
// ------------------------------------------
function playCalculator() {
    echo "\n=== Консольний Калькулятор ===\n";
    echo "Формат вводу: число1 оператор число2 (напр. 5+3 або 10**2).\n";
    echo "Підтримувані оператори: +, -, *, /, **, %\n";
    echo "Введіть 'exit' для повернення в головне меню.\n";

    // Анонімні функції для операцій
    $add = fn($a, $b) => $a + $b;
    $sub = fn($a, $b) => $a - $b;
    $mul = fn($a, $b) => $a * $b;
    $div = fn($a, $b) => $b == 0 ? "Помилка: ділення на нуль" : $a / $b;
    $pow = fn($a, $b) => $a ** $b;
    $mod = fn($a, $b) => $b == 0 ? "Помилка: ділення на нуль" : $a % $b;

    while (true) {
        $expr = trim(readline("\nВведіть вираз: "));
        if (strtolower($expr) === 'exit') break;

        // Регулярний вираз для парсингу виразу (підтримує цілі та дробові числа)
        if (preg_match('/^\s*([+-]?\d*\.?\d+)\s*([\+\-\*\/\%\*]{1,2})\s*([+-]?\d*\.?\d+)\s*$/', $expr, $matches)) {
            $num1 = (float)$matches[1];
            $op = $matches[2];
            $num2 = (float)$matches[3];

            // Використання Match Expression (PHP 8.0+)
            $result = match ($op) {
                '+' => $add($num1, $num2),
                '-' => $sub($num1, $num2),
                '*' => $mul($num1, $num2),
                '/' => $div($num1, $num2),
                '**' => $pow($num1, $num2),
                '%' => $mod($num1, $num2),
                default => "Помилка: невідомий оператор"
            };

            echo "Результат: {$result}\n";
        } else {
            echo "Помилка: невірний формат. Введіть у форматі 'число оп число' (напр. 10*5).\n";
        }
    }
}

// ------------------------------------------
// ЗАВДАННЯ 4: Гра "Roll the Dice"
// ------------------------------------------
function rollDice() {
    return rand(1, 6);
}

function playRollDice() {
    echo "\n=== Гра 'Roll the Dice' ===\n";
    echo "Правила: кидайте кубик, мета — набрати рівно 20 очок. Перевищення 20 — поразка.\n";
    echo "Випадання '6' дає суперкидок!\n";
    echo "Введіть 'exit' для повернення в меню.\n";

    $score = 0;
    echo "Початковий рахунок: {$score}\n";

    while ($score < 20) {
        $input = readline("Натисніть Enter, щоб зробити кидок (або 'exit' для виходу): ");
        if (strtolower($input) === 'exit') return;

        $dice = rollDice();
        if ($dice === 6) {
            echo "Кидок: 6.  Суперкидок!\n";
            $input = readline("Натисніть Enter, щоб зробити суперкидок... ");
            if (strtolower($input) === 'exit') return;
            
            $superDice = rollDice();
            echo "Суперкидок результат: {$superDice}.\n";
            $score += $superDice;
        } else {
            echo "Кидок: {$dice}.\n";
            $score += $dice;
        }
        echo "Загальний рахунок: {$score}\n";
    }

    if ($score === 20) {
        echo " Вітаю! Ви набрали рівно 20 очок і виграли!\n";
    } else {
        echo " Ви набрали {$score} очок (перевищили 20). Поразка!\n";
    }
}

// ------------------------------------------
// ГОЛОВНЕ МЕНЮ ПРОГРАМИ
// ------------------------------------------
function main() {
    while (true) {
        echo "\n========================================\n";
        echo "   ГОЛОВНЕ МЕНЮ — ЛАБОРАТОРНА РОБОТА №6\n";
        echo "========================================\n";
        echo "[1] Гра 'Guess a Number' (Вгадай число)\n";
        echo "[2] Гра 'Камінь, Ножиці, Папір'\n";
        echo "[3] Консольний Калькулятор\n";
        echo "[4] Гра 'Roll the Dice' (Кидання кубика)\n";
        echo "[exit] Вихід із програми\n";
        echo "----------------------------------------\n";

        $choice = trim(readline("Оберіть номер програми (1-4): "));

        if (strtolower($choice) === 'exit') {
            echo "До побачення!\n";
            break;
        }

        switch ($choice) {
            case '1':
                playGuessNumber();
                break;
            case '2':
                playRPS();
                break;
            case '3':
                playCalculator();
                break;
            case '4':
                playRollDice();
                break;
            default:
                echo "Невірний вибір. Будь ласка, введіть число від 1 до 4 або 'exit'.\n";
        }
    }
}

// Запуск головного меню
main();
?>
