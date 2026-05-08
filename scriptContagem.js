document.addEventListener("DOMContentLoaded", () => {
    const timerDisplay = document.getElementById("timerDisplay");
    const startButton = document.getElementById("startTimer");
    const resetButton = document.getElementById("resetTimer");
    let timer;
    let timeLeft = 60;

    function updateDisplay() {
        timerDisplay.textContent = `${timeLeft}s`;
    }

    startButton.addEventListener("click", () => {
        clearInterval(timer); // Garante que não haja timers duplicados
        timer = setInterval(() => {
            timeLeft--;
            updateDisplay();
            if (timeLeft <= 0) clearInterval(timer);
        }, 1000);
    });

    resetButton.addEventListener("click", () => {
        clearInterval(timer); // Para contagem atual
        timeLeft = 60;        // Reseta para 60 segundos
        updateDisplay();      // Atualiza o display
    });

    updateDisplay(); // Inicializa o display em 60s
});
