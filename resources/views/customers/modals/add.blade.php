
<!-- Add Customer Modal -->
<div id="customersAddModalOverlay" class="customersModalOverlay" hidden>
    <section id="customersAddModal" role="dialog" aria-modal="true" aria-labelledby="customersAddModalTitle">
        <!-- Modal Header -->
        <div id="customersAddModalHeader">
            <span class="material-symbols-outlined customersModalHeaderIcon" aria-hidden="true">person_add</span>
            <div id="customersAddModalHeading">
                <h2 id="customersAddModalTitle">Add Customer</h2>
                <p>Register a new customer to the system.</p>
            </div>
            <button type="button" id="customersAddModalClose" aria-label="Close Add Customer modal">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </div>
        <!-- Customer Form -->
        <form id="customersAddForm" method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div id="customersAddModalBody">
                <!-- Customer Name -->
                <div class="customersAddFieldGroup">
                    <label for="customersAddName">Customer Name <span>*</span></label>
                    <div class="customersAddInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">person</span>
                        <input type="text" id="customersAddName" name="full_name" placeholder="Enter full name" autocomplete="name" required>
                    </div>
                </div>
                <!-- Phone Number and Email -->
                <div id="customersAddContactFields">
                    <div class="customersAddFieldGroup">
                        <label for="customersAddPhone">Phone Number</label>
                        <div class="customersAddInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">call</span>
                            <input type="tel" id="customersAddPhone" name="phone" placeholder="Enter phone number (e.g., 0917 123 4567)" autocomplete="tel">
                        </div>
                    </div>
                    <div class="customersAddFieldGroup">
                        <label for="customersAddEmail">Email</label>
                        <div class="customersAddInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                            <input type="email" id="customersAddEmail" name="email" placeholder="Enter email (optional)" autocomplete="email">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div id="customersAddModalFooter">
                <button type="button" id="customersAddCancelButton">Cancel</button>
                <button type="submit" id="customersAddSaveButton">
                    <span class="material-symbols-outlined" aria-hidden="true">person_add</span>
                    Add Customer
                </button>
            </div>
        </form>
    </section>
</div>
