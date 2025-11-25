import { productService } from '../services/productService.js';
import { store } from '../state/store.js';

let containerRef = null;

export function initProductListView({ containerSelector }) {
  const container = document.querySelector(containerSelector);
  if (!container) {
    return;
  }

  containerRef = container;

  store.subscribe((state) => {
    if (!state.products.length) {
      container.innerHTML = '<p class="text-muted">No hay productos cargados.</p>';
      return;
    }

    container.innerHTML = state.products
      .map(
        (product) => `
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">${product.nombre}</h5>
            <p class="card-text">
              ${product.descripcion || 'Sin descripción'}<br>
              Precio: $${product.precio}<br>
              Ubicación: ${product.ubicacion}<br>
              Contacto: ${product.email}
            </p>
          </div>
        </div>
      `
      )
      .join('');
  });
}

export function loadProducts() {
  if (!containerRef) {
    return;
  }

  productService
    .list()
    .then((response) => store.setProducts(response.data))
    .catch(() => {
      containerRef.innerHTML = '<p class="text-danger">No se pudieron cargar los productos.</p>';
    });
}
