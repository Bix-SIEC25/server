

// create an id name from name
function makeId(name, prefix = '') {
    if (!name && name !== 0) return prefix + Math.random().toString(36).slice(2, 8);
    return prefix + String(name)
        .replace(/\s+/g, '')
        .replace(/[^A-Za-z0-9_-]/g, '')
        .slice(0, 60) || (prefix + 'item'); // cap length
}

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
            // try some fallback
            try {
                const fixed = strOrObj
                    .replace(/'([^']*?)'/g, function (_, inner) {
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

    const parsed = tolerantParse(input);
    if (!Array.isArray(parsed)) {
        console.error('loadScenario: parsed input is not an array.');
        return;
    }

    scenario = parsed;

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

    for (const number in scenario) {
        const state = scenario[number];
        if (number == 0)
            scenario[number]["state"] = "next";
        else
            scenario[number]["state"] = "todo";
    }
}

function markStep(step) {
    if (!document.getElementById(makeId(step,"step-"))) return console.error(`${step} doesn't exist!`);
    for (let number = 0; number < scenario.length; number++) {
        const state = scenario[number];

        if (state.step) {
            if (state.step == step) {
                if (document.getElementById(makeId(step,"step-")).classList.contains("notdone")) {
                    document.getElementById(makeId(step,"step-")).classList.remove("notdone");
                    document.getElementById(makeId(step,"step-")).classList.add('done');
                    scenario[number]["state"] = "done";
                    return;
                }
                if (document.getElementById(makeId(step,"step-")).classList.contains("inprogress"))
                    document.getElementById(makeId(step,"step-")).classList.remove("inprogress");
                if (document.getElementById(makeId(step,"step-")).classList.contains("next"))
                    document.getElementById(makeId(step,"step-")).classList.remove("next");
                document.getElementById(makeId(step,"step-")).classList.add('done');
                scenario[number]["state"] = "done";

                // if there are more steps
                if (scenario.length > number + 2) {
                    scenario[number+1]["state"] = "inprogress";
                    scenario[number+2]["state"] = "next";
                    if (document.getElementById(makeId(scenario[number+1].step,"trans-")))
                        document.getElementById(makeId(scenario[number+1].step,"trans-")).classList.add("inprogress");
                    if (document.getElementById(makeId(scenario[number+2].step,"step-")))
                        document.getElementById(makeId(scenario[number+2].step,"step-")).classList.add("next");
                }
                return;
            } else {
                if (document.getElementById(makeId(state.step,"step-")).classList.contains("done") || document.getElementById(makeId(state.step,"step-")).classList.contains("notdone")) continue;
                else if (document.getElementById(makeId(state.step,"step-")).classList.contains("next")) {
                    document.getElementById(makeId(state.step,"step-")).classList.remove("next");
                    document.getElementById(makeId(state.step,"step-")).classList.add('done');
                    scenario[number]["state"] = "done";
                } else {
                    document.getElementById(makeId(state.step,"step-")).classList.add('notdone');
                    scenario[number]["state"] = "notdone";
                }
            }
        } else if (state.transition) {
            if (document.getElementById(makeId(state.transition,"trans-")).classList.contains("inprogress"))
                document.getElementById(makeId(state.transition,"trans-")).classList.remove("inprogress");
            document.getElementById(makeId(state.transition,"trans-")).classList.add('done');
            scenario[number]["state"] = "done";
        }
    }
}