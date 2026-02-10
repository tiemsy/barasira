// import axios from "axios"
import { api } from '@/lib/api'

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
