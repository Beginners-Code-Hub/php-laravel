@extends('layout')
@section('title', 'Sell')

@section('content')
<h1>Sell</h1>

<div class="pos">
    <section class="card">
        <label>Scan or type a barcode, then press Enter
            <input id="barcode" class="scan" autofocus autocomplete="off" placeholder="480000001000">
        </label>
        <div id="msg" class="flash" hidden></div>
        <table id="cart">
            <thead><tr><th>Medicine</th><th class="num">Price</th><th class="num">Qty</th>
                <th class="num">Line</th><th></th></tr></thead>
            <tbody></tbody>
        </table>
    </section>

    <aside class="card pay">
        <div class="big"><small>Total</small><span id="total">0.00</span></div>
        <label>Cash received
            <input id="paid" type="number" step="0.01" min="0">
        </label>
        <div class="change">Change: <b id="change">0.00</b></div>
        <button id="checkout" class="btn" disabled>Complete sale</button>
    </aside>
</div>
@endsection

@push('scripts')
<script>
const cart = [];
const $ = (id) => document.getElementById(id);
const token = document.querySelector('meta[name=csrf-token]').content;

function say(text, bad = true) {
    $('msg').hidden = !text;
    $('msg').textContent = text;
    $('msg').className = 'flash ' + (bad ? 'bad' : 'ok');
}

function render() {
    const body = document.querySelector('#cart tbody');
    body.innerHTML = '';
    cart.forEach((line, i) => {
        const row = body.insertRow();
        row.innerHTML = `<td>${line.name}<br><span class="muted small">exp ${line.expiry}</span></td>
            <td class="num">${line.price.toFixed(2)}</td>
            <td class="num">${line.qty}</td>
            <td class="num">${(line.price * line.qty).toFixed(2)}</td>
            <td><button class="link bad" data-i="${i}">✕</button></td>`;
    });
    const total = cart.reduce((sum, l) => sum + l.price * l.qty, 0);
    $('total').textContent = total.toFixed(2);
    const paid = parseFloat($('paid').value) || 0;
    $('change').textContent = Math.max(paid - total, 0).toFixed(2);
    $('checkout').disabled = cart.length === 0 || paid < total;
}

$('barcode').addEventListener('keydown', async (e) => {
    if (e.key !== 'Enter') return;
    const code = e.target.value.trim();
    e.target.value = '';
    if (!code) return;

    const res = await fetch('/pos/lookup/' + encodeURIComponent(code), {
        headers: { Accept: 'application/json' },
    });
    const data = await res.json();
    if (!res.ok) return say(data.error);

    say('');
    const line = cart.find((l) => l.id === data.id);
    if (line) {
        if (line.qty >= data.stock) return say(`Only ${data.stock} of ${data.name} in stock.`);
        line.qty++;
    } else {
        cart.push({ ...data, qty: 1 });
    }
    render();
});

$('cart').addEventListener('click', (e) => {
    if (e.target.dataset.i) { cart.splice(e.target.dataset.i, 1); render(); }
});
$('paid').addEventListener('input', render);

$('checkout').addEventListener('click', async () => {
    const res = await fetch('/pos/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({
            items: cart.map((l) => ({ id: l.id, qty: l.qty })),
            paid: $('paid').value,
        }),
    });
    const data = await res.json();
    if (!res.ok) return say(data.message || 'Could not complete the sale.');
    location.href = data.receipt;
});
</script>
@endpush
