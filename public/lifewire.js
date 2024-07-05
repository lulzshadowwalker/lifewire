document.querySelectorAll('[wire\\:snapshot]').forEach(el => {
    const attr = el.getAttribute('wire:snapshot');
    const snapshot = JSON.parse(attr);
    el.__lifewire = snapshot;
    initWireClick(el);
});

function initWireClick(el) {
    el.addEventListener('click', e => {
        const action = e.target.getAttribute('wire:click');
        if (!action) return;

        sendRequest(el, { action });
    });
}

async function sendRequest(el, args) {
    const { html, snapshot } = await fetch('/lifewire', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ snapshot: el.__lifewire, ...args }),
    }).then(res => res.json());

    el.__lifewire = snapshot;
    Alpine.morph(el.firstElementChild, html);
}
