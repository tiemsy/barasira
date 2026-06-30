import axios from 'axios'
const Missions = `/api/missions`
const Catagories = `/api/service-categories`
const api = axios.create({
  baseURL: `${import.meta.env.VITE_API_URL}/api`,
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
})

export { api }
