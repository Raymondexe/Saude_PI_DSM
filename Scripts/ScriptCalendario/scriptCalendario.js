const diasContainer = document.getElementById("dias");
const mesAno = document.getElementById("mesAno");

let data = new Date();
let dataSelecionada = null;

let exames = JSON.parse(localStorage.getItem("exames")) || {};

function gerarCalendario() {

    diasContainer.innerHTML = "";

    const ano = data.getFullYear();
    const mes = data.getMonth();

    const primeiroDia = new Date(ano, mes, 1).getDay();
    const primeiroDiaCorrigido = primeiroDia === 0 ? 0 : primeiroDia;
    const ultimoDia = new Date(ano, mes + 1, 0).getDate();

    mesAno.innerText = data.toLocaleDateString("pt-BR", {
        month: "long",
        year: "numeric"
    });

    // espaços vazios
    for (let i = 0; i < primeiroDiaCorrigido; i++){
    const vazio = document.createElement("div");
    vazio.classList.add("vazio");
    diasContainer.appendChild(vazio);
}

    // dias do mês
    for (let dia = 1; dia <= ultimoDia; dia++) {

        const divDia = document.createElement("div");
        divDia.classList.add("dia");

        const diaFormatado = String(dia).padStart(2, "0");
        const mesFormatado = String(mes + 1).padStart(2, "0");

        const chave = `${ano}-${mesFormatado}-${diaFormatado}`;

        divDia.innerHTML = `<span class="numeroDia">${dia}</span>`;

        // MOSTRAR EXAMES DO DIA
        if (exames[chave]) {

            exames[chave].forEach(exame => {

                let resumo = exame.tipo || "Consulta";

                if (resumo.length > 10) {
                    resumo = resumo.substring(0, 10) + "...";
                }

                const notif = document.createElement("div");
                notif.classList.add("exameResumo");
                notif.innerText = resumo;

                divDia.appendChild(notif);

            });

        }

        divDia.addEventListener("click", () => {

            const hoje = new Date();
            hoje.setHours(0, 0, 0, 0);

            const dataClicada = new Date(ano, mes, dia);

            if (dataClicada < hoje) {
                alert("Não é possível marcar exames em dias passados.");
                return;
            }

            abrirModal(dia, mes, ano);
        });


        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        const dataDia = new Date(ano, mes, dia);

        if (dataDia < hoje) {
            divDia.classList.add("dia-passado");
        }
        diasContainer.appendChild(divDia);
    }
}

function abrirModal(dia, mes, ano) {

    const diaFormatado = String(dia).padStart(2, "0");
    const mesFormatado = String(mes + 1).padStart(2, "0");

    dataSelecionada = `${ano}-${mesFormatado}-${diaFormatado}`;

    const modal = document.getElementById("modalExame");

    if (!modal) {
        console.error("Modal não encontrado");
        return;
    }

    modal.style.display = "flex";

    document.getElementById("dataSelecionada").innerText =
        `${diaFormatado}/${mesFormatado}/${ano}`;

    // limpa campos sempre que abrir
    document.getElementById("local").value = "";
    document.getElementById("tipo").value = "";
    document.getElementById("medico").value = "";
    document.getElementById("horario").value = "";
    document.getElementById("levar").value = "";
}

function fecharModal() {

    const modal = document.getElementById("modalExame");

    if (modal) {
        modal.style.display = "none";
    }
}

function salvarExame() {

    if (!dataSelecionada) return;

    const local = document.getElementById("local").value;
    const tipo = document.getElementById("tipo").value;
    const medico = document.getElementById("medico").value;
    const horario = document.getElementById("horario").value;
    const levar = document.getElementById("levar").value;

    // se ainda não existir lista nesse dia
    if (!exames[dataSelecionada]) {
        exames[dataSelecionada] = [];
    }

    exames[dataSelecionada].push({
        local,
        tipo,
        medico,
        horario,
        levar
    });

    localStorage.setItem("exames", JSON.stringify(exames));

    fecharModal();
    gerarCalendario();
}

// fechar clicando fora
window.onclick = function (event) {

    const modal = document.getElementById("modalExame");

    if (event.target === modal) {
        fecharModal();
    }

};

document.getElementById("prev").addEventListener("click", () => {
    data.setMonth(data.getMonth() - 1);
    gerarCalendario();
});

document.getElementById("next").addEventListener("click", () => {
    data.setMonth(data.getMonth() + 1);
    gerarCalendario();
});

gerarCalendario();