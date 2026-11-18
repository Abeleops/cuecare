
<!-- Edit Customer Modal -->
<div id="customersEditModalOverlay" class="customersModalOverlay" hidden>
    <section id="customersEditModal" role="dialog" aria-modal="true" aria-labelledby="customersEditModalTitle">
        <!-- Modal Header -->
        <div id="customersEditModalHeader">
            <span class="material-symbols-outlined customersEditHeaderIcon" aria-hidden="true">person_add</span>
            <div id="customersEditModalHeading">
                <h2 id="customersEditModalTitle">Edit Customer</h2>
                <p>Update the customer's information.</p>
            </div>
            <button type="button" id="customersEditModalClose" aria-label="Close Edit Customer modal">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </div>
        <!-- Edit Customer Form -->
        <form id="customersEditForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div id="customersEditModalBody">
                <!-- Customer Name -->
                <div class="customersEditFieldGroup">
                    <label for="customersEditName">Customer Name <span>*</span></label>
                    <div class="customersEditInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">person</span>
                        <input type="text" id="customersEditName" name="full_name" value="Maria Santos" placeholder="Enter full name" autocomplete="name" required>
                    </div>
                </div>
                <!-- Phone Number and Email -->
                <div id="customersEditContactFields">
                    <div class="customersEditFieldGroup">
                        <label for="customersEditPhone">Phone Number</label>
                        <div class="customersEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">call</span>
                            <input type="tel" id="customersEditPhone" name="phone" value="0917 123 4567" placeholder="Enter phone number" autocomplete="tel">
                        </div>
                    </div>
                    <div class="customersEditFieldGroup">
                        <label for="customersEditEmail">Email</label>
                        <div class="customersEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                            <input type="email" id="customersEditEmail" name="email" value="maria.santos@gmail.com" placeholder="Enter email" autocomplete="email">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div id="customersEditModalFooter">
                <button type="button" id="customersEditCancelButton">Cancel</button>
                <button type="submit" id="customersEditSaveButton">
                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                    Save Changes
                </button>
            </div>
        </form>
    </section>
</div>
