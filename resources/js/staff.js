/* =======================
   Staff - Add Staff Modal
   ======================= */

document.addEventListener("DOMContentLoaded", function () {
    const addButton = document.getElementById("staffAddButton");
    const modalOverlay = document.getElementById("staffAddModalOverlay");
    const closeButton = document.getElementById("staffAddModalClose");
    const cancelButton = document.getElementById("staffAddCancelButton");
    const addForm = document.getElementById("staffAddForm");

    const fullNameInput = document.getElementById("staffAddFullName");
    const imageInput = document.getElementById("staffAddImage");
    const avatarContainer = document.getElementById("staffAddAvatarContainer");
    const passwordInput = document.getElementById("staffAddPassword");
    const passwordToggle = document.getElementById("staffAddPasswordToggle");

    if (
        !addButton ||
        !modalOverlay ||
        !closeButton ||
        !cancelButton ||
        !addForm ||
        !fullNameInput ||
        !imageInput ||
        !avatarContainer ||
        !passwordInput ||
        !passwordToggle
    ) {
        return;
    }

    let previousFocus = null;
    let previewUrl = null;

    // Reset Profile Picture
    function resetImagePreview() {
        const preview = avatarContainer.querySelector(".staffAddImagePreview");

        if (preview) {
            preview.remove();
        }

        avatarContainer.querySelector(".staffAddAvatarIcon").hidden = false;
        imageInput.value = "";

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }
    }

    // Reset Password Visibility
    function resetPasswordVisibility() {
        passwordInput.type = "password";
        passwordToggle.setAttribute("aria-label", "Show password");
        passwordToggle.setAttribute("aria-pressed", "false");

        const icon = passwordToggle.querySelector(".material-symbols-outlined");

        if (icon) {
            icon.textContent = "visibility";
        }
    }

    // Open Add Staff Modal
    function openModal() {
        previousFocus = document.activeElement;
        modalOverlay.hidden = false;
        document.body.classList.add("staffModalOpen");
        fullNameInput.focus();
    }

    // Close Add Staff Modal
    function closeModal() {
        modalOverlay.hidden = true;
        document.body.classList.remove("staffModalOpen");

        addForm.reset();
        resetImagePreview();
        resetPasswordVisibility();

        if (previousFocus && previousFocus.isConnected) {
            previousFocus.focus();
        }
    }

    // Open Modal
    addButton.addEventListener("click", openModal);

    // Close Modal Buttons
    closeButton.addEventListener("click", closeModal);
    cancelButton.addEventListener("click", closeModal);

    // Close When Clicking Outside Modal
    modalOverlay.addEventListener("click", function (event) {
        if (event.target === modalOverlay) {
            closeModal();
        }
    });

    // Close With Escape Key
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !modalOverlay.hidden) {
            closeModal();
        }
    });

    // Profile Picture Preview
    imageInput.addEventListener("change", function () {
        const file = imageInput.files[0];

        if (!file) {
            return;
        }

        const allowedTypes = ["image/png", "image/jpeg"];
        const maxSize = 2 * 1024 * 1024;

        if (!allowedTypes.includes(file.type) || file.size > maxSize) {
            alert("Please upload a PNG or JPG image up to 2MB.");
            imageInput.value = "";
            return;
        }

        resetImagePreview();

        previewUrl = URL.createObjectURL(file);

        const preview = document.createElement("img");
        preview.src = previewUrl;
        preview.alt = "Staff profile picture preview";
        preview.className = "staffAddImagePreview";

        avatarContainer.appendChild(preview);
        avatarContainer.querySelector(".staffAddAvatarIcon").hidden = true;
    });

    // Show or Hide Password
    passwordToggle.addEventListener("click", function () {
        const isPasswordHidden = passwordInput.type === "password";
        const icon = passwordToggle.querySelector(".material-symbols-outlined");

        passwordInput.type = isPasswordHidden ? "text" : "password";

        passwordToggle.setAttribute(
            "aria-label",
            isPasswordHidden ? "Hide password" : "Show password",
        );

        passwordToggle.setAttribute("aria-pressed", String(isPasswordHidden));

        if (icon) {
            icon.textContent = isPasswordHidden
                ? "visibility_off"
                : "visibility";
        }
    });

    // Demo Form Submission
    addForm.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!addForm.reportValidity()) {
            return;
        }

        addForm.submit();
    });
});

/* =======================
   Staff - Edit Staff Modal
   ======================= */

document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".staffEditButton");
    const editOverlay = document.getElementById("staffEditModalOverlay");
    const closeButton = document.getElementById("staffEditModalClose");
    const cancelButton = document.getElementById("staffEditCancelButton");
    const editForm = document.getElementById("staffEditForm");

    const fullNameInput = document.getElementById("staffEditFullName");
    const usernameInput = document.getElementById("staffEditUsername");
    const passwordInput = document.getElementById("staffEditPassword");
    const passwordToggle = document.getElementById("staffEditPasswordToggle");
    const roleSelect = document.getElementById("staffEditRole");

    const imageInput = document.getElementById("staffEditImage");
    const imagePreview = document.getElementById("staffEditImagePreview");

    if (
        !editOverlay ||
        !closeButton ||
        !cancelButton ||
        !editForm ||
        !fullNameInput ||
        !usernameInput ||
        !passwordInput ||
        !passwordToggle ||
        !roleSelect ||
        !imageInput ||
        !imagePreview
    ) {
        return;
    }

    const defaultImage = imagePreview.src;
    let previousFocus = null;
    let previewUrl = null;

    // Reset Password Visibility
    function resetPasswordVisibility() {
        passwordInput.type = "password";
        passwordInput.value = "";

        passwordToggle.setAttribute("aria-label", "Show password");
        passwordToggle.setAttribute("aria-pressed", "false");

        const icon = passwordToggle.querySelector(".material-symbols-outlined");

        if (icon) {
            icon.textContent = "visibility";
        }
    }

    // Reset Image Preview
    function resetImagePreview() {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        imageInput.value = "";
        imagePreview.src = defaultImage;
    }

    // Open Edit Staff Modal
    function openEditModal(button) {
        if (button.dataset.userId) {
            editForm.action = `/staff/${button.dataset.userId}`;
        }
        const row = button.closest("tr");

        if (!row) {
            return;
        }

        const cells = row.querySelectorAll("td");

        if (cells.length < 6) {
            return;
        }

        // Get Staff Information From Selected Row
        fullNameInput.value = cells[2].textContent.trim();
        usernameInput.value = cells[3].textContent.trim();

        const staffRole = button.dataset.roleKey || cells[4].textContent.trim().toLowerCase();

        const matchingRole = Array.from(roleSelect.options).find(
            function (option) {
                return option.value === staffRole;
            },
        );

        roleSelect.value = matchingRole ? matchingRole.value : "";

        resetPasswordVisibility();
        resetImagePreview();

        // Use Staff Photo If Available
        const staffPhoto = cells[0].querySelector("img");

        if (staffPhoto) {
            imagePreview.src = staffPhoto.src;
        }

        previousFocus = document.activeElement;

        editOverlay.hidden = false;
        document.body.classList.add("staffModalOpen");

        fullNameInput.focus();
    }

    // Close Edit Staff Modal
    function closeEditModal() {
        editOverlay.hidden = true;
        document.body.classList.remove("staffModalOpen");

        editForm.reset();
        resetPasswordVisibility();
        resetImagePreview();

        if (previousFocus && previousFocus.isConnected) {
            previousFocus.focus();
        }
    }

    // Open Modal From Edit Buttons
    editButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            openEditModal(button);
        });
    });

    // Close Buttons
    closeButton.addEventListener("click", closeEditModal);
    cancelButton.addEventListener("click", closeEditModal);

    // Close When Clicking Outside Modal
    editOverlay.addEventListener("click", function (event) {
        if (event.target === editOverlay) {
            closeEditModal();
        }
    });

    // Close With Escape Key
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !editOverlay.hidden) {
            closeEditModal();
        }
    });

    // Show or Hide Password
    passwordToggle.addEventListener("click", function () {
        const isHidden = passwordInput.type === "password";
        const icon = passwordToggle.querySelector(".material-symbols-outlined");

        passwordInput.type = isHidden ? "text" : "password";

        passwordToggle.setAttribute(
            "aria-label",
            isHidden ? "Hide password" : "Show password",
        );

        passwordToggle.setAttribute("aria-pressed", String(isHidden));

        if (icon) {
            icon.textContent = isHidden ? "visibility_off" : "visibility";
        }
    });

    // Change Profile Picture
    imageInput.addEventListener("change", function () {
        const file = imageInput.files[0];

        if (!file) {
            return;
        }

        const allowedTypes = ["image/png", "image/jpeg"];
        const maxSize = 2 * 1024 * 1024;

        if (!allowedTypes.includes(file.type) || file.size > maxSize) {
            alert("Please upload a PNG or JPG image up to 2MB.");
            imageInput.value = "";
            return;
        }

        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
        }

        previewUrl = URL.createObjectURL(file);
        imagePreview.src = previewUrl;
    });

    // Demo Save Changes
    editForm.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!editForm.reportValidity()) {
            return;
        }

        editForm.submit();
    });
});
