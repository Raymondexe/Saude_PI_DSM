document.addEventListener('DOMContentLoaded', () => {
  const feedbackButtons = document.querySelectorAll('.btn-feedback');
  const alertContainer = document.getElementById('alert-container');

  feedbackButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const resposta = btn.getAttribute('data-feedback');

      // Cria o alerta
      const alert = document.createElement('div');
      alert.classList.add('alert-feedback', resposta);
      alert.textContent = `Obrigado pelo seu feedback: ${resposta.toUpperCase()}`;

      // Adiciona ao container
      alertContainer.appendChild(alert);

      // Remove após 3.5s
      setTimeout(() => {
        alert.remove();
      }, 3500);

      // Salva no LocalStorage (opcional)
      let feedbacks = JSON.parse(localStorage.getItem('feedbacks')) || [];
      feedbacks.push({ tipo: resposta, data: new Date().toISOString() });
      localStorage.setItem('feedbacks', JSON.stringify(feedbacks));
    });
  });
});