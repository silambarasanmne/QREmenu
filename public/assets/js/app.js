/* Gourmet QR Menu Real-time Polling & UI Engine */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Toast Notification System
    window.showToast = function(message, type = 'info') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        const icon = type === 'success' ? 'circle-check' : (type === 'error' ? 'circle-xmark' : 'bell');
        toast.innerHTML = `<i class="fa-solid fa-${icon}"></i> <span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    };

    // Audio Chime Manager
    let soundEnabled = true;
    const audioBtn = document.getElementById('btn-toggle-audio');
    if (audioBtn) {
        audioBtn.addEventListener('click', () => {
            soundEnabled = !soundEnabled;
            audioBtn.innerHTML = soundEnabled ? '<i class="fa-solid fa-volume-high"></i> Sound On' : '<i class="fa-solid fa-volume-xmark"></i> Sound Off';
        });
    }

    function playNotificationSound() {
        if (!soundEnabled) return;
        const sound = document.getElementById('notification-sound');
        if (sound) {
            sound.currentTime = 0;
            sound.play().catch(() => {});
        }
    }

    // -------------------------------------------------------------
    // 2. Customer Mobile OTP Verification Logic
    // -------------------------------------------------------------
    const formSendOtp = document.getElementById('form-send-otp');
    const formVerifyOtp = document.getElementById('form-verify-otp');
    const btnSendOtp = document.getElementById('btn-send-otp');
    const btnVerifyOtp = document.getElementById('btn-verify-otp');
    let userMobileNumber = '';

    if (formSendOtp) {
        formSendOtp.addEventListener('submit', async (e) => {
            e.preventDefault();
            const mobileInput = document.getElementById('mobile-number-input').value.trim();

            if (mobileInput.length < 10) {
                showToast('Please enter a valid 10-digit mobile number', 'error');
                return;
            }

            btnSendOtp.disabled = true;
            btnSendOtp.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending OTP...';

            try {
                const res = await fetch('/api/customer/send-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mobile: mobileInput })
                });
                const data = await res.json();

                if (data.success) {
                    userMobileNumber = data.mobile;
                    showToast(data.message || 'OTP Sent successfully!', 'success');
                    formSendOtp.classList.add('hidden');
                    formVerifyOtp.classList.remove('hidden');
                    document.getElementById('otp-code-input').focus();
                    document.getElementById('otp-code-input').value = '1234'; // prefill demo OTP
                } else {
                    showToast(data.error || 'Failed to send OTP', 'error');
                }
            } catch (err) {
                showToast('Connection error sending OTP', 'error');
            } finally {
                btnSendOtp.disabled = false;
                btnSendOtp.innerHTML = 'Send OTP <i class="fa-solid fa-paper-plane"></i>';
            }
        });
    }

    if (formVerifyOtp) {
        formVerifyOtp.addEventListener('submit', async (e) => {
            e.preventDefault();
            const otpCode = document.getElementById('otp-code-input').value.trim();
            const selectedTableId = document.getElementById('select-table-id').value;

            if (!otpCode) {
                showToast('Please enter OTP code', 'error');
                return;
            }

            btnVerifyOtp.disabled = true;
            btnVerifyOtp.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';

            try {
                const res = await fetch('/api/customer/verify-otp', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        mobile: userMobileNumber || document.getElementById('mobile-number-input').value,
                        otp: otpCode,
                        table_id: selectedTableId
                    })
                });
                const data = await res.json();

                if (data.success) {
                    showToast('OTP verified! Opening eMenu...', 'success');
                    document.getElementById('customer-otp-modal').classList.add('hidden');
                    window.location.href = `/table/${selectedTableId}`;
                } else {
                    showToast(data.error || 'Invalid OTP', 'error');
                }
            } catch (err) {
                showToast('Connection error verifying OTP', 'error');
            } finally {
                btnVerifyOtp.disabled = false;
                btnVerifyOtp.innerHTML = '<i class="fa-solid fa-circle-check"></i> Verify OTP & View Menu';
            }
        });
    }

    // -------------------------------------------------------------
    // 3. Customer Page Logic (/table/{table_id})
    // -------------------------------------------------------------
    if (window.TABLE_ID) {
        let cart = {}; // { dishId: { id, name, price, quantity, notes } }

        // Category filter tabs
        const categoryTabs = document.querySelectorAll('.category-tab');
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                categoryTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const selectedCat = tab.getAttribute('data-category');
                const sections = document.querySelectorAll('.category-section');
                sections.forEach(sec => {
                    if (selectedCat === 'all' || sec.getAttribute('data-cat-section') === selectedCat) {
                        sec.style.display = 'block';
                    } else {
                        sec.style.display = 'none';
                    }
                });
            });
        });

        // Quick Action Grid Handlers
        const infoModal = document.getElementById('info-modal');
        const infoModalTitle = document.getElementById('info-modal-title');
        const infoModalBody = document.getElementById('info-modal-body');
        const closeInfoModalBtn = document.getElementById('btn-close-info-modal');

        if (closeInfoModalBtn) {
            closeInfoModalBtn.addEventListener('click', () => {
                infoModal.classList.add('hidden');
            });
        }

        document.querySelectorAll('.action-card-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const action = e.currentTarget.getAttribute('data-action');
                if (action === 'menu') {
                    window.scrollTo({ top: document.querySelector('.category-tabs-wrapper').offsetTop - 80, behavior: 'smooth' });
                    return;
                }

                infoModal.classList.remove('hidden');
                switch (action) {
                    case 'today-special':
                        infoModalTitle.innerHTML = '<i class="fa-solid fa-star" style="color:var(--emenu-gold);"></i> Today Special Deal';
                        infoModalBody.innerHTML = `
                            <img src="https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?auto=format&fit=crop&w=400&q=80" style="width:100%; max-height:180px; object-fit:cover; border-radius:12px; margin-bottom:14px;">
                            <h3>Butter Chicken Special</h3>
                            <p style="color:var(--text-muted); margin: 8px 0 14px 0;">Rich, buttery tomato gravy with tender chicken tikka & fresh cream.</p>
                            <span class="badge badge-success" style="font-size:1rem;">Special Price: ₹340.00</span>
                        `;
                        break;
                    case 'chef-special':
                        infoModalTitle.innerHTML = '<i class="fa-solid fa-user-chef" style="color:var(--emenu-gold);"></i> Chef Recommended';
                        infoModalBody.innerHTML = `
                            <img src="https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=400&q=80" style="width:100%; max-height:180px; object-fit:cover; border-radius:12px; margin-bottom:14px;">
                            <h3>Classic Dum Biryani</h3>
                            <p style="color:var(--text-muted); margin: 8px 0 14px 0;">Slow-cooked fragrant Basmati rice with whole tandoori spices and fresh mint.</p>
                            <span class="badge badge-warning" style="font-size:1rem;">₹290.00</span>
                        `;
                        break;
                    case 'audio':
                        infoModalTitle.innerHTML = '<i class="fa-solid fa-headphones" style="color:var(--emenu-gold);"></i> Audio Guide';
                        infoModalBody.innerHTML = `
                            <div style="font-size:3rem; color:var(--emenu-gold); margin-bottom:12px;"><i class="fa-solid fa-circle-play"></i></div>
                            <h3>Audio Menu Description</h3>
                            <p style="color:var(--text-muted); margin-bottom:16px;">Listen to detailed descriptions of our authentic spices & chef recommended items.</p>
                            <button class="btn btn-primary" onclick="showToast('Playing audio description...', 'success')"><i class="fa-solid fa-play"></i> Play Audio Description</button>
                        `;
                        break;
                    case 'video':
                        infoModalTitle.innerHTML = '<i class="fa-solid fa-circle-play" style="color:var(--emenu-gold);"></i> Kitchen Tour';
                        infoModalBody.innerHTML = `
                            <img src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=400&q=80" style="width:100%; max-height:180px; object-fit:cover; border-radius:12px; margin-bottom:14px;">
                            <h3>Behind the Scenes</h3>
                            <p style="color:var(--text-muted);">Watch our master chefs craft your food with highest hygiene & fresh local ingredients.</p>
                        `;
                        break;
                    case 'about':
                        infoModalTitle.innerHTML = '<i class="fa-solid fa-circle-info" style="color:var(--emenu-gold);"></i> About Gourmet eMenu';
                        infoModalBody.innerHTML = `
                            <h3>Welcome to Gourmet Restaurant</h3>
                            <p style="color:var(--text-muted); margin-top:8px;">Experience touchless dining with instant real-time order tracking directly to kitchen and table service.</p>
                        `;
                        break;
                }
            });
        });

        // Quantity Plus/Minus buttons in dish cards
        document.querySelectorAll('.dish-card').forEach(card => {
            const dishId = card.getAttribute('data-dish-id');
            const name = card.getAttribute('data-name');
            const price = parseFloat(card.getAttribute('data-price'));

            const minusBtn = card.querySelector('.btn-minus');
            const plusBtn = card.querySelector('.btn-plus');
            const qtyCount = card.querySelector('.qty-count');
            const addBtn = card.querySelector('.btn-add-cart');

            function updateCardState() {
                const currentQty = cart[dishId] ? cart[dishId].quantity : 0;
                qtyCount.textContent = currentQty;
                minusBtn.disabled = currentQty <= 0;
                updateCartFloatingBar();
            }

            plusBtn.addEventListener('click', () => {
                if (!cart[dishId]) {
                    cart[dishId] = { id: dishId, name, price, quantity: 1, notes: '' };
                } else {
                    cart[dishId].quantity++;
                }
                updateCardState();
            });

            minusBtn.addEventListener('click', () => {
                if (cart[dishId]) {
                    cart[dishId].quantity--;
                    if (cart[dishId].quantity <= 0) {
                        delete cart[dishId];
                    }
                }
                updateCardState();
            });

            if (addBtn) {
                addBtn.addEventListener('click', () => {
                    if (!cart[dishId]) {
                        cart[dishId] = { id: dishId, name, price, quantity: 1, notes: '' };
                    } else {
                        cart[dishId].quantity++;
                    }
                    updateCardState();
                    showToast(`Added ${name} to cart`, 'success');
                });
            }
        });

        function updateCartFloatingBar() {
            const floatingBar = document.getElementById('cart-floating-bar');
            const itemCountEl = document.getElementById('cart-item-count');
            const totalPriceEl = document.getElementById('cart-total-price');

            let totalQty = 0;
            let totalPrice = 0;

            Object.values(cart).forEach(item => {
                totalQty += item.quantity;
                totalPrice += item.price * item.quantity;
            });

            if (totalQty > 0) {
                floatingBar.classList.remove('hidden');
                itemCountEl.textContent = `${totalQty} item${totalQty > 1 ? 's' : ''}`;
                totalPriceEl.textContent = `₹${totalPrice.toFixed(2)}`;
            } else {
                floatingBar.classList.add('hidden');
            }
        }

        // Cart Modal logic
        const cartModal = document.getElementById('cart-modal');
        const openCartBtn = document.getElementById('btn-open-cart');
        const closeCartBtn = document.getElementById('btn-close-cart');
        const cartContainer = document.getElementById('cart-items-container');
        const modalTotalEl = document.getElementById('modal-cart-total');

        if (openCartBtn) {
            openCartBtn.addEventListener('click', () => {
                renderCartModalItems();
                cartModal.classList.remove('hidden');
            });
        }

        if (closeCartBtn) {
            closeCartBtn.addEventListener('click', () => {
                cartModal.classList.add('hidden');
            });
        }

        function renderCartModalItems() {
            cartContainer.innerHTML = '';
            let total = 0;

            const items = Object.values(cart);
            if (items.length === 0) {
                cartContainer.innerHTML = '<p class="text-muted">Your cart is empty.</p>';
                modalTotalEl.textContent = '₹0.00';
                return;
            }

            items.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;

                const itemRow = document.createElement('div');
                itemRow.className = 'cart-item-card';
                itemRow.innerHTML = `
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">₹${item.price.toFixed(2)} x ${item.quantity} = ₹${subtotal.toFixed(2)}</div>
                        <input type="text" class="cart-item-note-input" placeholder="Special notes (e.g. extra spicy, medium spice)..." value="${item.notes}" data-dish-id="${item.id}">
                    </div>
                `;

                cartContainer.appendChild(itemRow);
            });

            modalTotalEl.textContent = `₹${total.toFixed(2)}`;

            // Listen to notes input changes
            document.querySelectorAll('.cart-item-note-input').forEach(input => {
                input.addEventListener('input', (e) => {
                    const id = e.target.getAttribute('data-dish-id');
                    if (cart[id]) {
                        cart[id].notes = e.target.value;
                    }
                });
            });
        }

        // Submit Order
        const placeOrderBtn = document.getElementById('btn-place-order');
        if (placeOrderBtn) {
            placeOrderBtn.addEventListener('click', async () => {
                const itemsList = Object.values(cart).map(i => ({
                    dish_id: i.id,
                    quantity: i.quantity,
                    notes: i.notes
                }));

                if (itemsList.length === 0) {
                    showToast('Cart is empty', 'error');
                    return;
                }

                placeOrderBtn.disabled = true;
                placeOrderBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

                try {
                    const res = await fetch(`/table/${window.TABLE_ID}/order`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ items: itemsList })
                    });
                    const data = await res.json();

                    if (data.success) {
                        showToast('Order placed successfully!', 'success');
                        cartModal.classList.add('hidden');
                        cart = {}; // reset cart
                        updateCartFloatingBar();
                        // Reset all card quantity badges
                        document.querySelectorAll('.qty-count').forEach(el => el.textContent = '0');
                        document.querySelectorAll('.btn-minus').forEach(el => el.disabled = true);
                        
                        window.CURRENT_ORDER_ID = data.order_id;
                        pollCustomerOrderStatus();
                    } else {
                        showToast(data.error || 'Failed to place order', 'error');
                    }
                } catch (e) {
                    showToast('Network error submitting order', 'error');
                } finally {
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Order to Kitchen';
                }
            });
        }

        // Customer Bill Modal & Request Bill
        const btnOpenRequestBillModal = document.getElementById('btn-open-request-bill-modal');
        const customerBillModal = document.getElementById('customer-bill-modal');
        const btnCloseCustomerBillModal = document.getElementById('btn-close-customer-bill-modal');
        const btnSubmitBillRequest = document.getElementById('btn-submit-bill-request');
        const customerThankyouModal = document.getElementById('customer-thankyou-modal');
        const btnRestartDiningSession = document.getElementById('btn-restart-dining-session');

        let lastKnownSessionTotal = 0;
        let wasInOccupiedSession = false;

        if (btnOpenRequestBillModal) {
            btnOpenRequestBillModal.addEventListener('click', () => {
                renderCustomerBillModal();
                customerBillModal.classList.remove('hidden');
            });
        }

        if (btnCloseCustomerBillModal) {
            btnCloseCustomerBillModal.addEventListener('click', () => {
                customerBillModal.classList.add('hidden');
            });
        }

        if (btnSubmitBillRequest) {
            btnSubmitBillRequest.addEventListener('click', async () => {
                btnSubmitBillRequest.disabled = true;
                btnSubmitBillRequest.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Intimating Waiter...';

                try {
                    const res = await fetch(`/api/table/${window.TABLE_ID}/request-bill`, { method: 'POST' });
                    const data = await res.json();
                    if (data.success) {
                        showToast('Waiter intimated for bill collection!', 'success');
                        document.getElementById('bill-request-status-badge').classList.remove('hidden');
                    } else {
                        showToast('Failed to send request', 'error');
                    }
                } catch (e) {
                    showToast('Connection error', 'error');
                } finally {
                    btnSubmitBillRequest.disabled = false;
                    btnSubmitBillRequest.innerHTML = '<i class="fa-solid fa-bell"></i> Intimate Waiter for Bill & Payment';
                }
            });
        }

        if (btnRestartDiningSession) {
            btnRestartDiningSession.addEventListener('click', () => {
                customerThankyouModal.classList.add('hidden');
                window.location.reload();
            });
        }

        function renderCustomerBillModal() {
            const listEl = document.getElementById('customer-bill-items-list');
            const totalEl = document.getElementById('customer-bill-total-price');
            listEl.innerHTML = '';

            if (window.LATEST_SESSION_ITEMS && window.LATEST_SESSION_ITEMS.length > 0) {
                let total = 0;
                window.LATEST_SESSION_ITEMS.forEach(it => {
                    const subtotal = parseFloat(it.price_at_order) * parseInt(it.quantity);
                    total += subtotal;
                    const row = document.createElement('div');
                    row.className = 'cart-item-card';
                    row.innerHTML = `
                        <div class="cart-item-details">
                            <div class="cart-item-name">${it.quantity}x ${it.dish_name}</div>
                            <div class="cart-item-price">₹${parseFloat(it.price_at_order).toFixed(2)} each</div>
                        </div>
                        <strong>₹${subtotal.toFixed(2)}</strong>
                    `;
                    listEl.appendChild(row);
                });
                totalEl.textContent = `₹${total.toFixed(2)}`;
                lastKnownSessionTotal = total;
            } else {
                listEl.innerHTML = '<p class="text-muted text-center">No active dishes ordered in this session yet.</p>';
                totalEl.textContent = '₹0.00';
            }
        }

        // Real-time Short Polling for Customer Order Status
        async function pollCustomerOrderStatus() {
            try {
                const res = await fetch(`/api/table/${window.TABLE_ID}/status`);
                const data = await res.json();

                const trackerContainer = document.getElementById('order-tracker-container');
                if (data.has_active_order && data.order) {
                    const order = data.order;
                    wasInOccupiedSession = true;
                    trackerContainer.classList.remove('hidden');

                    document.getElementById('tracker-order-id').textContent = `Order #${order.id}`;
                    window.CURRENT_ORDER_ID = order.id;
                    window.LATEST_SESSION_ITEMS = order.session_items || order.items || [];
                    lastKnownSessionTotal = parseFloat(order.session_total || order.total_amount || 0);

                    // Stepper state updates
                    const steps = ['placed', 'accepted', 'ready', 'served'];
                    const currentIdx = steps.indexOf(order.status);

                    steps.forEach((st, idx) => {
                        const stepEl = document.getElementById(`step-${st}`);
                        if (stepEl) {
                            stepEl.className = `stepper-item ${idx <= currentIdx ? 'completed' : ''} ${idx === currentIdx ? 'active' : ''}`;
                        }
                    });

                    // Message text
                    const msgEl = document.getElementById('tracker-status-message');
                    let msg = '';
                    switch (order.status) {
                        case 'placed':
                            msg = '<i class="fa-solid fa-spinner fa-spin"></i> Order received! Waiting for kitchen to accept...';
                            break;
                        case 'accepted':
                            msg = '<i class="fa-solid fa-utensils"></i> Chef is preparing your delicious meal!';
                            break;
                        case 'ready':
                            msg = '<i class="fa-solid fa-bell pulse-text"></i> Order is ready! Waiter is bringing it to your table.';
                            break;
                        case 'served':
                            msg = '<i class="fa-solid fa-heart"></i> Served! You can order more dishes anytime or request the bill from waiter.';
                            break;
                    }
                    msgEl.innerHTML = msg;

                    if (data.bill_requested) {
                        const statusBadge = document.getElementById('bill-request-status-badge');
                        if (statusBadge) statusBadge.classList.remove('hidden');
                    }
                } else {
                    trackerContainer.classList.add('hidden');
                    if (wasInOccupiedSession && data.table_status === 'available') {
                        wasInOccupiedSession = false;
                        if (customerBillModal) customerBillModal.classList.add('hidden');
                        const paidTotalEl = document.getElementById('thankyou-paid-total');
                        if (paidTotalEl) paidTotalEl.textContent = `₹${lastKnownSessionTotal.toFixed(2)}`;
                        if (customerThankyouModal) customerThankyouModal.classList.remove('hidden');
                        playNotificationSound();
                    }
                }
            } catch (e) {
                console.error('Customer poll error', e);
            }
        }

        // Initial poll and 3s interval
        pollCustomerOrderStatus();
        setInterval(pollCustomerOrderStatus, 3000);
    }

    // -------------------------------------------------------------
    // 4. Kitchen Page Logic (/kitchen)
    // -------------------------------------------------------------
    if (document.body.classList.contains('kitchen-page')) {
        let knownOrderIds = new Set();

        async function pollKitchenOrders() {
            try {
                const res = await fetch('/api/kitchen/orders');
                const data = await res.json();

                if (!data.success) return;

                const grid = document.getElementById('kitchen-orders-grid');
                const orders = data.orders || [];

                let pendingCount = 0;
                let cookingCount = 0;
                let hasNewPlacedOrder = false;

                if (orders.length === 0) {
                    grid.innerHTML = `
                        <div class="empty-kds-message card" style="grid-column: 1/-1; text-align:center; padding: 40px;">
                            <i class="fa-solid fa-utensils fa-3x" style="color: var(--text-muted); margin-bottom: 12px;"></i>
                            <p class="text-muted">No active orders right now. Waiting for new orders...</p>
                        </div>
                    `;
                    document.getElementById('kds-pending-count').textContent = '0';
                    document.getElementById('kds-cooking-count').textContent = '0';
                    return;
                }

                grid.innerHTML = '';

                orders.forEach(ord => {
                    if (ord.status === 'placed') {
                        pendingCount++;
                        if (!knownOrderIds.has(ord.id)) {
                            hasNewPlacedOrder = true;
                        }
                    } else if (ord.status === 'accepted') {
                        cookingCount++;
                    }
                    knownOrderIds.add(ord.id);

                    // Render Order Card
                    const card = document.createElement('div');
                    card.className = `kds-order-card status-${ord.status}`;
                    
                    const timeAgo = Math.max(0, Math.floor((new Date() - new Date(ord.created_at)) / 60000));

                    let itemsHtml = '';
                    (ord.items || []).forEach(it => {
                        itemsHtml += `
                            <div class="kds-item-row">
                                <span class="kds-item-qty">${it.quantity}x</span>
                                <div>
                                    <span class="kds-item-name">${it.dish_name}</span>
                                    ${it.notes ? `<div class="kds-item-note"><i class="fa-regular fa-note-sticky"></i> ${it.notes}</div>` : ''}
                                </div>
                            </div>
                        `;
                    });

                    let actionBtn = '';
                    if (ord.status === 'placed') {
                        actionBtn = `<button class="btn btn-warning btn-block btn-accept-order" data-id="${ord.id}"><i class="fa-solid fa-fire-burner"></i> Accept Order</button>`;
                    } else if (ord.status === 'accepted') {
                        actionBtn = `<button class="btn btn-success btn-block btn-ready-order" data-id="${ord.id}"><i class="fa-solid fa-bell"></i> Mark Ready to Serve</button>`;
                    } else {
                        actionBtn = `<div class="badge badge-success btn-block" style="text-align:center;"><i class="fa-solid fa-check"></i> Waiting for Waiter</div>`;
                    }

                    card.innerHTML = `
                        <div class="kds-card-header">
                            <span class="kds-table-name"><i class="fa-solid fa-chair"></i> ${ord.table_number}</span>
                            <span class="kds-timer"><i class="fa-regular fa-clock"></i> ${timeAgo}m ago</span>
                        </div>
                        <div class="kds-card-body">
                            <div class="badge badge-outline">Order #${ord.id}</div>
                            <div class="kds-items-list">${itemsHtml}</div>
                        </div>
                        <div class="kds-card-footer">${actionBtn}</div>
                    `;

                    grid.appendChild(card);
                });

                document.getElementById('kds-pending-count').textContent = pendingCount;
                document.getElementById('kds-cooking-count').textContent = cookingCount;

                if (hasNewPlacedOrder) {
                    playNotificationSound();
                    showToast('🔔 New Order Received in Kitchen!', 'warning');
                }

                // Add event listeners for status buttons
                document.querySelectorAll('.btn-accept-order').forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        const id = e.currentTarget.getAttribute('data-id');
                        await updateKdsStatus(id, 'accepted');
                    });
                });

                document.querySelectorAll('.btn-ready-order').forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        const id = e.currentTarget.getAttribute('data-id');
                        await updateKdsStatus(id, 'ready');
                    });
                });

            } catch (e) {
                console.error('Kitchen poll error', e);
            }
        }

        async function updateKdsStatus(orderId, status) {
            try {
                const res = await fetch(`/api/kitchen/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(`Order #${orderId} marked as ${status}`, 'success');
                    pollKitchenOrders();
                } else {
                    showToast(data.error || 'Update failed', 'error');
                }
            } catch (e) {
                showToast('Network error', 'error');
            }
        }

        pollKitchenOrders();
        setInterval(pollKitchenOrders, 3000);
    }

    // -------------------------------------------------------------
    // 5. Waiter Page Logic (/waiter) with Close Bill Modal Workflow
    // -------------------------------------------------------------
    if (document.body.classList.contains('waiter-page')) {
        let previousReadyCount = 0;
        let activeTableMap = {};

        const closeBillModal = document.getElementById('close-bill-modal');
        const closeBillModalBtn = document.getElementById('btn-close-bill-modal');
        const confirmCloseBillBtn = document.getElementById('btn-confirm-close-bill');
        let selectedTableIdForBill = null;

        if (closeBillModalBtn) {
            closeBillModalBtn.addEventListener('click', () => {
                closeBillModal.classList.add('hidden');
            });
        }

        async function pollWaiterStatus() {
            try {
                const res = await fetch('/api/waiter/status');
                const data = await res.json();

                if (!data.success) return;

                const tables = data.tables || [];
                const readyAlertsContainer = document.getElementById('waiter-alerts-container');
                const tablesGrid = document.getElementById('tables-grid');

                let availCount = 0;
                let occCount = 0;
                let readyCount = 0;

                readyAlertsContainer.innerHTML = '';
                tablesGrid.innerHTML = '';
                activeTableMap = {};

                tables.forEach(t => {
                    activeTableMap[t.id] = t;

                    if (t.status === 'available') availCount++;
                    else occCount++;

                    const isReady = t.has_ready_order;
                    const isBillRequested = t.bill_requested == 1;

                    if (isReady) {
                        readyCount++;
                        let readyOrderId = null;
                        (t.session_orders || []).forEach(o => {
                            if (o.status === 'ready') readyOrderId = o.id;
                        });

                        const alertCard = document.createElement('div');
                        alertCard.className = 'ready-alert-card';
                        alertCard.innerHTML = `
                            <div class="ready-alert-info">
                                <h3><i class="fa-solid fa-bell"></i> ${t.table_number} - Dish Ready to Serve!</h3>
                                <p>Items in kitchen are prepared and ready for table delivery.</p>
                            </div>
                            <button class="btn btn-success btn-serve-order" data-order-id="${readyOrderId}">
                                <i class="fa-solid fa-check"></i> Mark Served
                            </button>
                        `;
                        readyAlertsContainer.appendChild(alertCard);
                    }

                    if (isBillRequested) {
                        const billAlertCard = document.createElement('div');
                        billAlertCard.className = 'ready-alert-card';
                        billAlertCard.style.borderColor = '#f59e0b';
                        billAlertCard.style.background = '#fefce8';
                        billAlertCard.innerHTML = `
                            <div class="ready-alert-info">
                                <h3 style="color:#854d0e;"><i class="fa-solid fa-receipt pulse-icon"></i> ${t.table_number} - BILL REQUESTED BY CUSTOMER!</h3>
                                <p style="color:#a16207;">Customer requested final bill settlement. Total: <strong>₹${parseFloat(t.session_total || 0).toFixed(2)}</strong></p>
                            </div>
                            <button class="btn btn-warning btn-open-close-bill" data-table-id="${t.id}">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Collect Bill & Pay
                            </button>
                        `;
                        readyAlertsContainer.appendChild(billAlertCard);
                    }

                    const tableCard = document.createElement('div');
                    tableCard.className = `table-card ${t.status} ${isReady ? 'has-ready-order' : ''} ${isBillRequested ? 'has-bill-request' : ''}`;

                    let statusBadge = t.status === 'available' 
                        ? '<span class="badge badge-success">Available</span>'
                        : (isBillRequested
                            ? '<span class="badge badge-warning pulse-text" style="background:#f59e0b; color:#fff;"><i class="fa-solid fa-bell"></i> Bill Requested</span>'
                            : '<span class="badge badge-warning">Occupied</span>');

                    let activeOrderHtml = '<p class="text-muted small">No active orders</p>';
                    let actionsHtml = '';

                    if (t.status === 'occupied') {
                        let sessionItemsHtml = '';
                        (t.session_items || []).forEach(it => {
                            sessionItemsHtml += `
                                <div style="display:flex; justify-content:space-between; font-size:0.8rem; margin-bottom:2px;">
                                    <span><strong>${it.quantity}x</strong> ${it.dish_name}</span>
                                    <span>₹${parseFloat(it.subtotal).toFixed(2)}</span>
                                </div>
                            `;
                        });

                        activeOrderHtml = `
                            <div class="active-order-summary">
                                <div style="font-weight:700; margin-bottom:4px; font-size:0.85rem;"><i class="fa-solid fa-receipt"></i> Cumulative Table Session Dishes:</div>
                                <div style="max-height:100px; overflow-y:auto; background:#f8fafc; padding:6px; border-radius:6px; border:1px solid #e2e8f0; margin-bottom:6px;">
                                    ${sessionItemsHtml || '<span class="text-muted small">No items</span>'}
                                </div>
                                <div style="display:flex; justify-content:space-between; font-weight:800; font-size:0.95rem; color:#d97706;">
                                    <span>Session Total:</span>
                                    <span>₹${parseFloat(t.session_total || 0).toFixed(2)}</span>
                                </div>
                            </div>
                        `;

                        if (isReady) {
                            let readyOrderId = null;
                            (t.session_orders || []).forEach(o => {
                                if (o.status === 'ready') readyOrderId = o.id;
                            });
                            actionsHtml += `<button class="btn btn-success btn-sm btn-block btn-serve-order" data-order-id="${readyOrderId}"><i class="fa-solid fa-check"></i> Mark Dish Served</button>`;
                        }

                        if (t.can_close_bill) {
                            actionsHtml += `<button class="btn btn-primary btn-sm btn-block btn-open-close-bill" data-table-id="${t.id}" style="margin-top:6px;"><i class="fa-solid fa-file-invoice-dollar"></i> Close Bill & Pay (₹${parseFloat(t.session_total || 0).toFixed(2)})</button>`;
                        } else {
                            actionsHtml += `<button class="btn btn-secondary btn-sm btn-block" disabled style="margin-top:6px; opacity:0.75; cursor:not-allowed;"><i class="fa-solid fa-fire-burner"></i> Kitchen Preparing Food (Bill Locked)</button>`;
                        }
                    }

                    tableCard.innerHTML = `
                        <div class="table-card-header">
                            <span class="table-card-number">${t.table_number}</span>
                            ${statusBadge}
                        </div>
                        <div class="table-card-body" style="margin-bottom: 12px;">
                            ${activeOrderHtml}
                        </div>
                        <div class="table-card-footer">
                            ${actionsHtml}
                        </div>
                    `;

                    tablesGrid.appendChild(tableCard);
                });

                document.getElementById('waiter-available-count').textContent = availCount;
                document.getElementById('waiter-occupied-count').textContent = occCount;
                document.getElementById('waiter-ready-count').textContent = readyCount;

                if (readyCount > previousReadyCount) {
                    playNotificationSound();
                    showToast('🛎️ Order is Ready to Serve!', 'success');
                }
                previousReadyCount = readyCount;

                document.querySelectorAll('.btn-serve-order').forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        const orderId = e.currentTarget.getAttribute('data-order-id');
                        await markOrderServed(orderId);
                    });
                });

                document.querySelectorAll('.btn-open-close-bill').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const tableId = parseInt(e.currentTarget.getAttribute('data-table-id'));
                        openCloseBillModal(tableId);
                    });
                });

            } catch (e) {
                console.error('Waiter poll error', e);
            }
        }

        function openCloseBillModal(tableId) {
            const table = activeTableMap[tableId];
            if (!table) return;

            selectedTableIdForBill = tableId;
            document.getElementById('bill-modal-table-title').innerHTML = `<i class="fa-solid fa-receipt"></i> Close Bill - ${table.table_number}`;
            
            const listEl = document.getElementById('bill-modal-items-list');
            listEl.innerHTML = '';

            let items = table.session_items || [];
            if (items.length === 0) {
                listEl.innerHTML = '<p class="text-muted">No dishes ordered for this table.</p>';
            } else {
                items.forEach(it => {
                    const row = document.createElement('div');
                    row.className = 'cart-item-card';
                    row.innerHTML = `
                        <div class="cart-item-details">
                            <div class="cart-item-name">${it.quantity}x ${it.dish_name}</div>
                            <div class="cart-item-price">₹${parseFloat(it.price_at_order).toFixed(2)} each</div>
                        </div>
                        <strong>₹${parseFloat(it.subtotal).toFixed(2)}</strong>
                    `;
                    listEl.appendChild(row);
                });
            }

            document.getElementById('bill-modal-total-price').textContent = `₹${parseFloat(table.session_total || 0).toFixed(2)}`;
            closeBillModal.classList.remove('hidden');
        }

        if (confirmCloseBillBtn) {
            confirmCloseBillBtn.addEventListener('click', async () => {
                if (!selectedTableIdForBill) return;

                const paymentMethod = document.getElementById('payment-method-select').value;
                confirmCloseBillBtn.disabled = true;
                confirmCloseBillBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Payment...';

                try {
                    const res = await fetch(`/waiter/tables/${selectedTableIdForBill}/close-bill`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ payment_method: paymentMethod })
                    });
                    const data = await res.json();

                    if (data.success) {
                        showToast('Payment settled & table is now free!', 'success');
                        closeBillModal.classList.add('hidden');
                        pollWaiterStatus();
                    } else {
                        showToast(data.error || 'Failed to close bill', 'error');
                    }
                } catch (e) {
                    showToast('Network error closing bill', 'error');
                } finally {
                    confirmCloseBillBtn.disabled = false;
                    confirmCloseBillBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Confirm Payment & Free Table';
                }
            });
        }

        async function markOrderServed(orderId) {
            try {
                const res = await fetch(`/waiter/orders/${orderId}/serve`, { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    showToast(`Dish marked as served`, 'success');
                    pollWaiterStatus();
                }
            } catch (e) {
                showToast('Error marking order served', 'error');
            }
        }

        pollWaiterStatus();
        setInterval(pollWaiterStatus, 3000);
    }

    // -------------------------------------------------------------
    // 6. Owner Page Logic (/owner & /admin)
    // -------------------------------------------------------------
    if (document.body.classList.contains('owner-page')) {
        const ownerTabs = document.querySelectorAll('.owner-tab');
        ownerTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                ownerTabs.forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.owner-tab-content').forEach(c => c.classList.remove('active'));

                tab.classList.add('active');
                const targetId = tab.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
            });
        });

        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            const targetTab = document.querySelector(`.owner-tab[data-target="tab-${hash}"]`);
            if (targetTab) {
                targetTab.click();
            }
        }

        // Real-Time Polling for Owner Analytics & Recent Orders
        async function pollOwnerStats() {
            try {
                const res = await fetch('/api/owner/stats');
                if (!res.ok) return;
                const data = await res.json();
                if (!data.success || !data.stats) return;

                const s = data.stats;

                // Update metric cards
                const elRev = document.getElementById('owner-total-revenue');
                if (elRev) elRev.textContent = `₹${parseFloat(s.total_revenue).toFixed(2)}`;

                const elOrd = document.getElementById('owner-total-orders');
                if (elOrd) elOrd.textContent = s.total_orders;

                const elAvg = document.getElementById('owner-avg-order-value');
                if (elAvg) elAvg.textContent = `₹${parseFloat(s.avg_order_value).toFixed(2)}`;

                const elRevToday = document.getElementById('owner-revenue-today');
                if (elRevToday) elRevToday.textContent = `₹${parseFloat(s.revenue_today).toFixed(2)}`;

                const elOrdToday = document.getElementById('owner-orders-today');
                if (elOrdToday) elOrdToday.textContent = `${s.orders_today} orders today`;

                if (s.table_metrics) {
                    const elOcc = document.getElementById('owner-table-occupancy');
                    if (elOcc) elOcc.textContent = `${s.table_metrics.occupancy_rate}%`;

                    const elOccSub = document.getElementById('owner-table-occupancy-sub');
                    if (elOccSub) elOccSub.textContent = `${s.table_metrics.occupied_tables} of ${s.table_metrics.total_tables} tables occupied`;
                }

                // Save current scroll position before updating
                const scrollBox = document.getElementById('recent-orders-scroll-box');
                const savedScrollTop = scrollBox ? scrollBox.scrollTop : 0;

                // Update Recent Orders container with Live Auto Refresh & Item Breakdown
                const recentContainer = document.getElementById('owner-recent-orders-container');
                if (recentContainer && s.recent_orders) {
                    if (s.recent_orders.length === 0) {
                        recentContainer.innerHTML = `
                            <div class="card-header-flex">
                                <h3><i class="fa-solid fa-clock-rotate-left text-primary"></i> Live Recent Orders</h3>
                                <span class="badge badge-success"><i class="fa-solid fa-rotate"></i> Auto Refresh</span>
                            </div>
                            <p class="text-muted p-4">No orders found.</p>`;
                    } else {
                        let html = `
                            <div class="card-header-flex">
                                <h3><i class="fa-solid fa-clock-rotate-left text-primary"></i> Live Recent Orders</h3>
                                <span class="badge badge-success"><i class="fa-solid fa-rotate"></i> Auto Refresh</span>
                            </div>
                            <div class="recent-orders-scroll-container" id="recent-orders-scroll-box">
                                <div class="recent-orders-list">`;

                        s.recent_orders.forEach(ord => {
                            const dateObj = new Date(ord.created_at);
                            const dateStr = isNaN(dateObj) ? ord.created_at : dateObj.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });

                            let itemsHtml = '';
                            if (ord.items && ord.items.length > 0) {
                                itemsHtml = '<div class="ro-items-summary">';
                                ord.items.forEach(item => {
                                    const itemTotal = (parseFloat(item.price_at_order) * parseInt(item.quantity)).toFixed(2);
                                    itemsHtml += `
                                        <div class="ro-item-line">
                                            <span class="ro-item-name">• ${item.name}</span>
                                            <span class="ro-item-qty">x${item.quantity}</span>
                                            <span class="ro-item-price">₹${itemTotal}</span>
                                        </div>`;
                                });
                                itemsHtml += '</div>';
                            }

                            html += `
                                <div class="recent-order-item">
                                    <div class="ro-header">
                                        <span class="ro-table"><i class="fa-solid fa-chair"></i> ${ord.table_number}</span>
                                        <span class="badge badge-status-${ord.status}">${ord.status.toUpperCase()}</span>
                                    </div>
                                    ${itemsHtml}
                                    <div class="ro-meta">
                                        <span>Order #${ord.id}</span> &bull;
                                        <strong>Total: ₹${parseFloat(ord.total_amount).toFixed(2)}</strong> &bull;
                                        <small>${dateStr}</small>
                                    </div>
                                </div>`;
                        });

                        html += `</div></div>`;
                        recentContainer.innerHTML = html;

                        // Restore scroll position after HTML replacement
                        const newScrollBox = document.getElementById('recent-orders-scroll-box');
                        if (newScrollBox && savedScrollTop > 0) {
                            newScrollBox.scrollTop = savedScrollTop;
                        }
                    }
                }
            } catch (e) {
                // Silent catch on poll error
            }
        }

        pollOwnerStats();
        setInterval(pollOwnerStats, 5000);

        // Window Print / Download PDF Report Handler
        window.printReport = function() {
            window.print();
        };

        document.querySelectorAll('.toggle-availability').forEach(chk => {

            chk.addEventListener('change', async (e) => {
                const id = e.target.getAttribute('data-id');
                try {
                    await fetch(`/owner/dish/${id}/toggle`, { method: 'POST' });
                    showToast('Dish availability updated', 'success');
                } catch (err) {
                    showToast('Failed to update availability', 'error');
                }
            });
        });

        document.querySelectorAll('.btn-delete-dish').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                if (!confirm('Are you sure you want to delete this dish?')) return;
                const id = e.currentTarget.getAttribute('data-id');
                try {
                    const res = await fetch(`/owner/dish/${id}/delete`, { method: 'POST' });
                    const data = await res.json();
                    if (data.success) {
                        showToast('Dish deleted', 'success');
                        window.location.reload();
                    }
                } catch (err) {
                    showToast('Delete failed', 'error');
                }
            });
        });

        document.querySelectorAll('.btn-delete-table').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                if (!confirm('Are you sure you want to remove this table?')) return;
                const id = e.currentTarget.getAttribute('data-id');
                try {
                    const res = await fetch(`/owner/table/${id}/delete`, { method: 'POST' });
                    const data = await res.json();
                    if (data.success) {
                        showToast('Table deleted', 'success');
                        window.location.reload();
                    }
                } catch (err) {
                    showToast('Delete table failed', 'error');
                }
            });
        });

        const dishModal = document.getElementById('dish-modal');
        const openDishModalBtn = document.getElementById('btn-open-dish-modal');
        const closeDishModalBtn = document.getElementById('btn-close-dish-modal');

        document.querySelectorAll('.btn-preset-img').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const url = e.currentTarget.getAttribute('data-url');
                document.getElementById('dish-image-url').value = url;
                showToast('Sample image URL applied', 'info');
            });
        });

        if (openDishModalBtn) {
            openDishModalBtn.addEventListener('click', () => {
                document.getElementById('modal-dish-title').textContent = 'Add New Dish';
                document.getElementById('dish-id').value = '';
                document.getElementById('dish-name').value = '';
                document.getElementById('dish-category').value = 'Starters & Soups';
                document.getElementById('dish-price').value = '';
                document.getElementById('dish-description').value = '';
                document.getElementById('dish-image-url').value = '';
                document.getElementById('dish-is-available').checked = true;
                dishModal.classList.remove('hidden');
            });
        }

        if (closeDishModalBtn) {
            closeDishModalBtn.addEventListener('click', () => {
                dishModal.classList.add('hidden');
            });
        }

        document.querySelectorAll('.btn-edit-dish').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const dishData = JSON.parse(e.currentTarget.getAttribute('data-dish'));
                document.getElementById('modal-dish-title').textContent = 'Edit Dish';
                document.getElementById('dish-id').value = dishData.id;
                document.getElementById('dish-name').value = dishData.name;
                document.getElementById('dish-category').value = dishData.category;
                document.getElementById('dish-price').value = dishData.price;
                document.getElementById('dish-description').value = dishData.description || '';
                document.getElementById('dish-image-url').value = dishData.image_url || '';
                document.getElementById('dish-is-available').checked = dishData.is_available == 1;
                dishModal.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.btn-print-qr').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tableName = e.currentTarget.getAttribute('data-table');
                const qrSvg = e.currentTarget.getAttribute('data-qr');
                const qrUrl = e.currentTarget.getAttribute('data-url');

                const printWindow = window.open('', '_blank', 'width=600,height=600');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Print QR - ${tableName}</title>
                        <style>
                            body { font-family: sans-serif; text-align: center; padding: 40px; }
                            .card { border: 2px solid #333; padding: 30px; border-radius: 20px; display: inline-block; }
                            h1 { margin-bottom: 10px; }
                            .qr-box { margin: 20px 0; }
                            .qr-box img { width: 250px; height: 250px; }
                            p { color: #666; font-size: 14px; }
                        </style>
                    </head>
                    <body>
                        <div class="card">
                            <h1>${tableName}</h1>
                            <p>Scan to view menu & order</p>
                            <div class="qr-box"><img src="${qrSvg}"></div>
                            <p>${qrUrl}</p>
                        </div>
                        <script>
                            window.onload = function() { window.print(); window.close(); }
                        </script>
                    </body>
                    </html>
                `);
                printWindow.document.close();
            });
        });

        // Batch Add Multiple Tables Modal Handlers
        const batchTableModal = document.getElementById('batch-table-modal');
        const openBatchTableBtn = document.getElementById('btn-open-batch-table-modal');
        const closeBatchTableBtn = document.getElementById('btn-close-batch-table-modal');

        if (openBatchTableBtn && batchTableModal) {
            openBatchTableBtn.addEventListener('click', () => {
                batchTableModal.classList.remove('hidden');
            });
        }

        if (closeBatchTableBtn && batchTableModal) {
            closeBatchTableBtn.addEventListener('click', () => {
                batchTableModal.classList.add('hidden');
            });
        }

        document.querySelectorAll('.btn-count-preset').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const count = e.currentTarget.getAttribute('data-count');
                const input = document.getElementById('table-count');
                if (input) input.value = count;
            });
        });
    }


    // -------------------------------------------------------------
    // 7. Staff Login Form Logic (/login)
    // -------------------------------------------------------------
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());

            const errorEl = document.getElementById('login-error');
            errorEl.classList.add('hidden');

            try {
                const res = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    errorEl.textContent = result.error || 'Invalid credentials';
                    errorEl.classList.remove('hidden');
                }
            } catch (err) {
                errorEl.textContent = 'Connection error. Please try again.';
                errorEl.classList.remove('hidden');
            }
        });
    }
});
