let googleMapsPromise

export function loadGoogleMapsPlaces() {
    if (window.google?.maps?.places) {
        return Promise.resolve(window.google.maps.places)
    }

    const apiKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY

    if (!apiKey) {
        return Promise.reject(new Error('GOOGLE_MAPS_API_KEY_MISSING'))
    }

    if (googleMapsPromise) return googleMapsPromise

    googleMapsPromise = new Promise((resolve, reject) => {
        const callbackName = '__barasiraGoogleMapsReady'
        const existingScript = document.querySelector('script[data-barasira-google-maps]')

        window[callbackName] = () => {
            delete window[callbackName]
            resolve(window.google.maps.places)
        }

        if (existingScript) {
            existingScript.addEventListener('error', reject, { once: true })
            return
        }

        const script = document.createElement('script')
        const params = new URLSearchParams({
            key: apiKey,
            libraries: 'places',
            loading: 'async',
            callback: callbackName,
            language: document.documentElement.lang || 'fr',
            region: 'ML',
        })

        script.src = `https://maps.googleapis.com/maps/api/js?${params}`
        script.async = true
        script.defer = true
        script.dataset.barasiraGoogleMaps = 'true'
        script.onerror = () => {
            googleMapsPromise = undefined
            delete window[callbackName]
            reject(new Error('GOOGLE_MAPS_LOAD_FAILED'))
        }

        document.head.append(script)
    })

    return googleMapsPromise
}
