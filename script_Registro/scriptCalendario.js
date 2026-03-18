const calendarDays = document.getElementById('calendarDays');
const monthYear = document.getElementById('monthYear');
const dayRecords = document.getElementById('dayRecords');

let currentDate = new Date();

const registros = {
    // exemplo de registros no formato YYYY-MM-DD
    '2025-10-29': { pressao: '120/80', temperatura: '36.5°C', batimentos: '72 bpm', glicemia: '75 mg/dl (jejum)' },
    '2025-10-30': { pressao: '130/85', temperatura: '37°C', batimentos: '80 bpm' , glicemia: '75 mg/dl (jejum)' },
    '2025-11-01': { pressao: '118/78', temperatura: '36.6°C', batimentos: '74 bpm', glicemia: '82 mg/dl (jejum)' },
    '2025-11-02': { pressao: '125/80', temperatura: '36.8°C', batimentos: '79 bpm', glicemia: '88 mg/dl (jejum)' },
    '2025-11-03': { pressao: '130/85', temperatura: '37.0°C', batimentos: '83 bpm', glicemia: '92 mg/dl (jejum)' },
    '2025-11-04': { pressao: '122/76', temperatura: '36.4°C', batimentos: '70 bpm', glicemia: '80 mg/dl (jejum)' },
    '2025-11-05': { pressao: '128/82', temperatura: '36.9°C', batimentos: '78 bpm', glicemia: '87 mg/dl (jejum)' },

    '2025-11-06': { pressao: '119/75', temperatura: '36.6°C', batimentos: '72 bpm', glicemia: '79 mg/dl (jejum)' },
    '2025-11-07': { pressao: '132/86', temperatura: '37.1°C', batimentos: '88 bpm', glicemia: '95 mg/dl (jejum)' },
    '2025-11-08': { pressao: '115/72', temperatura: '36.5°C', batimentos: '69 bpm', glicemia: '78 mg/dl (jejum)' },
    '2025-11-09': { pressao: '138/90', temperatura: '37.2°C', batimentos: '92 bpm', glicemia: '97 mg/dl (jejum)' },
    '2025-11-10': { pressao: '124/79', temperatura: '36.7°C', batimentos: '75 bpm', glicemia: '83 mg/dl (jejum)' },

    '2025-11-11': { pressao: '129/83', temperatura: '36.9°C', batimentos: '80 bpm', glicemia: '90 mg/dl (jejum)' },
    '2025-11-12': { pressao: '117/74', temperatura: '36.4°C', batimentos: '67 bpm', glicemia: '76 mg/dl (jejum)' },
    '2025-11-13': { pressao: '133/87', temperatura: '37.3°C', batimentos: '94 bpm', glicemia: '98 mg/dl (jejum)' },
    '2025-11-14': { pressao: '121/77', temperatura: '36.6°C', batimentos: '71 bpm', glicemia: '82 mg/dl (jejum)' },
    '2025-11-15': { pressao: '126/81', temperatura: '37.0°C', batimentos: '84 bpm', glicemia: '91 mg/dl (jejum)' },

    '2025-11-16': { pressao: '120/76', temperatura: '36.5°C', batimentos: '73 bpm', glicemia: '79 mg/dl (jejum)' },
    '2025-11-17': { pressao: '134/88', temperatura: '37.2°C', batimentos: '89 bpm', glicemia: '96 mg/dl (jejum)' },
    '2025-11-18': { pressao: '118/75', temperatura: '36.4°C', batimentos: '68 bpm', glicemia: '77 mg/dl (jejum)' },
    '2025-11-19': { pressao: '127/82', temperatura: '36.8°C', batimentos: '79 bpm', glicemia: '85 mg/dl (jejum)' },
    '2025-11-20': { pressao: '123/78', temperatura: '36.7°C', batimentos: '74 bpm', glicemia: '81 mg/dl (jejum)' },

    '2025-11-21': { pressao: '131/84', temperatura: '37.1°C', batimentos: '86 bpm', glicemia: '94 mg/dl (jejum)' },
    '2025-11-22': { pressao: '119/73', temperatura: '36.5°C', batimentos: '70 bpm', glicemia: '79 mg/dl (jejum)' },
    '2025-11-22': { pressao: '120/80', temperatura: '36.7°C', batimentos: '72 bpm', glicemia: '95 mg/dl (jejum)' },
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
