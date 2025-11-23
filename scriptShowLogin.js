document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("btnTestAccounts");
    const box = document.getElementById("testAccountsBox");

    if (!btn || !box) {
        console.error("IDs não encontrados no HTML");
        return;
    }

    let timeout;

    btn.addEventListener("click", () => {
        clearTimeout(timeout);

        box.classList.add("show");

        timeout = setTimeout(() => {
            box.classList.remove("show");
        }, 4000);
    });
});