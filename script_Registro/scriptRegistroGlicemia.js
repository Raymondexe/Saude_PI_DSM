const form = document.getElementById("glicemiaForm");

form.addEventListener("submit", async (e) => {

    e.preventDefault();

    const idUsuarioRegistro =
        document.getElementById("idUsuarioRegistro").value;

    const valorGlicemia =
        document.getElementById("valorGlicemia").value;

    const tipoMedicao =
        document.getElementById("tipoMedicao").value;

    const data =
        document.getElementById("data").value;

    const hora =
        document.getElementById("hora").value;

    const observacoes =
        document.getElementById("observacoes").value;

    try {

        const response = await fetch(
            "php/usuario/registros/salvarGlicemia.php",
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body: new URLSearchParams({
                    idUsuarioRegistro,
                    valorGlicemia,
                    tipoMedicao,
                    data,
                    hora,
                    observacoes
                })
            }
        );

        const resultado = await response.text();

        if (resultado.includes("sucesso")) {

            alert("Glicemia registrada com sucesso!");

            form.reset();

        } else {

            alert(resultado);

        }

    } catch (erro) {

        console.error(erro);

        alert("Erro ao salvar.");

    }

});