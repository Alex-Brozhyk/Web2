document.addEventListener("DOMContentLoaded", () => {
    // ==========================================
    // ПОПЕРЕДНІЙ ФУНКЦІОНАЛ (Калькулятор та меню)
    // ==========================================
    const num1Input = document.getElementById("num1");
    const num2Input = document.getElementById("num2");
    const saveHistorySelect = document.getElementById("save-history");
    const resultDisplay = document.getElementById("calc-result");
    const operationButtons = document.querySelectorAll(".op-btn");
    
    const historyList = document.getElementById("history-list");
    const clearHistoryBtn = document.getElementById("clear-history");
    const emptyHistoryMsg = document.getElementById("empty-history-msg");

    function calculate(a, b, operator) {
        switch (operator) {
            case "+": return a + b;
            case "-": return a - b;
            case "*": return a * b;
            case "/":
                if (b === 0) return "Помилка: ділення на 0";
                return Number((a / b).toFixed(4));
            default: return null;
        }
    }

    function addHistoryRecord(expression) {
        if (emptyHistoryMsg) {
            emptyHistoryMsg.style.display = "none";
        }

        const li = document.createElement("li");
        li.className = "history-item p-3 bg-gray-100 rounded border-l-4 border-green-500 text-sm flex justify-between items-center";
        
        const textSpan = document.createElement("span");
        textSpan.innerText = expression;

        const timeSpan = document.createElement("span");
        const now = new Date();
        timeSpan.className = "text-xs text-gray-600";
        timeSpan.innerText = now.toLocaleTimeString();

        li.appendChild(textSpan);
        li.appendChild(timeSpan);
        historyList.appendChild(li);
    }

    operationButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const val1 = num1Input.value.trim();
            const val2 = num2Input.value.trim();

            if (val1 === "" || val2 === "") {
                alert("Будь ласка, заповніть обидва числові поля!");
                return;
            }

            const a = parseFloat(val1);
            const b = parseFloat(val2);
            const operator = button.getAttribute("data-op");

            const result = calculate(a, b, operator);
            const expressionText = `${a} ${operator} ${b} = ${result}`;

            resultDisplay.innerText = result;

            if (saveHistorySelect.value === "yes") {
                addHistoryRecord(expressionText);
            }

            setTimeout(() => {
                num1Input.value = "";
                num2Input.value = "";
                resultDisplay.innerText = "—";
            }, 1500);
        });
    });

    if (clearHistoryBtn) {
        clearHistoryBtn.addEventListener("click", () => {
            historyList.innerHTML = "";
            if (emptyHistoryMsg) {
                emptyHistoryMsg.style.display = "block";
            }
        });
    }

    const navToggles = document.querySelectorAll(".nav-toggle");
    navToggles.forEach((toggle) => {
        toggle.addEventListener("click", () => {
            const parentItem = toggle.closest(".nav-item");
            const content = parentItem.querySelector(".nav-content");
            const arrow = toggle.querySelector(".arrow");

            content.classList.toggle("hidden");
            if (content.classList.contains("hidden")) {
                arrow.style.transform = "rotate(0deg)";
            } else {
                arrow.style.transform = "rotate(180deg)";
            }
        });
    });


    // ==========================================
    // НОВИЙ КОД: ЛАБОРАТОРНА РОБОТА №4 (ВАРІАНТ 2)
    // ==========================================
    console.log("=== ПОЧАТОК ВИКОНАННЯ ЛАБОРАТОРНОЇ №4 (ВАРІАНТ 2) ===");

    // 1. IIFE (Негайно викликана функція)
    (function() {
        console.log("1. IIFE вивід window.location.href:", window.location.href);
    })();

    // 2. Методи масивів: map, filter, reduce
    const numbers = [5, 12, 18, 25, 30, 42, 50, 14, 8, 33]; // 10 елементів
    const doubled = numbers.map(num => num * 2);
    const filtered = doubled.filter(num => num > 25);
    const sum = filtered.reduce((total, num) => total + num, 0);
    
    console.log("2. Початковий масив:", numbers);
    console.log("2. Подвоєні значення (.map):", doubled);
    console.log("2. Значення більше 25 (.filter):", filtered);
    console.log("2. Сума елементів (.reduce):", sum);

    // 3. Оператор Spread у функції
    function multiply(a, b) {
        return a * b;
    }
    const pair = [6, 7];
    const multiplyResult = multiply(...pair);
    console.log(`3. Spread Operator: multiply(${pair[0]}, ${pair[1]}) = ${multiplyResult}`);

    // 4. Робота з Set (перевірка унікальності)
    const mySet = new Set([10, 20, 20, 30, 40, 40, 50, "тест", "тест"]);
    console.log("4. Об'єкт Set (дублікати видалено автоматично):");
    for (const value of mySet) {
        console.log("   - Елемент Set:", value);
    }

    // 5. Використання методу bind для прив'язки контексту
    const user = {
        name: "Олександр",
        getName: function() {
            console.log(`5. Метод bind викликано. Ім'я: ${this.name}`);
        }
    };
    const admin = { name: "Адмін Сергій" };
    const boundGetName = user.getName.bind(admin);
    boundGetName(); // Має вивести ім'я адміна

    // 6. Створення функції із замиканням (Advanced Counter)
    function createAdvancedCounter() {
        let count = 0; // приватна змінна стану
        return {
            increment: function() {
                count++;
                return count;
            },
            decrement: function() {
                count--;
                return count;
            }
        };
    }
    const counter = createAdvancedCounter();
    console.log("6. Замикання (Advanced Counter):");
    console.log("   Після increment():", counter.increment()); // 1
    console.log("   Після increment():", counter.increment()); // 2
    console.log("   Після decrement():", counter.decrement()); // 1
    
    console.log("=== КІНЕЦЬ ВИКОНАННЯ ЛАБОРАТОРНОЇ №4 ===");
});
