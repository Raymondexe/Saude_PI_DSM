document.addEventListener('DOMContentLoaded', () => {

    const calendarDays = document.getElementById('calendarDays');
    const monthYear = document.getElementById('monthYear');
    const dayRecords = document.getElementById('dayRecords');

    let currentDate = new Date();

    function renderCalendar() {

        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        monthYear.textContent =
            `${currentDate.toLocaleString('pt-BR', { month: 'long' })} ${year}`;

        calendarDays.innerHTML = '';

        const firstDay = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {

            const emptyDiv = document.createElement('div');
            calendarDays.appendChild(emptyDiv);

        }

        for (let d = 1; d <= lastDate; d++) {

            const dayDiv = document.createElement('div');

            dayDiv.textContent = d;

            const fullDate =
                `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

            if (registros[fullDate]) {
                dayDiv.classList.add('has-record');
            }

            const hoje = new Date();

            if (
                d === hoje.getDate() &&
                month === hoje.getMonth() &&
                year === hoje.getFullYear()
            ) {
                dayDiv.classList.add('today');
            }

            dayDiv.addEventListener('click', () => {

                const rec = registros[fullDate];

                if (rec) {

                    dayRecords.innerHTML = `
                        <h3>Registros do dia</h3>

                        <p>
                            <strong>Batimentos:</strong>
                            ${rec.batimentos ?? '--'}
                        </p>

                        <p>
                            <strong>Temperatura:</strong>
                            ${rec.temperatura ?? '--'}
                        </p>

                        <p>
                            <strong>Glicemia:</strong>
                            ${rec.glicemia ?? '--'}
                        </p>

                        <p>
                            <strong>Pressão:</strong>
                            ${rec.pressao ?? '--'}
                        </p>
                    `;

                } else {

                    dayRecords.innerHTML =
                        '<p>Não há registros neste dia.</p>';

                }

            });

            calendarDays.appendChild(dayDiv);

        }

    }

    document.getElementById('prevMonth')
        .addEventListener('click', () => {

            currentDate.setMonth(currentDate.getMonth() - 1);

            renderCalendar();

        });

    document.getElementById('nextMonth')
        .addEventListener('click', () => {

            currentDate.setMonth(currentDate.getMonth() + 1);

            renderCalendar();

        });

    renderCalendar();

});