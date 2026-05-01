import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.withCredentials = false; // Bearer token based auth

// Set base URL for API requests
const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';
window.axios.defaults.baseURL = baseUrl + '/api';

// Attach Bearer token if available (for Steps 2-5)
const token = localStorage.getItem('auth_token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

