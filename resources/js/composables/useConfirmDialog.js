import { reactive } from 'vue'

const state = reactive({ open: false, title: '', message: '', confirmLabel: '', tone: 'danger' })
let resolver = null

function close(result) {
    state.open = false
    resolver?.(result)
    resolver = null
}

export function useConfirmDialog() {
    const confirm = options => {
        if (resolver) resolver(false)
        Object.assign(state, { open: true, tone: 'danger', ...options })

        return new Promise(resolve => { resolver = resolve })
    }

    return { state, confirm, accept: () => close(true), cancel: () => close(false) }
}
