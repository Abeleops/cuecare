/* =======================
   Customers - Add Customer Modal
   ======================= */

document.addEventListener("DOMContentLoaded", function () {
    const addButton = document.getElementById("customersAddButton");
    const modalOverlay = document.getElementById("customersAddModalOverlay");
    const closeButton = document.getElementById("customersAddModalClose");
    const cancelButton = document.getElementById("customersAddCancelButton");
    const addForm = document.getElementById("customersAddForm");
    const nameInput = document.getElementById("customersAddName");

    if (
        !addButton ||
        !modalOverlay ||
        !closeButton ||
        !cancelButton ||
        !addForm ||
        !nameInput
    ) {
        return;
    }

    let previousFocus = null;

    // Open Add Customer Modal
    function openModal() {
        previousFocus = document.activeElement;
        modalOverlay.hidden = false;
        document.body.classList.add("customersModalOpen");
        nameInput.focus();
    }

    // Close Add Customer Modal
    function closeModal() {
        modalOverlay.hidden = true;
        document.body.classList.remove("customersModalOpen");
        addForm.reset();

        if (previousFocus) {
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
   Customers - Edit Customer Modal
   ======================= */

document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".customersEditButton");
    const editOverlay = document.getElementById("customersEditModalOverlay");
    const closeButton = document.getElementById("customersEditModalClose");
    const cancelButton = document.getElementById("customersEditCancelButton");
    const editForm = document.getElementById("customersEditForm");

    const nameInput = document.getElementById("customersEditName");
    const phoneInput = document.getElementById("customersEditPhone");
    const emailInput = document.getElementById("customersEditEmail");

    if (
        !editOverlay ||
        !closeButton ||
        !cancelButton ||
        !editForm ||
        !nameInput ||
        !phoneInput ||
        !emailInput
    ) {
        return;
    }

    let previousFocus = null;

    // Open Edit Customer Modal
    function openEditModal(button) {
        if (button.dataset.customerId) {
            editForm.action = `/customers/${button.dataset.customerId}`;
        }
        const row = button.closest("tr");

        if (!row) {
            return;
        }

        const cells = row.querySelectorAll("td");

        if (cells.length < 4) {
            return;
        }

        // Get Customer Information From Selected Row
        nameInput.value = cells[1].textContent.trim();
        phoneInput.value = cells[2].textContent.trim();
        emailInput.value = cells[3].textContent.trim();

        previousFocus = document.activeElement;

        editOverlay.hidden = false;
        document.body.classList.add("customersModalOpen");

        nameInput.focus();
    }

    // Close Edit Customer Modal
    function closeEditModal() {
        editOverlay.hidden = true;
        document.body.classList.remove("customersModalOpen");

        editForm.reset();

        if (previousFocus && previousFocus.isConnected) {
            previousFocus.focus();
        }
    }

    // Edit Buttons
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

    // Demo Save Changes
    editForm.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!editForm.reportValidity()) {
            return;
        }

        editForm.submit();
    });
});
