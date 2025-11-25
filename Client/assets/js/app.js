import { initLoginView } from './views/loginView.js';
import { initProductListView, loadProducts } from './views/productListView.js';

document.addEventListener('DOMContentLoaded', () => {
  initLoginView({
    formSelector: '#api-login-form',
    messageSelector: '#api-login-message',
    onSuccess: () => {
      document.querySelector('#product-section')?.classList.remove('d-none');
      loadProducts();
    },
  });

  initProductListView({
    containerSelector: '#product-list',
  });
});
