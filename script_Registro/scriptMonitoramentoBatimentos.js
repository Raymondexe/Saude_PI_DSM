const batimentosDisplay = document.getElementById('batimentos');

function carregarBPM() {
    const bpmSalvo = localStorage.getItem('batimentos');
    if (bpmSalvo) {
        batimentosDisplay.textContent = `${bpmSalvo} bpm`;
    }
}

// Carrega ao abrir a página
carregarBPM();


document.addEventListener('DOMContentLoaded', () => {
    const pressaoDisplay = document.getElementById('pressao');

    // Pega o último registro de pressão
    const registros = JSON.parse(localStorage.getItem('pressao')) || [];
    if (registros.length > 0) {
        const ultimo = registros[registros.length - 1];
        pressaoDisplay.textContent = `${ultimo.sistolica}/${ultimo.diastolica} mmHg`;
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const glicemiaDisplay = document.getElementById('glicemia');

    const registros = JSON.parse(localStorage.getItem('glicemia')) || [];
    if(registros.length > 0) {
        const ultimo = registros[registros.length - 1];
        glicemiaDisplay.textContent = `${ultimo.valorGlicemia} mg/dL (${ultimo.tipoMedicao})`;
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const temperaturaDisplay = document.getElementById('temperatura');

    const registros = JSON.parse(localStorage.getItem('temperatura')) || [];
    if(registros.length > 0) {
        const ultimo = registros[registros.length - 1];
        temperaturaDisplay.textContent = `${ultimo.valorTemperatura} °C`;
    }
});

