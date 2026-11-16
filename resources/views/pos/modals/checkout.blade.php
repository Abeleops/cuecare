
<!-- Checkout Modal -->
<div id="posCheckoutModalOverlay" class="posModalOverlay" hidden>
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
        <form id="posCheckoutForm">
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
                                <!-- Static Sample Order Items -->
                                <tr>
                                    <td>Bottled Water</td>
                                    <td>2</td>
                                    <td>₱20.00</td>
                                    <td>₱40.00</td>
                                </tr>
                                <tr>
                                    <td>Softdrink</td>
                                    <td>1</td>
                                    <td>₱35.00</td>
                                    <td>₱35.00</td>
                                </tr>
                                <tr>
                                    <td>Chips</td>
                                    <td>1</td>
                                    <td>₱50.00</td>
                                    <td>₱50.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="posCheckoutTotal">
                        <strong>Total Amount</strong>
                        <strong id="posCheckoutTotalAmount">₱125.00</strong>
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
                                    <!-- Walk-in Customer -->
                                    <button type="button" class="posCheckoutCustomerSuggestion" data-customer-id="" data-customer-name="Walk-in / No Customer" role="option" aria-selected="false">
                                        <span class="material-symbols-outlined" aria-hidden="true">person_off</span>
                                        <span class="posCheckoutSuggestionDetails">
                                            <strong>Walk-in / No Customer</strong>
                                            <small>No registered customer required</small>
                                        </span>
                                    </button>
                                    <!-- Static Sample Customers -->
                                    <button type="button" class="posCheckoutCustomerSuggestion" data-customer-id="CUST-001" data-customer-name="Maria Santos" role="option" aria-selected="false">
                                        <span class="material-symbols-outlined" aria-hidden="true">account_circle</span>
                                        <span class="posCheckoutSuggestionDetails">
                                            <strong>Maria Santos</strong>
                                            <small>0917 123 4567</small>
                                        </span>
                                    </button>
                                    <button type="button" class="posCheckoutCustomerSuggestion" data-customer-id="CUST-002" data-customer-name="Carlo Reyes" role="option" aria-selected="false">
                                        <span class="material-symbols-outlined" aria-hidden="true">account_circle</span>
                                        <span class="posCheckoutSuggestionDetails">
                                            <strong>Carlo Reyes</strong>
                                            <small>0920 765 4321</small>
                                        </span>
                                    </button>
                                    <button type="button" class="posCheckoutCustomerSuggestion" data-customer-id="CUST-003" data-customer-name="Angelica Cruz" role="option" aria-selected="false">
                                        <span class="material-symbols-outlined" aria-hidden="true">account_circle</span>
                                        <span class="posCheckoutSuggestionDetails">
                                            <strong>Angelica Cruz</strong>
                                            <small>0918 555 7890</small>
                                        </span>
                                    </button>
                                    <button type="button" class="posCheckoutCustomerSuggestion" data-customer-id="CUST-004" data-customer-name="Mark Villanueva" role="option" aria-selected="false">
                                        <span class="material-symbols-outlined" aria-hidden="true">account_circle</span>
                                        <span class="posCheckoutSuggestionDetails">
                                            <strong>Mark Villanueva</strong>
                                            <small>0935 222 1111</small>
                                        </span>
                                    </button>
                                    <button type="button" class="posCheckoutCustomerSuggestion" data-customer-id="CUST-005" data-customer-name="Denise Flores" role="option" aria-selected="false">
                                        <span class="material-symbols-outlined" aria-hidden="true">account_circle</span>
                                        <span class="posCheckoutSuggestionDetails">
                                            <strong>Denise Flores</strong>
                                            <small>0919 888 3344</small>
                                        </span>
                                    </button>
                                    <!-- No Matching Customers -->
                                    <div id="posCheckoutNoCustomerResults" hidden>
                                        <span class="material-symbols-outlined" aria-hidden="true">person_search</span>
                                        <p>No customers found</p>
                                        <small>Click Add New to register a customer.</small>
                                    </div>
                                </div>
                                <!-- Selected Customer ID -->
                                <input type="hidden" id="posCheckoutSelectedCustomerId" name="customerId" value="">
                            </div>
                            <!-- Add New Customer Button -->
                            <button type="button" id="posCheckoutAddCustomerButton">
                                <span class="material-symbols-outlined" aria-hidden="true">person_add</span>
                                Add New
                            </button>
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
                                    <input type="radio" name="paymentMethod" value="cash" checked required>
                                    <span>Cash</span>
                                </label>
                                <label class="posCheckoutPaymentOption">
                                    <input type="radio" name="paymentMethod" value="gcash">
                                    <span>GCash</span>
                                </label>
                            </div>
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
                                    name="amountReceived"
                                    placeholder="Enter amount received"
                                    value="200.00"
                                    min="0"
                                    step="0.01"
                                    required
                                >
                            </div>
                        </div>
                        <!-- Change -->
                        <div id="posCheckoutChangeContainer" aria-live="polite">
                            <span>Change</span>
                            <strong id="posCheckoutChangeAmount">₱75.00</strong>
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
