const calendarDays = document.getElementById('calendarDays');
const monthYear = document.getElementById('monthYear');
const dayRecords = document.getElementById('dayRecords');

let currentDate = new Date();

const registros = {
    // exemplo de registros no formato YYYY-MM-DD
    '2025-10-29': { pressao: '120/80', temperatura: '36.5°C', batimentos: '72 bpm', glicemia: '75 mg/dl (jejum)' },
    '2025-10-30': { pressao: '130/85', temperatura: '37°C', batimentos: '80 bpm' , glicemia: '75 mg/dl (jejum)' }
};

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    monthYear.textContent = `${currentDate.toLocaleString('pt-BR', { month: 'long' })} ${year}`;

    calendarDays.innerHTML = '';

    const firstDay = new Date(year, month, 1).getDay(); // dia da semana
    const lastDate = new Date(year, month + 1, 0).getDate(); // último dia do mês

    // espaços em branco do início
    for (let i = 0; i < firstDay; i++) {
        const emptyDiv = document.createElement('div');
        calendarDays.appendChild(emptyDiv);
    }

    for (let d = 1; d <= lastDate; d++) {
        const dayDiv = document.createElement('div');
        dayDiv.textContent = d;

        const fullDate = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        if (registros[fullDate]) dayDiv.classList.add('has-record');
        if (d === new Date().getDate() && month === new Date().getMonth() && year === new Date().getFullYear()) {
            dayDiv.classList.add('today');
        }

        dayDiv.addEventListener('click', () => {
            const rec = registros[fullDate];
            if (rec) {
                dayRecords.innerHTML = `
                    <p>Pressão: ${rec.pressao}</p>
                    <p>Temperatura: ${rec.temperatura}</p>
                    <p>Batimentos: ${rec.batimentos}</p>
                    <p>Glicemia: ${rec.glicemia}</p>
                `;
            } else {
                dayRecords.innerHTML = '<p>Não há registros neste dia.</p>';
            }
        });

        calendarDays.appendChild(dayDiv);
    }
}

// navegação do calendário
document.getElementById('prevMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
});
document.getElementById('nextMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
});

renderCalendar();
