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

/*  TRADUÇÃO BÁSICA */

const langBtn = document.getElementById("change-lang");

const translations = {
  pt: {
    home: "Home",
    monitoring: "Monitoramento",
    history: "Histórico",
    services: "Serviços",
    about: "Quem somos",
    darkMode: "🌙 Modo Escuro",
    lightMode: "☀️ Modo Claro",
    accessibility: "♿ Acessibilidade",
    changeLang: "🌎 Trocar Idioma",
    title: "Bem-vindo ao Bem-Estar 360!",
    text1: "O Bem-Estar 360 foi desenvolvido para oferecer um acompanhamento completo da sua saúde. Aqui, você poderá registrar diariamente seus níveis de glicemia, pressão arterial, batimentos cardíacos e temperatura, permitindo um monitoramento detalhado do seu estado físico.",
    text2: "Além do controle diário, nosso portal oferece conteúdos educativos sobre saúde e bem-estar, ajudando você a compreender melhor seu corpo e adotar hábitos mais saudáveis.",
    text3: "Recomendamos realizar os registros de forma consistente para que os profissionais de saúde possam analisar seu histórico com precisão, proporcionando orientações mais assertivas e personalizadas.",
    cardPress: "Pressão Arterial",
    cardPressP: "Registre e acompanhe seus valores de pressão arterial.",
    cardPressA: "Registre sua Pressão Arterial",
    cardGlicemia: "Glicemia",
    cardGlicemiaP: "Registre e acompanhe seus níveis de glicose no sangue.",
    cardGlicemiaA: "Registrar Glicemia",
    cardBatimentos: "Batimentos Cardíacos",
    cardBatimentosP: "Registre e acompanhe sua frequência cardíaca.",
    cardBatimentosA: "Registrar Batimentos",
    cardTemperatura: "Temperatura",
    cardTemperaturaP: "Registre e acompanhe sua temperatura corporal.",
    cardTemperaturaA: "Registrar Temperatura",
    newsTitle: "Últimas notícias sobre saúde",
    newsTagSUS: "#Sus",
    newsTitleSUS: "SUS garante atendimento de qualidade a todos os brasileiros",
    newsTextSUS: "O Sistema Único de Saúde (SUS) continua oferecendo atendimento gratuito, promovendo prevenção, tratamentos e acompanhamento em diversas áreas da saúde, reforçando seu papel essencial na vida da população.",
    newsTagDiabetes: "#Diabete",
    newsTitleDiabetes: "Diabetes exige acompanhamento e cuidados contínuos",
    newsTextDiabetes: "O diabetes é uma doença crônica que exige atenção diária à alimentação, atividade física e controle médico. O diagnóstico precoce é essencial para evitar complicações graves.",
    newsTagSexualHealth: "#Saúde Sexual",
    newsTitleSexualHealth: "Saúde sexual: importância da prevenção e bem-estar",
    newsTextSexualHealth: "Especialistas alertam sobre a necessidade de cuidados com a saúde sexual, que envolve bem-estar físico, emocional e social, além da prevenção de doenças e promoção de relacionamentos saudáveis.",
    newsTagMentalHealth: "#Saúde Mental",
    newsTitleMentalHealth: "Saúde mental recebe atenção crescente no Brasil",
    newsTextMentalHealth: "O cuidado com a saúde mental é cada vez mais valorizado, envolvendo estratégias de prevenção, tratamento de transtornos psicológicos e promoção do equilíbrio emocional para uma melhor qualidade de vida.",
    newsTagGlaucoma: "#Glaucoma",
    newsTitleGlaucoma: "Glaucoma: doença silenciosa que ameaça a visão",
    newsTextGlaucoma: "O glaucoma, caracterizado pelo aumento da pressão ocular, pode levar à perda da visão se não for diagnosticado precocemente. Profissionais recomendam consultas regulares para prevenção.",
    newsBtn: "arrow_forward",
    footerHome: "Home",
    footerMonitoring: "Monitoramento",
    footerHistory: "Histórico",
    footerServices: "Serviços",
    footerFaq: "FAQ",
    footerContactTitle: "Contato",
    footerEmail: "Email: contato@bemestar360.com",
    footerPhone: "Telefone: (11) 1234-5678",
    footerCopy: "© 2025 Bem-Estar 360. Todos os direitos reservados.",
    heartTitle: "Registre os seus Batimentos Cardíacos",
    timerLabel: "Conte seus batimentos por 60 segundos",
    timerHelp: "Não sabe contar seus batimentos manualmente?",
    startTimerBtn: "Iniciar Contagem",
    resetTimerBtn: "Reiniciar Contagem",
    bpmLabel: "Batimentos por Minuto (bpm)",
    bpmPlaceholder: "Ex: 72",
    dateLabel: "Data da Medição",
    timeLabel: "Hora da Medição",
    obsLabel: "Observações",
    obsPlaceholder: "Ex: medi após caminhada, senti cansaço...",
    saveBtn: "Salvar Registro",
    helpTitle: "Como interpretar seus registros de batimentos cardíacos",
    helpIntro: "Veja abaixo como entender os valores de frequência cardíaca registrados:",
    helpNormalTitle: "Normal",
    helpNormalRange: "Frequência cardíaca: 60-100 bpm",
    helpNormalText: "✅ Está dentro do esperado, continue monitorando regularmente.",
    helpAttentionTitle: "Atenção",
    helpAttentionRange: "Frequência cardíaca: 101-120 bpm",
    helpAttentionText: "⚠️ Fique atento! Pode indicar estresse, esforço físico recente ou necessidade de avaliação.",
    helpDangerTitle: "Perigo",
    helpDangerRange: "Frequência cardíaca: Menor que 50 bpm ou Maior que 120 bpm",
    helpDangerText: "⛔ Procure orientação médica imediatamente!",
    videoManual: "Manualmente",
    videoDigital: "Com aparelho digital de pulso",
  },

  en: {
    home: "Home",
    monitoring: "Monitoring",
    history: "History",
    services: "Services",
    about: "About Us",
    darkMode: "🌙 Dark Mode",
    lightMode: "☀️ Light Mode",
    accessibility: "♿ Accessibility",
    changeLang: "🌎 Change Language",
    title: "Welcome to Bem-Estar 360!",
    text1: "Bem-Estar 360 was developed to offer comprehensive monitoring of your health. Here, you can record your blood sugar, blood pressure, heart rate, and temperature levels on a daily basis, allowing for detailed monitoring of your physical condition.",
    text2: "In addition to daily monitoring, our portal offers educational content on health and wellness, helping you to better understand your body and adopt healthier habits.",
    text3: "We recommend keeping consistent records so that healthcare professionals can accurately analyze your history, providing more assertive and personalized guidance.",
    cardPress: "Blood Pressure",
    cardPressP: "Record and track your blood pressure readings.",
    cardPressA: "Record your blood pressure",
    cardGlicemia: "Blood Sugar",
    cardGlicemiaP: "Record and track your blood sugar levels.",
    cardGlicemiaA: "Record Blood Sugar",
    cardBatimentos: "Heart Rate",
    cardBatimentosP: "Record and track your heart rate.",
    cardBatimentosA: "Record Heart Rate",
    cardTemperatura: "Temperature",
    cardTemperaturaP: "Record and track your body temperature.",
    cardTemperaturaA: "Record Temperature",
    newsTitle: "Últimas notícias sobre saúde",
    newsTagSUS: "#Sus",
    newsTitleSUS: "SUS garante atendimento de qualidade a todos os brasileiros",
    newsTextSUS: "O Sistema Único de Saúde (SUS) continua oferecendo atendimento gratuito, promovendo prevenção, tratamentos e acompanhamento em diversas áreas da saúde, reforçando seu papel essencial na vida da população.",
    newsTagDiabetes: "#Diabete",
    newsTitleDiabetes: "Diabetes exige acompanhamento e cuidados contínuos",
    newsTextDiabetes: "O diabetes é uma doença crônica que exige atenção diária à alimentação, atividade física e controle médico. O diagnóstico precoce é essencial para evitar complicações graves.",
    newsTagSexualHealth: "#Saúde Sexual",
    newsTitleSexualHealth: "Saúde sexual: importância da prevenção e bem-estar",
    newsTextSexualHealth: "Especialistas alertam sobre a necessidade de cuidados com a saúde sexual, que envolve bem-estar físico, emocional e social, além da prevenção de doenças e promoção de relacionamentos saudáveis.",
    newsTagMentalHealth: "#Saúde Mental",
    newsTitleMentalHealth: "Saúde mental recebe atenção crescente no Brasil",
    newsTextMentalHealth: "O cuidado com a saúde mental é cada vez mais valorizado, envolvendo estratégias de prevenção, tratamento de transtornos psicológicos e promoção do equilíbrio emocional para uma melhor qualidade de vida.",
    newsTagGlaucoma: "#Glaucoma",
    newsTitleGlaucoma: "Glaucoma: doença silenciosa que ameaça a visão",
    newsTextGlaucoma: "O glaucoma, caracterizado pelo aumento da pressão ocular, pode levar à perda da visão se não for diagnosticado precocemente. Profissionais recomendam consultas regulares para prevenção.",
    newsBtn: "arrow_forward",
    newsTitle: "Latest Health News",
    newsTagSUS: "#SUS",
    newsTitleSUS: "SUS ensures quality healthcare for all Brazilians",
    newsTextSUS: "The Unified Health System (SUS) continues to provide free healthcare, promoting prevention, treatments, and monitoring across multiple health areas, reinforcing its essential role in people's lives.",
    newsTagDiabetes: "#Diabetes",
    newsTitleDiabetes: "Diabetes requires continuous care and monitoring",
    newsTextDiabetes: "Diabetes is a chronic disease that requires daily attention to diet, physical activity, and medical supervision. Early diagnosis is essential to avoid serious complications.",
    newsTagSexualHealth: "#Sexual Health",
    newsTitleSexualHealth: "Sexual health: importance of prevention and well-being",
    newsTextSexualHealth: "Experts warn about the need for sexual health care, involving physical, emotional, and social well-being, as well as disease prevention and promotion of healthy relationships.",
    newsTagMentalHealth: "#Mental Health",
    newsTitleMentalHealth: "Mental health receives growing attention in Brazil",
    newsTextMentalHealth: "Mental health care is increasingly valued, involving prevention strategies, treatment of psychological disorders, and promotion of emotional balance for a better quality of life.",
    newsTagGlaucoma: "#Glaucoma",
    newsTitleGlaucoma: "Glaucoma: silent disease that threatens vision",
    newsTextGlaucoma: "Glaucoma, characterized by increased eye pressure, can lead to vision loss if not diagnosed early. Professionals recommend regular check-ups for prevention.",
    newsBtn: "arrow_forward",
    footerHome: "Home",
    footerMonitoring: "Monitoring",
    footerHistory: "History",
    footerServices: "Services",
    footerFaq: "FAQ",
    footerContactTitle: "Contact",
    footerEmail: "Email: contato@bemestar360.com",
    footerPhone: "Phone: +55 (11) 1234-5678",
    footerCopy: "© 2025 Wellness 360. All rights reserved.",
    heartTitle: "Record Your Heart Rate",
    timerLabel: "Count your beats for 60 seconds",
    timerHelp: "Don't know how to count your beats manually?",
    startTimerBtn: "Start Counting",
    resetTimerBtn: "Reset Counting",
    bpmLabel: "Beats Per Minute (bpm)",
    bpmPlaceholder: "Ex: 72",
    dateLabel: "Measurement Date",
    timeLabel: "Measurement Time",
    obsLabel: "Observations",
    obsPlaceholder: "Ex: measured after walking, felt tired...",
    saveBtn: "Save Record",
    helpTitle: "How to interpret your heart rate records",
    helpIntro: "See below how to understand the recorded heart rate values:",
    helpNormalTitle: "Normal",
    helpNormalRange: "Heart rate: 60-100 bpm",
    helpNormalText: "✅ Within the expected range, continue monitoring regularly.",
    helpAttentionTitle: "Attention",
    helpAttentionRange: "Heart rate: 101-120 bpm",
    helpAttentionText: "⚠️ Be alert! Could indicate stress, recent physical exertion, or need for evaluation.",
    helpDangerTitle: "Danger",
    helpDangerRange: "Heart rate: Less than 50 bpm or greater than 120 bpm",
    helpDangerText: "⛔ Seek medical advice immediately!",
    videoManual: "Manually",
    videoDigital: "With digital wrist device",
    
  }
};

// Carregar preferência do idioma
let currentLang = localStorage.getItem("lang") || "pt";
applyLanguage(currentLang);

// Função para aplicar o idioma
function applyLanguage(lang) {
  document.querySelectorAll("[data-lang]").forEach(el => {
    const key = el.getAttribute("data-lang");
    el.textContent = translations[lang][key];
  });

  // Atualizar textos dos botões do dropdown
  toggleTheme.textContent = document.body.classList.contains("dark-mode")
    ? translations[lang].lightMode
    : translations[lang].darkMode;
  document.getElementById("accessibility").textContent = translations[lang].accessibility;
  langBtn.textContent = translations[lang].changeLang;

  localStorage.setItem("lang", lang);
}

// Alternar idioma ao clicar
langBtn.addEventListener("click", () => {
  currentLang = currentLang === "pt" ? "en" : "pt";
  applyLanguage(currentLang);
});




















