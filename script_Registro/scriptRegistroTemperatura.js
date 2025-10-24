document.addEventListener('DOMContentLoaded', () => {
    const temperaturaForm = document.getElementById('temperaturaForm');

    temperaturaForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const valorTemperatura = document.getElementById('valorTemperatura').value;
        const data = document.getElementById('data').value;
        const hora = document.getElementById('hora').value;
        const observacoes = document.getElementById('observacoes').value;

        if(valorTemperatura && data && hora) {
            const registroTemperatura = {
                valorTemperatura,
                data,
                hora,
                observacoes
            };

            // Recupera registros existentes
            let registros = JSON.parse(localStorage.getItem('temperatura')) || [];
            registros.push(registroTemperatura);

            // Salva no localStorage
            localStorage.setItem('temperatura', JSON.stringify(registros));

            // Limpa formulário
            temperaturaForm.reset();

            alert('Registro de temperatura salvo com sucesso!');
        } else {
            alert('Preencha todos os campos obrigatórios!');
        }
    });
});
