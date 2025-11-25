import { authService } from '../services/authService.js';
import { store } from '../state/store.js';

export function initLoginView({ formSelector, messageSelector, onSuccess }) {
  const form = document.querySelector(formSelector);
  const messageBox = document.querySelector(messageSelector);

  if (!form) {
    return;
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    messageBox.textContent = 'Autenticando...';
    const formData = new FormData(form);
    const email = formData.get('email');
    const password = formData.get('password');

    try {
      const result = await authService.login(email, password);
      store.setUser(result.user);
      messageBox.textContent = 'Ingreso exitoso.';
      if (typeof onSuccess === 'function') {
        onSuccess(result);
      }
    } catch (error) {
      messageBox.textContent = error.message;
    }
  });
}
