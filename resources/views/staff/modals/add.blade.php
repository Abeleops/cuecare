
<!-- Add Staff Modal -->
<div id="staffAddModalOverlay" class="staffModalOverlay" hidden>
    <section id="staffAddModal" role="dialog" aria-modal="true" aria-labelledby="staffAddModalTitle">
        <!-- Modal Header -->
        <div id="staffAddModalHeader">
            <span class="material-symbols-outlined staffAddHeaderIcon" aria-hidden="true">person_add</span>
            <div id="staffAddModalHeading">
                <h2 id="staffAddModalTitle">Add Staff</h2>
                <p>Create a new staff account for the system.</p>
            </div>
            <button type="button" id="staffAddModalClose" aria-label="Close Add Staff modal">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </div>
        <!-- Add Staff Form -->
        <form id="staffAddForm" method="POST" action="{{ route('staff.store') }}" enctype="multipart/form-data">
            @csrf
            <div id="staffAddModalBody">
                <!-- Profile Picture -->
                <div id="staffAddImageGroup">
                    <label for="staffAddImage">Profile Picture</label>
                    <label for="staffAddImage" id="staffAddImageUpload">
                        <span id="staffAddAvatarContainer">
                            <span class="material-symbols-outlined staffAddAvatarIcon" aria-hidden="true">person</span>
                            <span class="material-symbols-outlined" id="staffAddCameraIcon" aria-hidden="true">photo_camera</span>
                        </span>
                        <span>Upload Photo</span>
                        <small>PNG, JPG (Max 2MB)</small>
                    </label>
                    <input type="file" id="staffAddImage" name="avatar" accept=".png,.jpg,.jpeg" hidden>
                </div>
                <!-- Staff Information -->
                <div id="staffAddFields">
                    <div class="staffAddFieldGroup">
                        <label for="staffAddFullName">Full Name <span>*</span></label>
                        <div class="staffAddInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                            <input type="text" id="staffAddFullName" name="full_name" placeholder="Enter full name" autocomplete="name" required>
                        </div>
                    </div>
                    <div class="staffAddFieldGroup">
                        <label for="staffAddUsername">Username <span>*</span></label>
                        <div class="staffAddInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                            <input type="text" id="staffAddUsername" name="username" placeholder="Enter username (e.g., juan.delacruz)" autocomplete="username" required>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Password and Role -->
            <div id="staffAddBottomFields">
                <div class="staffAddFieldGroup">
                    <label for="staffAddPassword">Password <span>*</span> <small>(minimum 10 characters)</small></label>
                    <div class="staffAddInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">lock</span>
                        <input type="password" id="staffAddPassword" name="password" placeholder="Enter password" autocomplete="new-password" required>
                        <button type="button" id="staffAddPasswordToggle" aria-label="Show password" aria-pressed="false">
                            <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                        </button>
                    </div>
                </div>
                <div class="staffAddFieldGroup">
                    <label for="staffAddRole">Role <span>*</span></label>
                    <div class="staffAddInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">deployed_code</span>
                        <select id="staffAddRole" name="role" required>
                            <option value="" selected disabled>Select role</option>
                            <option value="admin">Administrator</option>
                            <option value="cashier">Cashier</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div id="staffAddModalFooter">
                <button type="button" id="staffAddCancelButton">Cancel</button>
                <button type="submit" id="staffAddSaveButton">
                    <span class="material-symbols-outlined" aria-hidden="true">person_add</span>
                    Add Staff
                </button>
            </div>
        </form>
    </section>
</div>
