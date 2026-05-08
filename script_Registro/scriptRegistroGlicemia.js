document.addEventListener('DOMContentLoaded', () => {
    const glicemiaForm = document.getElementById('glicemiaForm');

    glicemiaForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Captura os valores do formulário
        const valorGlicemia = document.getElementById('valorGlicemia').value;
        const tipoMedicao = document.getElementById('tipoMedicao').value;
        const data = document.getElementById('data').value;
        const hora = document.getElementById('hora').value;
        const observacoes = document.getElementById('observacoes').value;

        if(valorGlicemia && tipoMedicao && data && hora) {
            const registroGlicemia = {
                valorGlicemia,
                tipoMedicao,
                data,
                hora,
                observacoes
            };

            // Recupera registros existentes
            let registros = JSON.parse(localStorage.getItem('glicemia')) || [];
            registros.push(registroGlicemia);

            // Salva no localStorage
            localStorage.setItem('glicemia', JSON.stringify(registros));

            // Limpa formulário
            glicemiaForm.reset();

            alert('Registro de glicemia salvo com sucesso!');
        } else {
            alert('Preencha todos os campos obrigatórios!');
        }
    });
});
