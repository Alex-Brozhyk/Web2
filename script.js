document.addEventListener("DOMContentLoaded", () => {
    // 1. Отримання елементів DOM для калькулятора та історії
    const num1Input = document.getElementById("num1");
    const num2Input = document.getElementById("num2");
    const saveHistorySelect = document.getElementById("save-history");
    const resultDisplay = document.getElementById("calc-result");
    const operationButtons = document.querySelectorAll(".op-btn");
    
    const historyList = document.getElementById("history-list");
    const clearHistoryBtn = document.getElementById("clear-history");
    const emptyHistoryMsg = document.getElementById("empty-history-msg");

    // Функція для обчислення результату
    function calculate(a, b, operator) {
        switch (operator) {
            case "+":
                return a + b;
            case "-":
                return a - b;
            case "*":
                return a * b;
            case "/":
                if (b === 0) {
                    return "Помилка: ділення на 0";
                }
                return Number((a / b).toFixed(4));
            default:
                return null;
        }
    }

    // Функція для додавання запису в історію
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

    // 2. Обробка натискання кнопок операцій ("+", "-", "*", "/")
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

            // Відображаємо результат на формі
            resultDisplay.innerText = result;

            // Перевіряємо випадаючий список "Зберегти до історії"
            if (saveHistorySelect.value === "yes") {
                addHistoryRecord(expressionText);
            }

            // Згідно з вимогою: введені значення в текстових полях та результат 
            // повинні видалятися автоматично після відправки (з невеликою затримкою, щоб користувач встиг побачити результат)
            setTimeout(() => {
                num1Input.value = "";
                num2Input.value = "";
                resultDisplay.innerText = "—";
            }, 1500);
        });
    });

    // 3. Очищення історії операцій
    clearHistoryBtn.addEventListener("click", () => {
        historyList.innerHTML = "";
        if (emptyHistoryMsg) {
            emptyHistoryMsg.style.display = "block";
        }
    });

    // 4. Реалізація згортання/розгортання випадаючих меню у боковій панелі
    const navToggles = document.querySelectorAll(".nav-toggle");

    navToggles.forEach((toggle) => {
        toggle.addEventListener("click", () => {
            const parentItem = toggle.closest(".nav-item");
            const content = parentItem.querySelector(".nav-content");
            const arrow = toggle.querySelector(".arrow");

            // Перемикаємо видимість вмісту
            content.classList.toggle("hidden");

            // Обертаємо стрілку
            if (content.classList.contains("hidden")) {
                arrow.style.transform = "rotate(0deg)";
            } else {
                arrow.style.transform = "rotate(180deg)";
            }
        });
    });
});
