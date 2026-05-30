document.addEventListener('DOMContentLoaded', () => {

    const glicemiaForm = document.getElementById('glicemiaForm');

    glicemiaForm.addEventListener('submit', function(e) {

        const valorGlicemia = document.getElementById('valorGlicemia').value;
        const tipoMedicao = document.getElementById('tipoMedicao').value;
        const data = document.getElementById('data').value;
        const hora = document.getElementById('hora').value;

        if(!valorGlicemia || !tipoMedicao || !data || !hora) {

            e.preventDefault();

            alert('Preencha todos os campos obrigatórios!');
        }

    });

});