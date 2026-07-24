// import axios from "axios"
import { api } from '@/lib/api'

export default {
    get(params = {}) {
        return api.get('/missions', { params })
    },
    show(id) {
        return api.get(`/missions/${id}`)
    },
    create(data) {
        return api.post('/missions', data)
    },
    update(id, data) {
        return api.patch(`/missions/${id}`, data)
    },
    apply(id, data = {}) {
        return api.post(`/missions/${id}/applications`, data)
    },
    acceptApplication(missionId, applicationId) {
        return api.post(`/missions/${missionId}/applications/${applicationId}/accept`)
    },
    remove(id) {
        return api.delete(`/missions/${id}`)
    }
}
