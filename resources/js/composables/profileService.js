import { api } from '@/lib/api'

export default {
    update(userId, data) {
        const payload = new FormData()

        Object.entries(data).forEach(([key, value]) => {
            if (value !== null && value !== undefined) payload.append(key, value)
        })
        payload.append('_method', 'PATCH')

        return api.post(`/users/${userId}`, payload)
    },
}
