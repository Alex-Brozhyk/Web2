document.addEventListener("DOMContentLoaded", () => {
    // Розгортання/згортання підменю у боковій панелі
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

    // Додаткова клієнтська валідація форми перед відправкою POST (п. 6 додаткових вимог)
    const calcForm = document.getElementById("calc-form");
    if (calcForm) {
        calcForm.addEventListener("submit", (e) => {
            const num1 = document.getElementById("num1").value.trim();
            const num2 = document.getElementById("num2").value.trim();
            const operator = document.getElementById("operator").value;

            if (num1 === "" || num2 === "") {
                e.preventDefault();
                alert("Будь ласка, заповніть обидва числові поля!");
                return;
            }

            if (operator === "/" && parseFloat(num2) === 0) {
                e.preventDefault();
                alert("Ділення на нуль заборонено!");
            }
        });
    }
});
