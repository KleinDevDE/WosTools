import axios from 'axios';

const api = axios.create({
  baseURL: '/api/v1/puzzles',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
  withXSRFToken: true,
});

// Initialize CSRF protection once on app start
export async function initializeSanctumAuth() {
  try {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
  } catch (error) {
    console.error('Failed to initialize Sanctum CSRF protection:', error);
    throw error;
  }
}

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if (error.response?.status === 401) {
      window.location.href = '/login';
      return Promise.reject(error);
    }

    // CSRF token expired - retry once
    if (error.response?.status === 419 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        await initializeSanctumAuth();
        return api.request(originalRequest);
      } catch {
        // If CSRF refresh fails, reload the page
        window.location.reload();
        return Promise.reject(error);
      }
    }

    return Promise.reject(error);
  }
);

export default api;
