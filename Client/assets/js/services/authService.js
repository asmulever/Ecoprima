import { httpClient } from '../core/httpClient.js';

export const authService = {
  async login(email, password) {
    const result = await httpClient.request('/v1/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });
    httpClient.setToken(result.token);
    return result;
  },
  async me() {
    return httpClient.request('/v1/auth/me', { method: 'GET' });
  },
  logout() {
    httpClient.clearToken();
  },
};
