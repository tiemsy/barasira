export function capitalize(value) {
    if (!value) {
        return ''
    }

    return value
        .toLowerCase()
        .replace(/\b\p{L}/gu, letter => letter.toUpperCase())
}

export function capitalizeFirst(value) {
    if (!value) {
        return ''
    }

    return value.charAt(0).toUpperCase() + value.slice(1)
}

export function fullName(firstname, lastname) {
    return `${capitalize(firstname)} ${capitalize(lastname)}`.trim()
}
