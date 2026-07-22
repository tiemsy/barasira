const iconAliases = { water:'plumbing',bolt:'electrical',scissors:'tailoring',truck:'transport',leaf:'gardening',broom:'cleaning',laptop:'computer',building:'building','shield-halved':'shield','helmet-safety':'construction',spa:'beauty',utensils:'food',motorcycle:'mechanic','solar-panel':'solar','wheat-awn':'agriculture',snowflake:'cooling',tools:'tools' }
const categoryAliases = { plomberie:'plumbing',electricite:'electrical','électricité':'electrical',couture:'tailoring',transport:'transport',jardinage:'gardening',menage:'cleaning','ménage':'cleaning',informatique:'computer',batiment:'building','bâtiment':'building',gardiennage:'shield',coiffure:'beauty',restauration:'food',mecanique:'mechanic','mécanique':'mechanic',solaire:'solar',agriculture:'agriculture',froid:'cooling' }

export function serviceIconName(service) {
    const stored = String(service?.icon || service?.category?.icon || '').toLowerCase()
    const faName = stored.match(/fa-([a-z0-9-]+)$/)?.[1]
    if (faName && iconAliases[faName]) return iconAliases[faName]
    if (stored && !stored.includes('fa-') && !stored.includes('bi-')) return stored
    const category = String(service?.category?.name || service?.name || '').toLowerCase()
    return Object.entries(categoryAliases).find(([key]) => category.includes(key))?.[1] || 'tools'
}
