const form = document.getElementById("temperaturaForm");

form.addEventListener("submit", async (e) => {

    e.preventDefault();

    const idUsuarioRegistro =
        document.getElementById("idUsuarioRegistro").value;

    const valorTemperatura =
        document.getElementById("valorTemperatura").value;

    const data =
        document.getElementById("data").value;

    const hora =
        document.getElementById("hora").value;

    const observacoes =
        document.getElementById("observacoes").value;

    try {

        const response = await fetch(
            "php/usuario/registros/salvarTemperatura.php",
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body: new URLSearchParams({
                    idUsuarioRegistro,
                    valorTemperatura,
                    data,
                    hora,
                    observacoes
                })
            }
        );

        const resultado = await response.text();

        if (resultado.includes("sucesso")) {

            alert("Temperatura registrada com sucesso!");

            form.reset();

        } else {

            alert(resultado);

        }

    } catch (erro) {

        console.error(erro);

        alert("Erro ao salvar.");

    }

});