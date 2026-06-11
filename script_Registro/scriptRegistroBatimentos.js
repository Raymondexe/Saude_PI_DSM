const form = document.getElementById("batimentosForm");

form.addEventListener("submit", async (e) => {

    e.preventDefault();

    const idUsuarioRegistro =
        document.getElementById("idUsuarioRegistro").value;

    const bpm =
        document.getElementById("valorBatimentos").value;

    const data =
        document.getElementById("data").value;

    const hora =
        document.getElementById("hora").value;

    const observacoes =
        document.getElementById("observacoes").value;

    try {

        const response = await fetch(
            "php/usuario/registros/salvarBatimentos.php",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                body: new URLSearchParams({
                    idUsuarioRegistro,
                    bpm,
                    data,
                    hora,
                    observacoes
                })
            }
        );

        const resultado = await response.text();

        if (resultado.includes("sucesso")) {

            alert("Batimentos registrados com sucesso!");

            form.reset();

        } else {

            alert(resultado);

        }

    } catch (erro) {

        console.error(erro);

        alert("Erro ao salvar.");

    }

});