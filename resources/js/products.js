document.addEventListener("DOMContentLoaded", function () {
    const addButton = document.getElementById("productsAddButton");
    const modalOverlay = document.getElementById("productsAddModalOverlay");
    const closeButton = document.getElementById("productsAddModalClose");
    const cancelButton = document.getElementById("productsAddCancelButton");
    const addForm = document.getElementById("productsAddForm");
    const imageInput = document.getElementById("productsAddImage");
    const imageUpload = document.getElementById("productsAddImageUpload");

    if (!addButton || !modalOverlay) {
        return;
    }

    let previousFocus = null;

    // Open Add Product Modal
    function openModal() {
        previousFocus = document.activeElement;
        modalOverlay.hidden = false;
        document.body.classList.add("productsModalOpen");
        document.getElementById("productsAddName").focus();
    }

    // Close Add Product Modal
    function closeModal() {
        modalOverlay.hidden = true;
        document.body.classList.remove("productsModalOpen");
        addForm.reset();
        resetImagePreview();

        if (previousFocus) {
            previousFocus.focus();
        }
    }

    // Reset Image Preview
    function resetImagePreview() {
        const preview = imageUpload.querySelector(".productsImagePreview");

        if (preview) {
            preview.remove();
        }

        imageUpload.querySelectorAll("span, small").forEach(function (element) {
            element.hidden = false;
        });
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

    // Image Upload Preview
    imageInput.addEventListener("change", function () {
        const file = imageInput.files[0];

        resetImagePreview();

        if (!file) {
            return;
        }

        const allowedTypes = ["image/png", "image/jpeg", "image/webp"];
        const maxSize = 2 * 1024 * 1024;

        if (!allowedTypes.includes(file.type) || file.size > maxSize) {
            alert("Please upload a PNG, JPG or WebP image up to 2MB.");
            imageInput.value = "";
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            const preview = document.createElement("img");
            preview.src = event.target.result;
            preview.alt = "Product image preview";
            preview.className = "productsImagePreview";

            imageUpload
                .querySelectorAll("span, small")
                .forEach(function (element) {
                    element.hidden = true;
                });

            imageUpload.appendChild(preview);
        };

        reader.readAsDataURL(file);
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

// Add a category without losing the product form
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-category-creator]").forEach((creator) => {
        const toggle = creator.querySelector(".productsCategoryToggle");
        const fields = creator.querySelector(".productsCategoryFields");
        const name = creator.querySelector("[data-category-name]");
        const group = creator.querySelector("[data-category-group]");
        const message = creator.querySelector("[data-category-message]");
        const result = creator.querySelector("[data-category-result]");
        const save = creator.querySelector("[data-category-save]");
        const cancel = creator.querySelector("[data-category-cancel]");
        const form = creator.closest("form");

        function closeFields() {
            fields.hidden = true;
            toggle.setAttribute("aria-expanded", "false");
            name.value = "";
            message.textContent = "";
            toggle.focus();
        }

        toggle.addEventListener("click", () => {
            if (!fields.hidden) {
                closeFields();
                return;
            }
            fields.hidden = false;
            toggle.setAttribute("aria-expanded", "true");
            result.textContent = "";
            name.focus();
        });
        cancel.addEventListener("click", closeFields);
        form.addEventListener("reset", () => {
            fields.hidden = true;
            toggle.setAttribute("aria-expanded", "false");
            message.textContent = "";
            result.textContent = "";
        });
        save.addEventListener("click", async () => {
            const categoryName = name.value.trim();
            if (!categoryName) {
                message.textContent = "Enter a category name";
                name.focus();
                return;
            }

            save.disabled = true;
            cancel.disabled = true;
            message.textContent = "Saving category…";
            try {
                const response = await fetch(creator.dataset.url, {
                    method: "POST",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": form.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({ name: categoryName, category_group: group.value }),
                });
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.errors?.name?.[0] || data.errors?.category_group?.[0] || data.message || "Could not add category");
                }
                document.querySelectorAll("#productsAddCategory, #productsEditCategory, #productsCategorySelect").forEach((select) => {
                    select.add(new Option(data.name, String(data.id)));
                });
                form.querySelector('select[name="category_id"]').value = String(data.id);
                closeFields();
                result.textContent = "Category added and selected";
            } catch (error) {
                message.textContent = error.message || "Could not add category";
            } finally {
                save.disabled = false;
                cancel.disabled = false;
            }
        });
    });
});

/* =======================
   Products - Edit Product Modal
   ======================= */

document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".productsEditButton");
    const editOverlay = document.getElementById("productsEditModalOverlay");
    const closeButton = document.getElementById("productsEditModalClose");
    const cancelButton = document.getElementById("productsEditCancelButton");
    const editForm = document.getElementById("productsEditForm");

    const nameInput = document.getElementById("productsEditName");
    const priceInput = document.getElementById("productsEditPrice");
    const thresholdInput = document.getElementById("productsEditThreshold");
    const stockInput = document.getElementById("productsEditStockQuantity");
    const categorySelect = document.getElementById("productsEditCategory");

    const imageInput = document.getElementById("productsEditImage");
    const removeImageFlag = document.getElementById("productsRemoveImageFlag");
    const imagePreview = document.getElementById("productsEditImagePreview");
    const removeImageButton = document.getElementById(
        "productsEditRemoveImage",
    );

    if (!editOverlay || !editForm) {
        return;
    }

    const defaultImage = imagePreview.src;
    let previousFocus = null;

    // Open Edit Modal
    editButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            if (editOverlay.dataset.reopen !== "true") {
                editForm.querySelectorAll('[role="alert"]').forEach((alert) => alert.remove());
            }
            if (button.dataset.productId) {
                editForm.action = button.dataset.updateUrl;
                editForm.elements._edit_id.value = button.dataset.productId;
            }
            const row = button.closest("tr");

            if (!row) {
                return;
            }

            const cells = row.querySelectorAll("td");

            const productName = row.querySelector(".productsProductInfo span");
            const productImage = row.querySelector(".productsProductImage");

            nameInput.value = productName ? productName.textContent.trim() : "";
            priceInput.value = cells[3]
                ? cells[3].textContent.replace(/[^\d.]/g, "")
                : "";
            thresholdInput.value = button.dataset.lowStockThreshold;
            if (stockInput) {
                stockInput.value = button.dataset.stockQuantity;
                editForm.elements.original_stock.value = button.dataset.stockQuantity;
            }
            categorySelect.value = button.dataset.categoryId;

            imagePreview.src = productImage ? productImage.src : defaultImage;
            imageInput.value = "";
            removeImageFlag.value = "0";

            previousFocus = document.activeElement;
            editOverlay.hidden = false;
            document.body.classList.add("productsModalOpen");
            nameInput.focus();
        });
    });

    // Close Edit Modal
    function closeEditModal() {
        editOverlay.hidden = true;
        document.body.classList.remove("productsModalOpen");
        editForm.reset();

        if (previousFocus) {
            previousFocus.focus();
        }
    }

    closeButton.addEventListener("click", closeEditModal);
    cancelButton.addEventListener("click", closeEditModal);

    // Close When Clicking Outside
    editOverlay.addEventListener("click", function (event) {
        if (event.target === editOverlay) {
            closeEditModal();
        }
    });

    // Close With Escape
    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape" && !editOverlay.hidden) {
            closeEditModal();
        }
    });

    // Change Product Image
    imageInput.addEventListener("change", function () {
        const file = imageInput.files[0];

        if (!file) {
            return;
        }

        if (
            !["image/png", "image/jpeg", "image/webp"].includes(file.type) ||
            file.size > 2 * 1024 * 1024
        ) {
            alert("Please select a PNG, JPG or WebP image up to 2MB.");
            imageInput.value = "";
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            imagePreview.src = event.target.result;
        };

        removeImageFlag.value = "0";
        reader.readAsDataURL(file);
    });

    // Remove Product Image
    removeImageButton.addEventListener("click", function () {
        imagePreview.src = defaultImage;
        imageInput.value = "";
        removeImageFlag.value = "1";
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
    const add = document.getElementById("productsAddModalOverlay");
    const edit = document.getElementById("productsEditModalOverlay");
    if (add?.dataset.reopen === "true") {
        add.hidden = false;
        document.body.classList.add("productsModalOpen");
        add.querySelector("[data-validation-summary]")?.focus();
    }
    if (edit?.dataset.reopen === "true") {
        const form = edit.querySelector("form");
        const button = Array.from(document.querySelectorAll(".productsEditButton"))
            .find((item) => item.dataset.productId === edit.dataset.editId);
        const values = Array.from(form.elements).filter((element) => element.name && element.type !== "file")
            .map((element) => [element, element.value]);
        button?.click();
        values.forEach(([element, value]) => { element.value = value; });
        form.action = edit.dataset.updateUrl.replace("__ID__", encodeURIComponent(edit.dataset.editId));
        edit.hidden = false;
        document.body.classList.add("productsModalOpen");
        edit.querySelector("[data-validation-summary]")?.focus();
        edit.dataset.reopen = "false";
    }
});
