function esperarBotao() {
    const btn = document.getElementsByClassName("btn-teste")[0]; // sem o ponto
    if (btn) {
        console.log("Botão encontrado:", btn);
        const inputValue = document.getElementsByClassName("quantidade-number")[0];

        btn.addEventListener("click", (event) => {
            window.location.href = `/sorteio/cadastro.html?numbers=${inputValue.placeholder}`
        });

        return;
    }

    setTimeout(esperarBotao, 500);
}

esperarBotao();