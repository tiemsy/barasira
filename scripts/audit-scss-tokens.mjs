import fs from 'node:fs'
import path from 'node:path'

const root = path.resolve('resources/scss')
const variablesDirectory = path.join(root, 'variables')
const violations = []

function audit(directory) {
    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
        const file = path.join(directory, entry.name)

        if (entry.isDirectory()) {
            if (file !== variablesDirectory) audit(file)
            continue
        }

        if (! entry.name.endsWith('.scss') || entry.name === 'app.scss') continue

        const content = fs.readFileSync(file, 'utf8')
        const lines = content.split('\n')

        lines.forEach((line, index) => {
            if (/#[\da-f]{3,8}\b|rgba?\([^()]+\)|(?<![\w$-])(?:white|black|transparent|currentColor)\b/i.test(line)) {
                violations.push(`${path.relative(process.cwd(), file)}:${index + 1}: couleur codée en dur`)
            }

            const properties = line.matchAll(/(?:margin|padding|gap|row-gap|column-gap|inset)(?:-[a-z-]+)?\s*:\s*([^;]+);/gi)

            for (const property of properties) {
                if (/(?<![\w.-])-?(?:\d*\.)?\d+(?:px|rem|em|%|vh|vw)(?![\w-])|(?<![\w-])0(?![\w-])/i.test(property[1])) {
                    violations.push(`${path.relative(process.cwd(), file)}:${index + 1}: espacement codé en dur`)
                }
            }
        })
    }
}

audit(root)

if (violations.length) {
    console.error(violations.join('\n'))
    process.exit(1)
}

console.log('Audit SCSS réussi : aucune couleur ni aucun espacement codé en dur.')
