import axios from 'axios'
const API = `/api/missions`
const api = axios.create({
  baseURL: `${import.meta.env.VITE_API_URL}/api`,
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
})

export { api }
