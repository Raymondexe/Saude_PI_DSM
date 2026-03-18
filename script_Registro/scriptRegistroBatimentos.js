const inputBatimentos = document.getElementById('valorBatimentos');
const botaoSalvar = document.getElementById('salvarBatimentos');

botaoSalvar.addEventListener('click', () => {
    const bpm = inputBatimentos.value;
    if (!bpm) {
        alert('Digite um valor válido!');
        return;
    }

    // Salva no localStorage
    localStorage.setItem('batimentos', bpm);

    alert(`BPM ${bpm} salvo com sucesso!`);
    inputBatimentos.value = '';
});



