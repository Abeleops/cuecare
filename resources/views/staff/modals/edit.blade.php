
<!-- Edit Staff Modal -->
<div id="staffEditModalOverlay" class="staffModalOverlay" hidden>
    <section id="staffEditModal" role="dialog" aria-modal="true" aria-labelledby="staffEditModalTitle">
        <!-- Modal Header -->
        <div id="staffEditModalHeader">
            <span class="material-symbols-outlined staffEditHeaderIcon" aria-hidden="true">manage_accounts</span>
            <div id="staffEditModalHeading">
                <h2 id="staffEditModalTitle">Edit Staff</h2>
                <p>Update the staff account's information.</p>
            </div>
            <button type="button" id="staffEditModalClose" aria-label="Close Edit Staff modal">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </div>
        <!-- Edit Staff Form -->
        <form id="staffEditForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div id="staffEditModalBody">
                <!-- Profile Picture -->
                <div id="staffEditImageGroup">
                    <label for="staffEditImage">Profile Picture</label>
                    <label for="staffEditImage" id="staffEditImageUpload">
                        <span id="staffEditAvatarContainer">
                            <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Staff profile picture preview" id="staffEditImagePreview">
                            <span class="material-symbols-outlined" id="staffEditCameraIcon" aria-hidden="true">photo_camera</span>
                        </span>
                        <span>Change Photo</span>
                        <small>PNG, JPG, WebP (Max 2MB)</small>
                    </label>
                    <input type="file" id="staffEditImage" name="avatar" accept=".png,.jpg,.jpeg,.webp" hidden>
                </div>
                <!-- Staff Information -->
                <div id="staffEditFields">
                    <div class="staffEditFieldGroup">
                        <label for="staffEditFullName">Full Name <span>*</span></label>
                        <div class="staffEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                            <input type="text" id="staffEditFullName" name="full_name" placeholder="Enter full name" autocomplete="name" required>
                        </div>
                    </div>
                    <div class="staffEditFieldGroup">
                        <label for="staffEditUsername">Username <span>*</span></label>
                        <div class="staffEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">person</span>
                            <input type="text" id="staffEditUsername" name="username" value="juan.delacruz" placeholder="Enter username" autocomplete="username" required>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Password and Role -->
            <div id="staffEditBottomFields">
                <div class="staffEditFieldGroup">
                    <label for="staffEditPassword">Password</label>
                    <div class="staffEditInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">lock</span>
                        <input type="password" id="staffEditPassword" name="password" placeholder="Leave blank to keep current password" autocomplete="new-password">
                        <button type="button" id="staffEditPasswordToggle" aria-label="Show password" aria-pressed="false">
                            <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                        </button>
                    </div>
                </div>
                <div class="staffEditFieldGroup">
                    <label for="staffEditRole">Role <span>*</span></label>
                    <div class="staffEditInputContainer">
                        <span class="material-symbols-outlined" aria-hidden="true">deployed_code</span>
                        <select id="staffEditRole" name="role" required>
                            <option value="" disabled>Select role</option>
                            <option value="admin">Administrator</option>
                            <option value="cashier" selected>Cashier</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div id="staffEditModalFooter">
                <button type="button" id="staffEditCancelButton">Cancel</button>
                <button type="submit" id="staffEditSaveButton">
                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                    Save Changes
                </button>
            </div>
        </form>
    </section>
</div>
