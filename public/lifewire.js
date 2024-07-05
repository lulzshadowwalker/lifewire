document.querySelectorAll('[wire\\:snapshot]').forEach(el => {
    el.__lifewire = JSON.parse(el.getAttribute('wire:snapshot'));
    initWireClick(el);
    initWireModel(el);
});

function initWireClick(el) {
    el.addEventListener('click', e => {
        if (!e.target.hasAttribute('wire:click')) return;

        const action = e.target.getAttribute('wire:click');
        sendRequest(el, { action });
    });
}

function initWireModel(el) {
    updateWireModelInputs(el);

    el.addEventListener('input', e => {
        if (!e.target.hasAttribute('wire:model')) return;

        const property = e.target.getAttribute('wire:model');
        const value = e.target.value;
        sendRequest(el, { update: [property, value] });
    });
}
function updateWireModelInputs(el) {
    const data = el.__lifewire.data;
    el.querySelectorAll('[wire\\:model]').forEach(input => {
        const property = input.getAttribute('wire:model');
        input.value = data[property];
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
    updateWireModelInputs(el);
}
