
<!-- Checkout Modal -->
<div id="posCheckoutModalOverlay" class="posModalOverlay" data-has-errors="{{ $errors->any() ? 'true' : 'false' }}" hidden>
    <section id="posCheckoutModal" role="dialog" aria-modal="true" aria-labelledby="posCheckoutModalTitle" tabindex="-1">
        <!-- Modal Header -->
        <div id="posCheckoutModalHeader">
            <span class="material-symbols-outlined posCheckoutHeaderIcon" aria-hidden="true">credit_card</span>
            <div id="posCheckoutModalHeading">
                <h2 id="posCheckoutModalTitle">Checkout</h2>
                <p>Review the order and process the payment.</p>
            </div>
            <button type="button" id="posCheckoutModalClose" aria-label="Close checkout modal">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </div>
        <!-- Checkout Form -->
        <form id="posCheckoutForm" method="POST" action="{{ route('pos.checkout') }}">
            @csrf
            <div class="posOrderTypeField">
                <label for="posOrderType">Order type</label>
                <select id="posOrderType" name="order_type">
                    <option value="dine_in" @selected(old('order_type', 'dine_in') === 'dine_in')>Dine-In</option>
                    <option value="takeout" @selected(old('order_type') === 'takeout')>Takeout</option>
                </select>
            </div>
            <div id="posCheckoutItems"></div>
            <div id="posCheckoutModalBody">
                <!-- Order Summary -->
                <section id="posCheckoutOrderSummary" aria-labelledby="posCheckoutOrderTitle">
                    <h3 id="posCheckoutOrderTitle">Order Summary</h3>
                    <div id="posCheckoutTableContainer">
                        <table id="posCheckoutTable">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Unit Price</th>
                                    <th scope="col">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="4">No items in the order.</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="posCheckoutTotal">
                        <strong>Total Amount</strong>
                        <strong id="posCheckoutTotalAmount">₱0.00</strong>
                    </div>
                </section>
                <!-- Right Column -->
                <div id="posCheckoutRightColumn">
                    <!-- Customer Selection -->
                    <section id="posCheckoutCustomerSection" aria-labelledby="posCheckoutCustomerTitle">
                        <h3 id="posCheckoutCustomerTitle">
                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                            Customer
                        </h3>
                        <div id="posCheckoutCustomerControls">
                            <div id="posCheckoutCustomerSearchWrapper">
                                <!-- Customer Search Input -->
                                <div id="posCheckoutCustomerSearchContainer">
                                    <span class="material-symbols-outlined" aria-hidden="true">search</span>
                                    <input
                                        type="search"
                                        id="posCheckoutCustomerSearch"
                                        placeholder="Search customer name or phone..."
                                        autocomplete="off"
                                        role="combobox"
                                        aria-label="Search registered customers"
                                        aria-autocomplete="list"
                                        aria-controls="posCheckoutCustomerSuggestions"
                                        aria-expanded="false"
                                    >
                                    <button type="button" id="posCheckoutClearCustomer" aria-label="Clear selected customer" hidden>
                                        <span class="material-symbols-outlined" aria-hidden="true">close</span>
                                    </button>
                                </div>
                                <!-- Customer Suggestions -->
                                <div id="posCheckoutCustomerSuggestions" role="listbox" aria-label="Customer suggestions" hidden>
                                </div>
                                <!-- Selected Customer ID -->
                                <input type="hidden" id="posCheckoutSelectedCustomerId" name="customer_id" value="">
                            </div>
                            <!-- Add New Customer Button -->
                        </div>
                    </section>
                    <!-- Payment Details -->
                    <section id="posCheckoutPaymentDetails" aria-labelledby="posCheckoutPaymentTitle">
                        <h3 id="posCheckoutPaymentTitle">Payment Details</h3>
                        <!-- Payment Method -->
                        <div id="posCheckoutPaymentMethodGroup">
                            <p id="posCheckoutPaymentMethodLabel">Payment Method <span>*</span></p>
                            <div id="posCheckoutPaymentOptions" role="group" aria-labelledby="posCheckoutPaymentMethodLabel">
                                <label class="posCheckoutPaymentOption">
                                    <input type="radio" name="payment_method" value="cash" @checked(old('payment_method', 'cash') === 'cash') required>
                                    <span>Cash</span>
                                </label>
                                <label class="posCheckoutPaymentOption">
                                    <input type="radio" name="payment_method" value="gcash" @checked(old('payment_method') === 'gcash')>
                                    <span>GCash</span>
                                </label>
                                <label class="posCheckoutPaymentOption">
                                    <input type="radio" name="payment_method" value="split" @checked(old('payment_method') === 'split')>
                                    <span>Cash + GCash</span>
                                </label>
                            </div>
                        </div>
                        <div id="posSplitPaymentGroup" hidden>
                            <label for="posCashAmount">Cash amount (₱)</label>
                            <input type="number" id="posCashAmount" name="cash_amount" value="{{ old('cash_amount') }}" min="0.01" step="0.01">
                            @error('cash_amount') <p role="alert">{{ $message }}</p> @enderror
                            <label for="posGcashAmount">GCash amount (₱)</label>
                            <input type="number" id="posGcashAmount" name="gcash_amount" value="{{ old('gcash_amount') }}" min="0.01" step="0.01">
                            @error('gcash_amount') <p role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div id="posGcashReferenceGroup" hidden>
                            <label for="posGcashReference">GCash reference</label>
                            <input id="posGcashReference" name="gcash_reference" maxlength="50" pattern="[A-Za-z0-9]+" autocomplete="off">
                            @error('gcash_reference') <p role="alert">{{ $message }}</p> @enderror
                        </div>
                        <!-- Amount Received -->
                        <div id="posCheckoutAmountGroup">
                            <label for="posCheckoutAmountReceived">
                                Amount Received (₱) <span>*</span>
                            </label>
                            <div id="posCheckoutAmountInputContainer">
                                <span>₱</span>
                                <input
                                    type="number"
                                    id="posCheckoutAmountReceived"
                                    name="amount_received"
                                    placeholder="Enter amount received"
                                    value="{{ old('amount_received') }}"
                                    min="0"
                                    step="0.01"
                                    required
                                >
                            </div>
                            @error('amount_received') <p role="alert">{{ $message }}</p> @enderror
                        </div>
                        <!-- Change -->
                        <div id="posCheckoutChangeContainer" aria-live="polite">
                            <span>Change</span>
                            <strong id="posCheckoutChangeAmount">Calculated after payment</strong>
                        </div>
                    </section>
                </div>
            </div>
            <!-- Modal Footer -->
            <div id="posCheckoutModalFooter">
                <button type="button" id="posCheckoutCancelButton">Cancel</button>
                <button type="submit" id="posCheckoutConfirmButton">
                    <span class="material-symbols-outlined" aria-hidden="true">check</span>
                    Confirm Payment
                </button>
            </div>
        </form>
    </section>
</div>
