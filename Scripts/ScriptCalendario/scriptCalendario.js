let editandoIndex = null;
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

    
    const primeiroDiaCorrigido = (primeiroDia + 6) % 7;
    const ultimoDia = new Date(ano, mes + 1, 0).getDate();

    mesAno.innerText = data.toLocaleDateString("pt-BR", {
        month: "long",
        year: "numeric"
    });


    for (let i = 0; i < primeiroDiaCorrigido; i++) {
        const vazio = document.createElement("div");
        vazio.classList.add("vazio");
        diasContainer.appendChild(vazio);
    }


    for (let dia = 1; dia <= ultimoDia; dia++) {

        const divDia = document.createElement("div");
        divDia.classList.add("dia");

        const diaFormatado = String(dia).padStart(2, "0");
        const mesFormatado = String(mes + 1).padStart(2, "0");

        const chave = `${ano}-${mesFormatado}-${diaFormatado}`;

        divDia.innerHTML = `<span class="numeroDia">${dia}</span>`;


        if (exames[chave]) {

            const lista = exames[chave];

            lista.slice(0, 2).forEach(exame => {

                let resumo = exame.tipo || "Consulta";

                if (resumo.length > 10) {
                    resumo = resumo.substring(0, 10) + "...";
                }

                const notif = document.createElement("div");
                notif.classList.add("exameResumo");
                notif.innerText = resumo;

                divDia.appendChild(notif);

                const tipo = exame.tipo;

                switch (tipo) {
                    case "Consulta":
                        notif.classList.add("consulta");
                        break;

                    case "Exame":
                        notif.classList.add("exame");
                        break;

                    case "Retorno":
                        notif.classList.add("retorno");
                        break;

                    default:
                        notif.style.background = "#999"; // fallback
                }
            });

            // SE TIVER MAIS DE 2
            if (lista.length > 2) {
                const mais = document.createElement("div");
                mais.classList.add("ver-mais");
                mais.innerText = `+${lista.length - 2} Ver mais`;

                divDia.appendChild(mais);
            }
        }
        divDia.addEventListener("click", () => {
            abrirListaEventos(dia, mes, ano);
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


function abrirListaEventos(dia, mes, ano) {

    const diaFormatado = String(dia).padStart(2, "0");
    const mesFormatado = String(mes + 1).padStart(2, "0");

    const chave = `${ano}-${mesFormatado}-${diaFormatado}`;

    const modal = document.getElementById("modalLista");
    const listaDiv = document.getElementById("listaEventos");
    const titulo = document.getElementById("tituloLista");
    const btnNovo = document.getElementById("btnNovo");

    // 🔥 limpa lista
    listaDiv.innerHTML = "";

    // 🔥 data atual
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    const dataClicada = new Date(ano, mes, dia);

    // 🔥 título base
    titulo.innerText = `${diaFormatado}/${mesFormatado}/${ano}`;

    // 🔥 controle botão + texto
    if (dataClicada < hoje) {

        btnNovo.disabled = true;
        btnNovo.style.opacity = "0.5";
        btnNovo.style.cursor = "not-allowed";

        titulo.innerText += " (Somente visualização)";

    } else {

        btnNovo.disabled = false;
        btnNovo.style.opacity = "1";
        btnNovo.style.cursor = "pointer";

    }

    const eventos = exames[chave] || [];

    if (eventos.length === 0) {
        listaDiv.innerHTML = "<p>Sem eventos.</p>";
    }

    eventos.forEach((ev, index) => {

        const div = document.createElement("div");
        div.classList.add("evento-item");

        div.innerHTML = `
            <div class="evento-tipo">${ev.tipo}</div>
            <div class="evento-info">📍 ${ev.local}</div>
            <div class="evento-info">🕒 ${ev.horario}</div>
            <div class="evento-info">👨‍⚕️ ${ev.medico || "-"}</div>

            <div class="evento-acoes">
                <button onclick="editarEvento('${chave}', ${index})">✏️</button>
                <button onclick="excluirEvento('${chave}', ${index})">🗑️</button>
            </div>
        `;

        listaDiv.appendChild(div);
    });

    modal.style.display = "flex";

    dataSelecionada = chave;
}

function excluirEvento(chave, index) {

    if (!confirm("Deseja excluir este evento?")) return;

    exames[chave].splice(index, 1);

    if (exames[chave].length === 0) {
        delete exames[chave];
    }

    localStorage.setItem("exames", JSON.stringify(exames));


    const [ano, mes, dia] = chave.split("-");
    abrirListaEventos(parseInt(dia), parseInt(mes) - 1, parseInt(ano));

    gerarCalendario();
}

function editarEvento(chave, index) {

    const evento = exames[chave][index];

    editandoIndex = index;
    dataSelecionada = chave;

    fecharLista();

    setTimeout(() => {

        abrirModal(
            parseInt(chave.split("-")[2]),
            parseInt(chave.split("-")[1]) - 1,
            parseInt(chave.split("-")[0]),
            evento
        );

    }, 150);
}


function fecharLista() {
    document.getElementById("modalLista").style.display = "none";
}

function abrirNovoEvento() {

    const [ano, mes, dia] = dataSelecionada.split("-");

    fecharLista();

    abrirModal(parseInt(dia), parseInt(mes) - 1, parseInt(ano));
}

function abrirModal(dia, mes, ano, evento = null) {

    const diaFormatado = String(dia).padStart(2, "0");
    const mesFormatado = String(mes + 1).padStart(2, "0");

    dataSelecionada = `${ano}-${mesFormatado}-${diaFormatado}`;

    const modal = document.getElementById("modalExame");

    modal.style.display = "flex";

    document.getElementById("dataSelecionada").innerText =
        `${diaFormatado}/${mesFormatado}/${ano}`;


    if (evento) {
        document.getElementById("local").value = evento.local;
        document.getElementById("tipo").value = evento.tipo;
        document.getElementById("medico").value = evento.medico;
        document.getElementById("horario").value = evento.horario;
        document.getElementById("levar").value = evento.levar;
    }

    else {
        document.getElementById("local").value = "";
        document.getElementById("tipo").value = "";
        document.getElementById("medico").value = "";
        document.getElementById("horario").value = "";
        document.getElementById("levar").value = "";
    }
}

function fecharModal() {

    const modal = document.getElementById("modalExame");

    if (modal) {
        modal.style.display = "none";
    }
}


function salvarExame() {
    console.log(document.getElementById("local"));
    console.log(document.getElementById("tipo"));
    console.log(document.getElementById("horario"));

    console.log("1 - entrou na função");

    if (!dataSelecionada) {
        console.log("2 - dataSelecionada está vazia");
        return;
    }

    console.log("3 - dataSelecionada OK:", dataSelecionada);

    const local = document.getElementById("local").value.trim();
    const tipo = document.getElementById("tipo").value;
    const medico = document.getElementById("medico").value.trim();
    const horario = document.getElementById("horario").value;
    const checkboxes = document.querySelectorAll(".check-list input:checked");

    const levar = Array.from(checkboxes).map(cb => cb.value);

    console.log("4 - dados capturados", { local, tipo, medico, horario, levar });

    if (!local || !tipo || !horario) {
        console.log("5 - falhou validação");
        alert("Preencha os campos obrigatórios");
        return;
    }

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

    console.log("6 - salvando no localStorage");

    localStorage.setItem("exames", JSON.stringify(exames));

    console.log("7 - atualizando interface");

    fecharModal();
    gerarCalendario();

    console.log("8 - finalizado");
}


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

const modalCard = document.querySelector(".modal-card");

const tipoSelect = document.getElementById("tipo");

if (tipoSelect) {
    tipoSelect.addEventListener("change", () => {

        const modalCard = document.querySelector(".modal-card");

        modalCard.classList.remove("consulta", "exame", "retorno");

        if (tipoSelect.value === "Consulta") {
            modalCard.classList.add("consulta");
        }

        if (tipoSelect.value === "Exame") {
            modalCard.classList.add("exame");
        }

        if (tipoSelect.value === "Retorno") {
            modalCard.classList.add("retorno");
        }

    });
}


gerarCalendario();