import { api } from '@/lib/api'

export default {
    create(data) {
        return api.post('/reviews', data)
    },
    update(reviewId, data) {
        return api.patch(`/reviews/${reviewId}`, data)
    },
}
