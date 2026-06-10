const form = document.getElementById("pressaoForm");

form.addEventListener("submit", async (e) => {

    e.preventDefault();

    const idUsuarioRegistro =
        document.getElementById("idUsuarioRegistro").value;

    const sistolica =
        document.getElementById("sistolica").value;

    const diastolica =
        document.getElementById("diastolica").value;

    const data =
        document.getElementById("data").value;

    const hora =
        document.getElementById("hora").value;

    const observacoes =
        document.getElementById("observacoes").value;

    try {

        const response = await fetch(
            "php/usuario/registros/salvarPressao.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                body: new URLSearchParams({
                    idUsuarioRegistro,
                    sistolica,
                    diastolica,
                    data,
                    hora,
                    observacoes
                })
            }
        );

        const resultado = await response.text();

        if (
            resultado.includes("sucesso")
        ) {

            alert("Pressão registrada com sucesso!");

            form.reset();

        } else {

            alert(resultado);

        }

    } catch (erro) {

        console.error(erro);

        alert("Erro ao salvar.");

    }

});