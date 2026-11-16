
<!-- POS Add New Customer Modal -->
<div id="posAddCustomerModalOverlay" class="posCustomerModalOverlay" hidden>
    <section id="posAddCustomerModal" role="dialog" aria-modal="true" aria-labelledby="posAddCustomerModalTitle">
        <!-- Modal Header -->
        <div id="posAddCustomerModalHeader">
            <span class="material-symbols-outlined posAddCustomerHeaderIcon" aria-hidden="true">person_add</span>
            <div id="posAddCustomerModalHeading">
                <h2 id="posAddCustomerModalTitle">Add New Customer</h2>
                <p>Register a customer for this transaction.</p>
            </div>
            <button type="button" id="posAddCustomerModalClose" aria-label="Close Add New Customer modal">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </div>
        <!-- Add Customer Form -->
        <form id="posAddCustomerForm">
            <div id="posAddCustomerModalBody">
                <!-- Customer Name -->
                <div class="posAddCustomerFieldGroup">
                    <label for="posAddCustomerName">
                        Customer Name <span>*</span>
                    </label>
                    <div class="posAddCustomerInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">person</span>
                        <input type="text" id="posAddCustomerName" name="customerName" placeholder="Enter customer name" autocomplete="name" required>
                    </div>
                </div>
                <!-- Phone Number -->
                <div class="posAddCustomerFieldGroup">
                    <label for="posAddCustomerPhone">
                        Phone Number <span class="posAddCustomerOptional">(Optional)</span>
                    </label>
                    <div class="posAddCustomerInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">call</span>
                        <input type="tel" id="posAddCustomerPhone" name="phoneNumber" placeholder="Enter phone number" autocomplete="tel">
                    </div>
                </div>
                <!-- Email Address -->
                <div class="posAddCustomerFieldGroup">
                    <label for="posAddCustomerEmail">
                        Email Address <span class="posAddCustomerOptional">(Optional)</span>
                    </label>
                    <div class="posAddCustomerInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                        <input type="email" id="posAddCustomerEmail" name="email" placeholder="Enter email address" autocomplete="email">
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div id="posAddCustomerModalFooter">
                <button type="button" id="posAddCustomerCancelButton">
                    Cancel
                </button>
                <button type="submit" id="posAddCustomerSaveButton">
                    <span class="material-symbols-outlined" aria-hidden="true">person_add</span>
                    Add Customer
                </button>
            </div>
        </form>
    </section>
</div>
