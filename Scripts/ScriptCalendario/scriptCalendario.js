let editandoIndex = null;
const diasContainer = document.getElementById("dias");
const mesAno = document.getElementById("mesAno");

let data = new Date();
let dataSelecionada = null;
let idEventoEditando = null;

/* vem do PHP */
let usuarioSelecionado = usuarioLogado;

let exames = {};

function selecionarUsuario(idUsuario, element) {

    if (papelUsuario !== "responsavel" && idUsuario != usuarioLogado) {
        alert("Você não pode visualizar a agenda de outros membros.");
        return;
    }

    usuarioSelecionado = idUsuario;

    // remove destaque de todos
    document.querySelectorAll(".person").forEach(p => {
        p.classList.remove("ativo");
    });

    // adiciona destaque no clicado
    element.classList.add("ativo");

    carregarEventos();
}

async function carregarEventos() {
    try {
        const response = await fetch(
            `php/usuario/calendario/listarEventos.php?usuario=${usuarioSelecionado}`
        );

        exames = await response.json();
        gerarCalendario();

    } catch (erro) {
        console.error("Erro ao carregar eventos:", erro);
    }
}

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
                const notif = document.createElement("div");
                notif.classList.add("exameResumo");
                notif.innerText = exame.tipo;

                switch (exame.tipo) {
                    case "Consulta":
                        notif.classList.add("consulta");
                        break;
                    case "Exame":
                        notif.classList.add("exame");
                        break;
                    case "Retorno":
                        notif.classList.add("retorno");
                        break;
                }

                divDia.appendChild(notif);
            });

            if (lista.length > 2) {
                const mais = document.createElement("div");
                mais.classList.add("ver-mais");
                mais.innerText = `+${lista.length - 2} Ver mais`;
                divDia.appendChild(mais);
            }
        }

        const hoje = new Date();
        hoje.setHours(0, 0, 0, 0);

        const dataDia = new Date(ano, mes, dia);

        if (dataDia < hoje) {
            divDia.classList.add("dia-passado");

            divDia.addEventListener("click", () => {
                abrirListaEventos(dia, mes, ano); // só visualiza
            });

        } else {
            divDia.addEventListener("click", () => {
                abrirListaEventos(dia, mes, ano);
            });
        }

        diasContainer.appendChild(divDia);
    }
}

function abrirListaEventos(dia, mes, ano) {
    const diaFormatado = String(dia).padStart(2, "0");
    const mesFormatado = String(mes + 1).padStart(2, "0");
    const chave = `${ano}-${mesFormatado}-${diaFormatado}`;

    dataSelecionada = chave;

    const modal = document.getElementById("modalLista");
    const listaDiv = document.getElementById("listaEventos");
    const titulo = document.getElementById("tituloLista");
    const btnNovo = document.getElementById("btnNovo");

    listaDiv.innerHTML = "";
    titulo.innerText = `${diaFormatado}/${mesFormatado}/${ano}`;

    // BLOQUEAR DIAS PASSADOS
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0);

    const dataClicada = new Date(ano, mes, dia);

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

        let botoesAcoes = "";

        const podeEditar =
            papelUsuario === "responsavel" ||
            usuarioSelecionado == usuarioLogado;

        if (podeEditar) {
            botoesAcoes = `
            <div class="evento-acoes">
                <button onclick="editarEvento('${chave}', ${index})">✏️</button>
                <button onclick="excluirEvento('${ev.id}')">🗑️</button>
            </div>
        `;
        }

        div.innerHTML = `
        <div class="evento-tipo">${ev.tipo}</div>
        <div class="evento-info">📍 ${ev.local}</div>
        <div class="evento-info">🕒 ${ev.horario}</div>
        <div class="evento-info">👨‍⚕️ ${ev.medico || "-"}</div>
        ${botoesAcoes}
    `;

        listaDiv.appendChild(div);
    });

    modal.style.display = "flex";
}


async function excluirEvento(idEvento) {

    if (!confirm("Deseja excluir este evento?")) return;

    try {
        const response = await fetch(
            "php/usuario/calendario/excluirEvento.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    idEvento
                })
            }
        );

        const resultado = await response.text();

        if (resultado.trim() !== "ok") {
            alert("Erro ao excluir evento");
            return;
        }

        fecharLista();
        carregarEventos();

    } catch (erro) {
        console.error(erro);
        alert("Erro ao excluir");
    }
}


function editarEvento(chave, index) {
    const evento = exames[chave][index];

    idEventoEditando = evento.id;

    const [ano, mes, dia] = chave.split("-");

    abrirModal(
        parseInt(dia),
        parseInt(mes) - 1,
        parseInt(ano),
        evento
    );

    fecharLista();
}

function abrirNovoEvento() {
    const [ano, mes, dia] = dataSelecionada.split("-");
    abrirModal(parseInt(dia), parseInt(mes) - 1, parseInt(ano));
}

function abrirModal(dia, mes, ano, evento = null) {
    const diaFormatado = String(dia).padStart(2, "0");
    const mesFormatado = String(mes + 1).padStart(2, "0");

    dataSelecionada = `${ano}-${mesFormatado}-${diaFormatado}`;

    document.getElementById("modalExame").style.display = "flex";
    document.getElementById("dataSelecionada").innerText =
        `${diaFormatado}/${mesFormatado}/${ano}`;

    document.getElementById("local").value = evento?.local || "";
    document.getElementById("tipo").value = evento?.tipo || "";
    document.getElementById("medico").value = evento?.medico || "";
    document.getElementById("horario").value = evento?.horario || "";
}

function fecharModal() {
    document.getElementById("modalExame").style.display = "none";
}

function fecharLista() {
    document.getElementById("modalLista").style.display = "none";
}

async function salvarExame() {
    const local = document.getElementById("local").value.trim();
    const tipo = document.getElementById("tipo").value;
    const medico = document.getElementById("medico").value.trim();
    const horario = document.getElementById("horario").value;

    const checkboxes = document.querySelectorAll(".check-list input:checked");
    const levar = Array.from(checkboxes).map(cb => cb.value);

    if (!local || !tipo || !horario) {
        alert("Preencha os campos obrigatórios");
        return;
    }

    try {
        const response = await fetch(
            "php/usuario/calendario/salvarEvento.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    idEvento: idEventoEditando || "",
                    local,
                    tipo,
                    medico,
                    horario,
                    data: dataSelecionada,
                    levar: levar.join(", "),
                    usuario: usuarioSelecionado
                })
            }
        );

        const resultado = await response.text();

        if (resultado.trim() !== "ok") {
            alert(resultado);
            return;
        }

        fecharModal();
        carregarEventos();

    } catch (erro) {
        console.error(erro);
        alert("Erro ao salvar evento");
    }
}

document.getElementById("prev").addEventListener("click", () => {
    data.setMonth(data.getMonth() - 1);
    gerarCalendario();
});

document.getElementById("next").addEventListener("click", () => {
    data.setMonth(data.getMonth() + 1);
    gerarCalendario();
});

const campoBusca = document.getElementById("buscarMembro");

if (campoBusca) {
    campoBusca.addEventListener("input", function () {
        const termo = this.value.toLowerCase();

        document.querySelectorAll(".person").forEach(person => {
            const nome = person.querySelector("p").textContent.toLowerCase();

            if (nome.includes(termo)) {
                person.style.display = "flex";
            } else {
                person.style.display = "none";
            }
        });
    });
}

/* inicia carregando agenda do usuário logado */
carregarEventos();