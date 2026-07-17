import { api } from '@/lib/api'

export default {
    conversations() {
        return api.get('/messages')
    },
    conversation(userId, missionId = null) {
        return api.get(`/messages/conversation/${userId}`, {
            params: missionId ? { mission_id: missionId } : {},
        })
    },
    send(data) {
        return api.post('/messages', data)
    },
}
