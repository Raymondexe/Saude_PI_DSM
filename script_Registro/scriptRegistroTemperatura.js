document.addEventListener('DOMContentLoaded', () => {

    const temperaturaForm = document.getElementById('temperaturaForm');

    temperaturaForm.addEventListener('submit', function(e) {

        const valorTemperatura = document.getElementById('valorTemperatura').value;
        const data = document.getElementById('data').value;
        const hora = document.getElementById('hora').value;

        if(!valorTemperatura || !data || !hora) {

            e.preventDefault();

            alert('Preencha todos os campos obrigatórios!');
        }

    });

});