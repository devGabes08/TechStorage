async function inserir() {
  var nome = document.getElementById("nome").value;
  var email = document.getElementById("email").value;
  var senha = document.getElementById("senha").value;
  const fd = new FormData();
  fd.append("nome", nome);
  fd.append("email", email);
  fd.append("senha", senha);

  const retorno = await fetch("../app/gerente_inserir.php", {
    method: "POST",
    body: fd,
  });
  const resposta = await retorno.json();

  if (resposta.ok === true) {
    window.location.href = "../gerente/index.html";
  }

  async function paginaInserir() {
    window.location.href = "../gerente/gerente_inserir.html";
  }
}
