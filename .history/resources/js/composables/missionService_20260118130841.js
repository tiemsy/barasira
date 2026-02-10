// import axios from "axios"
import { api } from '@/lib/api'

// const API = `${import.meta.env.VITE_API_URL}/api/missions`

export default {
  get(params = {}) {
    return api.get('/missions', { params })
  },
  create(data) {
    return api.post('/missions', data)
  },
  update(id, data) {
    return api.put(`'/missions'/${id}`, data)
  },
  remove(id) {
    return api.delete(`'/missions'/${id}`)
  }
}
