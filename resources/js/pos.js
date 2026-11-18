document.addEventListener('DOMContentLoaded', () => {
    const successDialog = document.getElementById('posSuccessDialog');
    if (successDialog) successDialog.showModal();
    const form = document.getElementById('posCheckoutForm');
    const overlay = document.getElementById('posCheckoutModalOverlay');
    const table = document.querySelector('#posOrderTable tbody');
    const summary = document.querySelector('#posCheckoutTable tbody');
    const token = form.querySelector('input[name="_token"]').value;
    const referenceGroup = document.getElementById('posGcashReferenceGroup');
    const reference = document.getElementById('posGcashReference');
    const customerSearch = document.getElementById('posCheckoutCustomerSearch');
    const customerResults = document.getElementById('posCheckoutCustomerSuggestions');
    const customerId = document.getElementById('posCheckoutSelectedCustomerId');
    const clearCustomer = document.getElementById('posCheckoutClearCustomer');
    let searchTimer;
    let searchController;
    let composing = false;

    function clearCustomerSelection() {
        clearTimeout(searchTimer);
        searchController?.abort();
        customerSearch.value = '';
        customerId.value = '';
        customerResults.replaceChildren();
        customerResults.hidden = true;
        customerSearch.setAttribute('aria-expanded', 'false');
        clearCustomer.hidden = true;
        customerSearch.focus();
    }

    function scheduleCustomerSearch() {
        clearTimeout(searchTimer);
        searchController?.abort();
        customerId.value = '';
        const query = customerSearch.value.trim();
        clearCustomer.hidden = !query;
        customerResults.replaceChildren();
        customerResults.hidden = true;
        customerSearch.setAttribute('aria-expanded', 'false');
        if (!query || composing) return;
        // Cancel stale searches before showing newer results
        searchTimer = setTimeout(async () => {
            searchController = new AbortController();
            try {
                const response = await fetch('/pos/customers/search?' + new URLSearchParams({ q: query }), {
                    headers: { Accept: 'application/json' },
                    signal: searchController.signal,
                });
                if (!response.ok) throw new Error('Customer search failed.');
                const customers = await response.json();
                customerResults.replaceChildren();
                if (!customers.length) customerResults.textContent = 'No customers found.';
                customers.forEach((customer) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'posCheckoutCustomerSuggestion';
                    button.setAttribute('role', 'option');
                    button.textContent = customer.phone ? customer.name + ' — ' + customer.phone : customer.name;
                    button.addEventListener('click', () => {
                        customerId.value = customer.id;
                        customerSearch.value = customer.name;
                        customerResults.hidden = true;
                        customerSearch.setAttribute('aria-expanded', 'false');
                        clearCustomer.hidden = false;
                    });
                    customerResults.append(button);
                });
                customerResults.hidden = false;
                customerSearch.setAttribute('aria-expanded', 'true');
            } catch (error) {
                if (error.name !== 'AbortError') {
                    customerResults.textContent = 'Customer search failed. Try again.';
                    customerResults.hidden = false;
                }
            }
        }, 300);
    }
    customerSearch.maxLength = 100;
    customerSearch.addEventListener('compositionstart', () => { composing = true; });
    customerSearch.addEventListener('compositionend', () => {
        composing = false;
        scheduleCustomerSearch();
    });
    customerSearch.addEventListener('input', scheduleCustomerSearch);
    clearCustomer.addEventListener('click', clearCustomerSelection);
    function updatePaymentFields() {
        const method = form.querySelector('input[name="payment_method"]:checked').value;
        const split = method === 'split';
        referenceGroup.hidden = method === 'cash';
        reference.required = method !== 'cash';
        document.getElementById('posSplitPaymentGroup').hidden = !split;
        document.getElementById('posCashAmount').required = split;
        document.getElementById('posGcashAmount').required = split;
        document.getElementById('posCheckoutAmountGroup').hidden = split;
        document.getElementById('posCheckoutAmountReceived').required = !split;
    }
    form.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
        radio.addEventListener('change', updatePaymentFields);
    });
    updatePaymentFields();
    if (overlay.dataset.hasErrors === 'true') overlay.hidden = false;
    const status = document.createElement('p');
    status.setAttribute('role', 'status');
    document.getElementById('posOrderHeader').after(status);
    let current = { items: [] };

    async function request(url, method = 'GET', body) {
        const response = await fetch(url, {
            method,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: body ? JSON.stringify(body) : undefined,
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Could not update the order.');
        render(data);
    }

    function render(cart) {
        current = cart;
        table.replaceChildren();
        summary.replaceChildren();
        if (!cart.items.length) {
            const row = table.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 5;
            cell.textContent = 'No items in the order.';
        }
        cart.items.forEach((item) => {
            const row = table.insertRow();
            [item.name, String(item.quantity), item.price, item.subtotal].forEach((value) => {
                row.insertCell().textContent = value;
            });
            const actions = row.insertCell();
            [['−', item.quantity - 1], ['+', item.quantity + 1], ['Remove', 0]].forEach(([label, quantity]) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.className = label === 'Remove' ? 'posCartRemoveButton' : 'posCartQuantityButton';
                const actionLabel = label === 'Remove'
                    ? 'Remove ' + item.name
                    : (label === '+' ? 'Increase ' : 'Decrease ') + item.name + ' quantity';
                button.setAttribute('aria-label', actionLabel);
                button.disabled = label === '−' && item.quantity <= 1;
                button.addEventListener('click', () => change(item.id, quantity));
                actions.append(button);
            });
            const checkoutRow = summary.insertRow();
            [item.name, String(item.quantity), item.price, item.subtotal].forEach((value) => {
                checkoutRow.insertCell().textContent = value;
            });
        });
        document.querySelector('#posOrderSummary .posOrderSummaryRow span:last-child').textContent = cart.subtotal;
        document.getElementById('posDiscountAmount').textContent = cart.discount;
        document.querySelector('#posOrderTotal strong:last-child').textContent = cart.total;
        document.getElementById('posCheckoutTotalAmount').textContent = cart.total;
    }

    async function change(id, quantity) {
        try {
            await request(
                '/pos/cart/' + encodeURIComponent(id),
                quantity ? 'PUT' : 'DELETE',
                quantity ? { quantity } : undefined,
            );
            status.textContent = '';
        } catch (error) {
            status.textContent = error.message;
        }
    }

    document.querySelectorAll('.posAddProductButton').forEach((button) => {
        button.addEventListener('click', () => {
            const id = Number(button.dataset.productId);
            const item = current.items.find((entry) => entry.id === id);
            change(id, (item?.quantity || 0) + 1);
        });
    });
    document.getElementById('posClearOrderButton').addEventListener('click', () => {
        request('/pos/cart', 'DELETE').catch((error) => { status.textContent = error.message; });
    });
    document.getElementById('posApplyDiscount')?.addEventListener('click', () => {
        request('/pos/cart/discount', 'PUT', {
            type: document.getElementById('posDiscountType').value,
            value: document.getElementById('posDiscountInput').value,
        })
            .then(() => { status.textContent = ''; })
            .catch((error) => { status.textContent = error.message; });
    });
    document.getElementById('posCompleteSaleButton').addEventListener('click', () => {
        if (!current.items.length) { status.textContent = 'Add at least one product.'; return; }
        overlay.hidden = false;
    });
    document.getElementById('posCheckoutModalClose').addEventListener('click', () => { overlay.hidden = true; });
    document.getElementById('posCheckoutCancelButton').addEventListener('click', () => { overlay.hidden = true; });
    form.addEventListener('submit', () => {
        form.querySelectorAll('[data-cart-item]').forEach((input) => input.remove());
        current.items.forEach((item, index) => {
            Object.entries({ product_id: item.id, quantity: item.quantity }).forEach(([field, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `items[${index}][${field}]`;
                input.value = value;
                input.dataset.cartItem = 'true';
                form.append(input);
            });
        });
    });
    request('/pos/cart').catch((error) => { status.textContent = error.message; });
});
