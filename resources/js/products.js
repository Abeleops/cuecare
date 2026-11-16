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

        const allowedTypes = ["image/png", "image/jpeg"];
        const maxSize = 2 * 1024 * 1024;

        if (!allowedTypes.includes(file.type) || file.size > maxSize) {
            alert("Please upload a PNG or JPG image up to 2MB.");
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

        alert("Product form validated! Demo only — no product was saved.");

        closeModal();
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
    const stockInput = document.getElementById("productsEditStock");
    const categorySelect = document.getElementById("productsEditCategory");

    const decreaseButton = document.getElementById("productsEditDecreaseStock");
    const increaseButton = document.getElementById("productsEditIncreaseStock");

    const imageInput = document.getElementById("productsEditImage");
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
            const row = button.closest("tr");

            if (!row) {
                return;
            }

            const cells = row.querySelectorAll("td");

            const productName = row.querySelector(".productsProductInfo span");
            const productImage = row.querySelector(".productsProductImage");
            const productCategory = row.querySelector(".productsCategoryBadge");

            nameInput.value = productName ? productName.textContent.trim() : "";
            priceInput.value = cells[3]
                ? cells[3].textContent.replace(/[^\d.]/g, "")
                : "";
            stockInput.value = cells[4] ? cells[4].textContent.trim() : "0";

            if (productCategory) {
                const categoryName = productCategory.textContent.trim();

                const matchingOption = Array.from(categorySelect.options).find(
                    function (option) {
                        return option.textContent.trim() === categoryName;
                    },
                );

                categorySelect.value = matchingOption
                    ? matchingOption.value
                    : "";
            }

            imagePreview.src = productImage ? productImage.src : defaultImage;
            imageInput.value = "";

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

    // Decrease Stock
    decreaseButton.addEventListener("click", function () {
        const currentStock = Number(stockInput.value) || 0;
        stockInput.value = Math.max(0, currentStock - 1);
    });

    // Increase Stock
    increaseButton.addEventListener("click", function () {
        const currentStock = Number(stockInput.value) || 0;
        stockInput.value = currentStock + 1;
    });

    // Change Product Image
    imageInput.addEventListener("change", function () {
        const file = imageInput.files[0];

        if (!file) {
            return;
        }

        if (
            !["image/png", "image/jpeg"].includes(file.type) ||
            file.size > 2 * 1024 * 1024
        ) {
            alert("Please select a PNG or JPG image up to 2MB.");
            imageInput.value = "";
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            imagePreview.src = event.target.result;
        };

        reader.readAsDataURL(file);
    });

    // Remove Product Image
    removeImageButton.addEventListener("click", function () {
        imagePreview.src = defaultImage;
        imageInput.value = "";
    });

    // Demo Save Changes
    editForm.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!editForm.reportValidity()) {
            return;
        }

        alert("Product changes validated! Demo only — no changes were saved.");

        closeEditModal();
    });
});
