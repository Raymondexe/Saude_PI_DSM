document.addEventListener("DOMContentLoaded", () => {
    const toggleTheme = document.getElementById("toggle-theme");

    function setTheme(theme) {
      if (theme === "dark") {
        document.body.classList.add("dark-mode");
        toggleTheme.textContent = "☀️ Modo Claro";
      } else {
        document.body.classList.remove("dark-mode");
        toggleTheme.textContent = "🌙 Modo Escuro";
      }
      localStorage.setItem("theme", theme);
    }

    // Carregar tema salvo
    setTheme(localStorage.getItem("theme") || "light");

    // Alternar tema
    toggleTheme.addEventListener("click", () => {
      const isDark = document.body.classList.contains("dark-mode");
      setTheme(isDark ? "light" : "dark");
    });
});
/* ==== DROPDOWN MENU ==== */


/* ==== DROPDOWN PRINCIPAL ==== */
const configBtn = document.getElementById("config-btn");
const dropdown = document.querySelector(".config-menu .dropdown");
const accessibilityToggle = document.getElementById("accessibility");
const subDropdown = document.querySelector(".submenu .sub-dropdown");


configBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  const isOpen = dropdown.style.display === "flex";
  dropdown.style.display = isOpen ? "none" : "flex";
  dropdown.style.flexDirection = "column";

  // Só mexe no subDropdown se ele existir
  if (subDropdown) {
    subDropdown.style.display = "none";
    accessibilityToggle.setAttribute("aria-expanded", "false");
  }
});

document.addEventListener("click", (e) => {
  if (!e.target.closest(".config-menu")) {
    dropdown.style.display = "none";
    if (subDropdown) subDropdown.style.display = "none";
    configBtn.setAttribute("aria-expanded", "false");
    if (accessibilityToggle) accessibilityToggle.setAttribute("aria-expanded", "false");
  }
});

accessibilityToggle.addEventListener("click", (e) => {
  e.stopPropagation();
  const isOpen = subDropdown.style.display === "flex";
  subDropdown.style.display = isOpen ? "none" : "flex";
  accessibilityToggle.setAttribute("aria-expanded", !isOpen);
});

document.addEventListener("click", (e) => {
  if (!e.target.closest(".config-menu")) {
    dropdown.style.display = "none";
    subDropdown.style.display = "none";
    configBtn.setAttribute("aria-expanded", "false");
    accessibilityToggle.setAttribute("aria-expanded", "false");
  }
});

/* END DARK/LIGHT MODE*/



















