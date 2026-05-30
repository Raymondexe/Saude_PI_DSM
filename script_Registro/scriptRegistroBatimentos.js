const inputBatimentos = document.getElementById('valorBatimentos');
const botaoSalvar = document.getElementById('salvarBatimentos');

botaoSalvar.addEventListener('click', (e) => {

    const bpm = inputBatimentos.value;

    if (!bpm) {

        e.preventDefault();

        alert('Digite um valor válido!');
    }

});