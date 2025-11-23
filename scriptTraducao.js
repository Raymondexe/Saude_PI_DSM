// Dicionário de traduções
const translations = {
    pt: {
        home: "Home",
        monitoring: "Monitoramento",
        services: "Serviços",
        about: "Quem somos",
        login: "Login",

        h1BemVindo: "Bem-vindo ao Bem-Estar 360",
        pTexto1: "Monitore sua saúde diariamente e acompanhe seu bem-estar de forma simples, intuitiva e confiável.",
        pTexto2: "Nosso objetivo é ajudá-lo a compreender seu corpo, adotando hábitos saudáveis e prevenindo riscos.",

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

        newsTagIdosos: "#Bem Estar de Idosos",
        newsTextIdoosos: "Como monitorar glicemia, pressão, temperatura e batimentos cardíacos pode transformar qualidade de vida — e quais são os obstáculos que o novo aplicativo enfrenta nesse caminho.",

        newsBtn: "arrow_forward",

        textContainerControle: "Registre seus dados de saúde",
        textPcontainer: "Para manter seu acompanhamento atualizado, clique nos cards abaixo e registre suas medições de pressão arterial, glicemia, batimentos cardíacos e temperatura. O registro diário ajuda você e os profissionais de saúde a monitorarem seu bem-estar com mais precisão.",

        cardPress: "Pressão Arterial",
        cardPressP: "Registre e acompanhe seus valores de pressão arterial.",
        cardPressA: "Registrar Pressão Arterial",

        cardGlicemia: "Glicemia",
        cardGlicemiaP: "Registre e acompanhe seus níveis de glicose no sangue.",
        cardGlicemiaA: "Registrar Glicemia",

        cardBatimentos: "Batimentos Cardíacos",
        cardBatimentosP: "Registre e acompanhe sua frequência cardíaca.",
        cardBatimentosA: "Registrar Batimentos",

        cardTemperatura: "Temperatura",
        cardTemperaturaP: "Registre e acompanhe sua temperatura corporal.",
        cardTemperaturaA: "Registrar Temperatura",

        textUbsSection: "Nossas UBS",
        textVerRota: "Ver rota",

        ubsDesc1: "Localizada na Rua Américo Salvador Novelli, 265, oferece atendimento clínico, vacinação e exames de rotina.",
        ubsDesc2: "Situada na Rua Ipopoca, 61, oferece atendimento clínico, vacinação e exames de rotina.",
        ubsDesc3: "Localizada na Rua Paulino Serqueira, 1, oferece atendimento clínico, vacinação e exames de rotina.",
        ubsDesc4: "Situada na Rua Murmúrios da Tarde, oferece atendimento clínico, vacinação e exames de rotina.",
        ubsDesc5: "Localizada na Rua Sílvio Barbini, 40, oferece atendimento clínico, vacinação e exames de rotina.",
        ubsDesc6: "Localizada na Av. Dr. Francisco Munhoz Filho, 379, oferece atendimento clínico, vacinação e exames de rotina.",

        footerHome: "Home",
        footerMonitoring: "Monitoramento",
        footerHistory: "Histórico",
        footerServices: "Serviços",
        footerFaq: "FAQ",
        footerContactTitle: "Contato",
        footerEmail: "Email: contato@bemestar360.com",
        footerPhone: "Telefone: (11) 1234-5678",
        footerCopy: "© 2025 Bem-Estar 360. Todos os direitos reservados."
    },

    en: {
        home: "Home",
        monitoring: "Monitoring",
        services: "Services",
        about: "About Us",
        login: "Login",

        h1BemVindo: "Welcome to Bem-Estar 360",
        pTexto1: "Monitor your health daily and track your well-being easily, intuitively and reliably.",
        pTexto2: "Our goal is to help you understand your body, adopt healthy habits and prevent risks.",

        newsTitle: "Latest health news",

        newsTagSUS: "#SUS",
        newsTitleSUS: "SUS ensures quality care for all Brazilians",
        newsTextSUS: "The Unified Health System (SUS) continues offering free care, promoting prevention, treatments and follow-up in various health areas, reinforcing its essential role in people's lives.",

        newsTagDiabetes: "#Diabetes",
        newsTitleDiabetes: "Diabetes requires continuous care and monitoring",
        newsTextDiabetes: "Diabetes is a chronic disease that requires daily attention to diet, physical activity and medical control. Early diagnosis is essential to avoid serious complications.",

        newsTagSexualHealth: "#Sexual Health",
        newsTitleSexualHealth: "Sexual health: importance of prevention and well-being",
        newsTextSexualHealth: "Experts warn of the need for sexual health care, involving physical, emotional, and social well-being, as well as disease prevention and healthy relationships.",

        newsTagMentalHealth: "#Mental Health",
        newsTitleMentalHealth: "Mental health receives growing attention in Brazil",
        newsTextMentalHealth: "Mental health care is increasingly valued, involving prevention strategies, treatment of psychological disorders, and emotional balance for a better quality of life.",

        newsTagGlaucoma: "#Glaucoma",
        newsTitleGlaucoma: "Glaucoma: the silent disease that threatens vision",
        newsTextGlaucoma: "Glaucoma, characterized by increased eye pressure, can lead to vision loss if not diagnosed early. Professionals recommend regular checkups for prevention.",

        newsTagIdosos: "#Elderly Well-being",
        newsTextIdoosos: "Monitoring glucose, blood pressure, temperature, and heart rate can transform quality of life — and here are the challenges the new app faces along the way.",

        newsBtn: "arrow_forward",

        textContainerControle: "Record your health data",
        textPcontainer: "To keep your follow-up up to date, click the cards below and record your measurements. Daily records help you and health professionals monitor your well-being more accurately.",

        cardPress: "Blood Pressure",
        cardPressP: "Record and track your blood pressure levels.",
        cardPressA: "Record Blood Pressure",

        cardGlicemia: "Blood Glucose",
        cardGlicemiaP: "Record and track your blood glucose levels.",
        cardGlicemiaA: "Record Blood Glucose",

        cardBatimentos: "Heart Rate",
        cardBatimentosP: "Record and track your heart rate.",
        cardBatimentosA: "Record Heart Rate",

        cardTemperatura: "Temperature",
        cardTemperaturaP: "Record and track your body temperature.",
        cardTemperaturaA: "Record Temperature",

        textUbsSection: "Our Health Centers",
        textVerRota: "See route",

        ubsDesc1: "Located at Rua Américo Salvador Novelli, 265, offering clinical care, vaccinations, and routine exams.",
        ubsDesc2: "Located at Rua Ipopoca, 61, offering clinical care, vaccinations, and routine exams.",
        ubsDesc3: "Located at Rua Paulino Serqueira, 1, offering clinical care, vaccinations, and routine exams.",
        ubsDesc4: "Located at Rua Murmúrios da Tarde, offering clinical care, vaccinations, and routine exams.",
        ubsDesc5: "Located at Rua Sílvio Barbini, 40, offering clinical care, vaccinations, and routine exams.",
        ubsDesc6: "Located at Av. Dr. Francisco Munhoz Filho, 379, offering clinical care, vaccinations, and routine exams.",

        footerHome: "Home",
        footerMonitoring: "Monitoring",
        footerHistory: "History",
        footerServices: "Services",
        footerFaq: "FAQ",
        footerContactTitle: "Contact",
        footerEmail: "Email: contact@bemestar360.com",
        footerPhone: "Phone: (11) 1234-5678",
        footerCopy: "© 2025 Bem-Estar 360. All rights reserved."
    }
};


// Função para trocar idioma
function changeLanguage(lang) {
    localStorage.setItem("lang", lang);

    document.querySelectorAll("[data-lang]").forEach(element => {
        const key = element.getAttribute("data-lang");

        if (translations[lang][key]) {
            element.innerHTML = translations[lang][key];
        }
    });
}


// Detecta o idioma salvo
document.addEventListener("DOMContentLoaded", () => {
    const savedLang = localStorage.getItem("lang") || "pt";
    changeLanguage(savedLang);

    // Botão de troca de idioma
    const btn = document.getElementById("change-lang");
    if (btn) {
        btn.addEventListener("click", () => {
            const newLang = localStorage.getItem("lang") === "pt" ? "en" : "pt";
            changeLanguage(newLang);
        });
    }
});
