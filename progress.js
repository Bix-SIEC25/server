function loadScenario(input) {
    const container = document.getElementById('scenario-progress');
    if (!container) {
        console.error('loadScenario: #scenario-progress not found in DOM.');
        return;
    }

    function tolerantParse(strOrObj) {
        if (Array.isArray(strOrObj)) return strOrObj;
        if (typeof strOrObj !== 'string') {
            throw new Error('Unsupported input type for loadScenario.');
        }
        try {
            return JSON.parse(strOrObj);
        } catch (e) {
            // fallback: attempt to convert single-quoted object syntax to JSON-ish
            // NOTE: this is a best-effort fallback for user-provided pseudo-JSON like "[{'step':'a'}]".
            try {
                const fixed = strOrObj
                    // replace single quotes around keys/values with double quotes (best-effort)
                    .replace(/'([^']*?)'/g, function (_, inner) {
                        // Escape any existing double quotes inside inner
                        return '"' + inner.replace(/"/g, '\\"') + '"';
                    });
                return JSON.parse(fixed);
            } catch (e2) {
                console.error('loadScenario: failed to parse input as JSON.', e2);
                return [];
            }
        }
    }

    function escapeHTML(s) {
        if (s === null || s === undefined) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Make an ID from a name: remove spaces and non-alphanumeric (keep - and _)
    function makeId(name, prefix = '') {
        if (!name && name !== 0) return prefix + Math.random().toString(36).slice(2, 8);
        return prefix + String(name)
            .replace(/\s+/g, '')
            .replace(/[^A-Za-z0-9_-]/g, '')
            .slice(0, 60) || (prefix + 'item'); // cap length
    }

    const parsed = tolerantParse(input);
    if (!Array.isArray(parsed)) {
        console.error('loadScenario: parsed input is not an array.');
        return;
    }

    // Build safe HTML string
    let parts = [];
    parts.push('<div class="sp-inner" role="list">');

    parsed.forEach((item, idx) => {
        if (item && typeof item === 'object' && 'step' in item) {
            const name = String(item.step);
            const icon = item.icon != null ? String(item.icon) : '';
            const id = makeId(name, 'step-');
            const escName = escapeHTML(name);
            const escIcon = escapeHTML(icon);

            // Circle content: show icon (if any) above small label. Use title attribute for full label.
            parts.push(
                `<div id="${id}" class="sp-step step" role="listitem" aria-label="${escName}" title="${escName}">` +
                `<div class="sp-step-circle">` +
                (escIcon ? `<div class="sp-icon">${escIcon}</div>` : '') +
                `<div class="sp-step-label">${escName}</div>` +
                `</div>` +
                `</div>`
            );
        } else if (item && typeof item === 'object' && 'transition' in item) {
            const name = String(item.transition);
            const id = makeId(name, 'trans-');
            const escName = escapeHTML(name);

            // Transition bar with label centered along it
            parts.push(
                `<div id="${id}" class="sp-transition transition" role="separator" aria-label="${escName}" title="${escName}">` +
                `<div class="sp-transition-bar">` +
                `<span class="sp-transition-label">${escName}</span>` +
                `</div>` +
                `</div>`
            );
        } else {
            // Unknown entry: skip but keep a placeholder
            const id = makeId('unknown' + idx, 'unknown-');
            parts.push(
                `<div id="${id}" class="sp-unknown" role="listitem">` +
                `<div class="sp-step-circle"><div class="sp-step-label">unknown</div></div>` +
                `</div>`
            );
        }
    });

    parts.push('</div>');

    // Write innerHTML once (escaped content)
    container.innerHTML = parts.join('');
}
