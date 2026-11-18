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
            editForm.action = button.dataset.updateUrl;
            editForm.elements._edit_id.value = button.dataset.customerId;
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
        nameInput.value = button.dataset.name;
        phoneInput.value = button.dataset.phone;
        emailInput.value = button.dataset.email;

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

// Restore the form that failed server validation
document.addEventListener("DOMContentLoaded", () => {
    const add = document.getElementById("customersAddModalOverlay");
    const edit = document.getElementById("customersEditModalOverlay");
    if (add?.dataset.reopen === "true") {
        add.hidden = false;
        document.body.classList.add("customersModalOpen");
        add.querySelector("[data-validation-summary]")?.focus();
    }
    if (edit?.dataset.reopen === "true") {
        const form = edit.querySelector("form");
        const button = Array.from(document.querySelectorAll(".customersEditButton"))
            .find((item) => item.dataset.customerId === edit.dataset.editId);
        const values = Array.from(form.elements).filter((element) => element.name && element.type !== "file")
            .map((element) => [element, element.value]);
        button?.click();
        values.forEach(([element, value]) => { element.value = value; });
        form.action = edit.dataset.updateUrl.replace("__ID__", encodeURIComponent(edit.dataset.editId));
        edit.hidden = false;
        document.body.classList.add("customersModalOpen");
        edit.querySelector("[data-validation-summary]")?.focus();
    }
});
