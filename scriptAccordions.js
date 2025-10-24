// servicos.js
const accordionBtns = document.querySelectorAll(".accordion-btn");

accordionBtns.forEach(btn => {
  btn.addEventListener("click", () => {
    btn.classList.toggle("active");
    const content = btn.nextElementSibling;
    content.style.display = content.style.display === "block" ? "none" : "block";
  });
});

// Exemplo de função para abrir mapa
function abrirMapa(tipo) {
  if (tipo === "UBS") {
    window.open("https://www.google.com/maps/search/UBS+próxima", "_blank");
  } else if (tipo === "UPA") {
    window.open("https://www.google.com/maps/search/UPA+próxima", "_blank");
  }
}


