document.addEventListener('DOMContentLoaded', () => {
    const pressaoForm = document.getElementById('pressaoForm');

    pressaoForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Evita que a página recarregue

        const sistolica = document.getElementById('sistolica').value;
        const diastolica = document.getElementById('diastolica').value;
        const data = document.getElementById('data').value;
        const hora = document.getElementById('hora').value;
        const observacoes = document.getElementById('observacoes').value;

        if(sistolica && diastolica) {
            // Criando objeto para salvar
            const pressaoRegistro = {
                sistolica,
                diastolica,
                data,
                hora,
                observacoes
            };

            // Recupera registros anteriores
            let registros = JSON.parse(localStorage.getItem('pressao')) || [];
            registros.push(pressaoRegistro);

            // Salva no LocalStorage
            localStorage.setItem('pressao', JSON.stringify(registros));

            // Limpa formulário
            pressaoForm.reset();

            alert('Registro de pressão salvo com sucesso!');
        } else {
            alert('Preencha todos os campos obrigatórios!');
        }
    });
});
