const API_BASE_URL = '/api/index.php';
const TOKEN_KEY = 'ecoprima_token';

export const httpClient = {
  setToken(token) {
    window.localStorage.setItem(TOKEN_KEY, token);
  },
  getToken() {
    return window.localStorage.getItem(TOKEN_KEY);
  },
  clearToken() {
    window.localStorage.removeItem(TOKEN_KEY);
  },
  async request(path, options = {}) {
    const headers = options.headers || {};
    headers['Content-Type'] = 'application/json';

    const token = this.getToken();
    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(`${API_BASE_URL}${path}`, {
      ...options,
      headers,
    });

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      throw new Error(data.error || 'Error desconocido');
    }

    return data;
  },
};
