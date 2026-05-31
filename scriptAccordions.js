
console.log("Carregando componentes...");

// DADOS DAS UPA
const upas = [

  {
    nome: "UPA Tito Lopes",
    regiao: "São Miguel Paulista",
    endereco: "Av. Pires do Rio, 294 - Vila Americana, São Paulo - SP",
    nota: 2.5,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Urgência 24h",
      "Atendimento clínico",
      "Estabilização",
      "Triagem",
      "Emergência"
    ],

    mapa:
    "https://maps.google.com/?q=Av.+Pires+do+Rio,+294+São+Paulo",

    imagem:
    "./Img/upa_tito.png"
  },

  {
    nome: "UPA Vila Mariana",
    regiao: "Vila Mariana",
    endereco: "Rua Dr. Diogo de Faria, 609 - Vila Clementino, São Paulo - SP",
    nota: 2.8,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Pronto atendimento 24h",
      "Urgência e emergência",
      "Clínica geral",
      "Triagem"
    ],

    mapa:
    "https://maps.google.com/?q=Rua+Dr.+Diogo+de+Faria,+609+São+Paulo",

    imagem:
    "./Img/upa_vilaMariana.png"
  },

  {
    nome: "UPA Jabaquara",
    regiao: "Jabaquara",
    endereco: "Rua Cruz das Almas, 290 - Vila Campestre, São Paulo - SP",
    nota: 3.1,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Emergência",
      "Atendimento adulto",
      "Atendimento infantil",
      "Observação médica"
    ],

    mapa:
    "https://maps.google.com/?q=Rua+Cruz+das+Almas,+290+São+Paulo",

    imagem:
    "./Img/upa_jabaquara.png"
  },

  {
    nome: "UPA Santo Amaro",
    regiao: "Santo Amaro",
    endereco: "Rua Promotor Gabriel Nettuzzi Perez, 41 - Santo Amaro, São Paulo - SP",
    nota: 3.0,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Urgência",
      "Exames básicos",
      "Medicação",
      "Estabilização"
    ],

    mapa:
    "https://maps.google.com/?q=Rua+Promotor+Gabriel+Nettuzzi+Perez,+41+São+Paulo",

    imagem:
    "./Img/upa_santoAmaro.png"
  },

  {
    nome: "UPA Vergueiro",
    regiao: "Liberdade",
    endereco: "Rua Vergueiro, 613 - Liberdade, São Paulo - SP",
    nota: 2.7,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Pronto atendimento",
      "Exames laboratoriais",
      "Observação",
      "Urgência"
    ],

    mapa:
    "https://maps.google.com/?q=Rua+Vergueiro,+613+São+Paulo",

    imagem:
    "./Img/upa_Vergueiro.png"
  },

  {
    nome: "UPA Mooca",
    regiao: "Mooca",
    endereco: "Rua Dr. Fomm, 261 - Belenzinho, São Paulo - SP",
    nota: 3.0,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Urgência 24h",
      "Clínica médica",
      "Medicação",
      "Atendimento rápido"
    ],

    mapa:
    "https://maps.google.com/?q=Rua+Dr.+Fomm,+261+São+Paulo",

    imagem:
    "./Img/upa_mooca.png"
  },

  {
    nome: "UPA Tatuapé",
    regiao: "Tatuapé",
    endereco: "Av. Celso Garcia, 4974 - Tatuapé, São Paulo - SP",
    nota: 2.5,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Hospital-dia",
      "Emergência",
      "Atendimento rápido",
      "Observação"
    ],

    mapa:
    "https://maps.google.com/?q=Av.+Celso+Garcia,+4974+São+Paulo",

    imagem:
    "./Img/upa_tatuape.png"
  },

  {
    nome: "UPA Itaquera",
    regiao: "Artur Alvim",
    endereco: "Av. Miguel Ignácio Curi, 44 - Artur Alvim, São Paulo - SP",
    nota: 2.5,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Pronto atendimento",
      "Emergência",
      "Triagem",
      "Urgência"
    ],

    mapa:
    "https://maps.google.com/?q=Av.+Miguel+Ignácio+Curi,+44+São+Paulo",

    imagem:
    "./Img/upa_itaquera.png"
  },

  {
    nome: "UPA Jaçanã",
    regiao: "Jaçanã",
    endereco: "Rua Ester Elisa, s/n - Vila Nilo, São Paulo - SP",
    nota: 2.1,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: false
    },

    servicos: [
      "Emergência",
      "Observação",
      "Medicação",
      "Atendimento clínico"
    ],

    mapa:
    "https://maps.google.com/?q=Rua+Ester+Elisa+São+Paulo",

    imagem:
    "./Img/upa_jacana.png"
  },

  {
    nome: "UPA Vila Maria",
    regiao: "Vila Maria",
    endereco: "Praça Eng. Hugo Brandi, 15 - Vila Maria, São Paulo - SP",
    nota: 3.9,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Emergência 24h",
      "Atendimento clínico",
      "Exames",
      "Urgência"
    ],

    mapa:
    "https://maps.google.com/?q=Praça+Eng.+Hugo+Brandi,+15+São+Paulo",

    imagem:
    "./Img/upa_vila_maria.png"
  },

  {
    nome: "UPA Ermelino Matarazzo",
    regiao: "Ermelino Matarazzo",
    endereco: "Rua Miguel Novais, 113 - Vila Paranaguá, São Paulo - SP",
    nota: 2.3,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Pronto atendimento",
      "Urgência",
      "Atendimento geral",
      "Observação"
    ],

    mapa:
    "https://maps.google.com/?q=Rua+Miguel+Novais,+113+São+Paulo",

    imagem:
    "./Img/upa_Ermelino Matarazzo.png"
  },

  {
    nome: "UPA Rio Pequeno",
    regiao: "Rio Pequeno",
    endereco: "Rua José Vicente da Cruz, 90 - Vila Antônio, São Paulo - SP",
    nota: 4.0,

    acessibilidade: {
      rampas: true,
      banheiroAdaptado: true,
      sinalizacao: true
    },

    servicos: [
      "Pronto atendimento",
      "Emergência",
      "Exames básicos",
      "Urgência"
    ],

    mapa:
    "https://maps.google.com/?q=Rua+José+Vicente+da+Cruz,+90+São+Paulo",

    imagem:
    "./Img/upa_rio pequeno.png"
  }

];

// ACCORDION PRINCIPAL
const accordionBtns =
document.querySelectorAll(".accordion > .accordion-btn");

accordionBtns.forEach(btn => {

  btn.addEventListener("click", () => {

    const content = btn.nextElementSibling;

    if (!content) return;

    document.querySelectorAll(".accordion-content.open")
      .forEach(item => {

        if(item !== content){
          item.classList.remove("open");
        }

      });

    accordionBtns.forEach(otherBtn => {

      if(otherBtn !== btn){
        otherBtn.classList.remove("active");
      }

    });

    btn.classList.toggle("active");
    content.classList.toggle("open");

  });

});

// CRIAR CARD UPA
function criarCardUPA(upa){

  return `

    <div class="card-upa">

      <button class="upa-btn">

        <div
          class="card-topo"
          style="background-image:
          url('${upa.imagem}')"
        >

          <div class="overlay"></div>

          <div class="info">

            <h2>${upa.nome}</h2>

            <p>${upa.regiao}</p>

            <span>⭐ ${upa.nota}</span>

          </div>

        </div>

      </button>

      <div class="accordion-content upa-content">

        <div class="detalhes-upa">

          <p>
            <strong>Endereço:</strong>
            ${upa.endereco}
          </p>

          <div class="servicos">

            <h3>Serviços:</h3>

            <ul>

              ${upa.servicos.map(servico => `
                <li>${servico}</li>
              `).join("")}

            </ul>

          </div>

          <div class="acessibilidade">

            <h3>Acessibilidade:</h3>

            <p>
              ♿ Rampas:
              ${upa.acessibilidade.rampas
                ? "Sim"
                : "Não"}
            </p>

            <p>
              🚻 Banheiro adaptado:
              ${upa.acessibilidade.banheiroAdaptado
                ? "Sim"
                : "Não"}
            </p>

            <p>
              🧭 Sinalização:
              ${upa.acessibilidade.sinalizacao
                ? "Boa"
                : "Ruim"}
            </p>

          </div>

          <div class="mapa">

            <iframe
              src="${upa.mapa}&output=embed"
              loading="lazy"
            >
            </iframe>

          </div>

        </div>

      </div>

    </div>

  `;

}

// INSERIR AS UPA
const container =
document.getElementById("container-upas");

if(container){

  upas.forEach(upa => {

    container.innerHTML +=
    criarCardUPA(upa);

  });

}

// ACCORDION INTERNO DAS UPA
document.addEventListener("click", function(e){

  const upaBtn =
  e.target.closest(".upa-btn");

  if(!upaBtn) return;

  const content =
  upaBtn.nextElementSibling;

  if(content){

    content.classList.toggle("open");

  }

});

// FUNÇÃO MAPA

function abrirMapa(tipo){

  if(tipo === "UBS"){

    window.open(
      "https://www.google.com/maps/search/UBS+próxima",
      "_blank"
    );

  }

  else if(tipo === "UPA"){

    window.open(
      "https://www.google.com/maps/search/UPA+próxima",
      "_blank"
    );

  }

}

