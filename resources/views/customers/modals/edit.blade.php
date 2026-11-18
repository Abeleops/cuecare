
<!-- Edit Customer Modal -->
<div id="customersEditModalOverlay" data-reopen="{{ $errors->customerEdit->any() ? 'true' : 'false' }}" data-edit-id="{{ old('_edit_id') }}" data-update-url="{{ route('customers.update', ['customer' => '__ID__']) }}" class="customersModalOverlay" hidden>
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
            @if ($errors->customerEdit->any())
                <div role="alert" tabindex="-1" data-validation-summary>
                    <p>Please correct the following</p>
                    <ul>@foreach ($errors->customerEdit->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
                </div>
            @endif
            @method('PUT')
            <input type="hidden" name="_edit_id" value="{{ old('_edit_id') }}">
            <div id="customersEditModalBody">
                <!-- Customer Name -->
                <div class="customersEditFieldGroup">
                    <label for="customersEditName">Customer Name <span>*</span></label>
                    <div class="customersEditInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">person</span>
                        <input type="text" id="customersEditName" name="full_name" placeholder="Enter full name" autocomplete="name" required value="{{ $errors->customerEdit->any() ? old('full_name') : '' }}">
                            @error('full_name', 'customerEdit') <p role="alert">{{ $message }}</p> @enderror
                    </div>
                </div>
                <!-- Phone Number and Email -->
                <div id="customersEditContactFields">
                    <div class="customersEditFieldGroup">
                        <label for="customersEditPhone">Phone Number</label>
                        <div class="customersEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">call</span>
                            <input type="tel" id="customersEditPhone" name="phone" placeholder="Enter phone number" autocomplete="tel" value="{{ $errors->customerEdit->any() ? old('phone') : '' }}">
                            @error('phone', 'customerEdit') <p role="alert">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="customersEditFieldGroup">
                        <label for="customersEditEmail">Email</label>
                        <div class="customersEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                            <input type="email" id="customersEditEmail" name="email" placeholder="Enter email" autocomplete="email" value="{{ $errors->customerEdit->any() ? old('email') : '' }}">
                            @error('email', 'customerEdit') <p role="alert">{{ $message }}</p> @enderror
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
