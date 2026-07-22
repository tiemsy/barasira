import { reactive } from 'vue'

const COOKIE_NAME = 'barasira_cookie_consent'
const CONSENT_VERSION = 1
const state = reactive({ ready: false, preferencesOpen: false, necessary: true, marketing: false })

function readConsent() {
    const value = document.cookie.split('; ').find(item => item.startsWith(`${COOKIE_NAME}=`))?.split('=').slice(1).join('=')
    if (!value) return null
    try {
        const consent = JSON.parse(decodeURIComponent(value))
        return consent.version === CONSENT_VERSION ? consent : null
    } catch {
        return null
    }
}

function writeConsent(marketing) {
    const secure = window.location.protocol === 'https:' ? '; Secure' : ''
    const value = encodeURIComponent(JSON.stringify({ version: CONSENT_VERSION, necessary: true, marketing, savedAt: new Date().toISOString() }))
    document.cookie = `${COOKIE_NAME}=${value}; Path=/; Max-Age=15552000; SameSite=Lax${secure}`
}

function removeMarketingCookies() {
    ['_ga', '_gid', '_gat', '_fbp', '_fbc'].forEach(name => {
        document.cookie = `${name}=; Path=/; Max-Age=0; SameSite=Lax`
    })
}

function loadMarketingScripts() {
    const googleId = import.meta.env.VITE_GOOGLE_ANALYTICS_ID
    if (googleId && !document.querySelector('[data-barasira-google-analytics]')) {
        const script = document.createElement('script')
        script.async = true
        script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(googleId)}`
        script.dataset.barasiraGoogleAnalytics = 'true'
        document.head.appendChild(script)
        window.dataLayer = window.dataLayer || []
        window.gtag = window.gtag || function () { window.dataLayer.push(arguments) }
        window.gtag('js', new Date())
        window.gtag('config', googleId, { anonymize_ip: true })
    }

    const pixelId = import.meta.env.VITE_META_PIXEL_ID
    if (pixelId && !window.fbq) {
        const fbq = window.fbq = function () { fbq.callMethod ? fbq.callMethod.apply(fbq, arguments) : fbq.queue.push(arguments) }
        fbq.queue = []
        fbq.loaded = true
        fbq.version = '2.0'
        const script = document.createElement('script')
        script.async = true
        script.src = 'https://connect.facebook.net/en_US/fbevents.js'
        script.dataset.barasiraMetaPixel = 'true'
        document.head.appendChild(script)
        fbq('init', pixelId)
        fbq('track', 'PageView')
    }
}

function save(marketing) {
    state.marketing = Boolean(marketing)
    state.preferencesOpen = false
    writeConsent(state.marketing)
    if (state.marketing) loadMarketingScripts()
    else removeMarketingCookies()
}

function initialize() {
    if (state.ready || typeof document === 'undefined') return
    const consent = readConsent()
    state.ready = true
    if (consent) {
        state.marketing = Boolean(consent.marketing)
        if (state.marketing) loadMarketingScripts()
    }
}

export function useCookieConsent() {
    return {
        state,
        initialize,
        acceptAll: () => save(true),
        rejectOptional: () => save(false),
        savePreferences: marketing => save(marketing),
        openPreferences: () => { state.preferencesOpen = true },
        closePreferences: () => { state.preferencesOpen = false },
        hasChoice: () => Boolean(readConsent()),
    }
}
