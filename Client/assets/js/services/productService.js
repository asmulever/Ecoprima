import { httpClient } from '../core/httpClient.js';

export const productService = {
  list() {
    return httpClient.request('/v1/products', { method: 'GET' });
  },
};
