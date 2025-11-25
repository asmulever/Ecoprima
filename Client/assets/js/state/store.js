const listeners = new Set();
const state = {
  user: null,
  products: [],
};

export const store = {
  getState() {
    return { ...state };
  },
  setUser(user) {
    state.user = user;
    notify();
  },
  setProducts(products) {
    state.products = products;
    notify();
  },
  subscribe(callback) {
    listeners.add(callback);
    callback(store.getState());
    return () => listeners.delete(callback);
  },
};

function notify() {
  listeners.forEach((cb) => cb(store.getState()));
}
