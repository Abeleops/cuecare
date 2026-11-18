
<!-- Edit Product Modal -->
<div id="productsEditModalOverlay" class="productsModalOverlay" hidden>
    <section id="productsEditModal" class="productsModal" role="dialog" aria-modal="true" aria-labelledby="productsEditModalTitle">
        <div id="productsEditModalHeader">
            <span class="material-symbols-outlined productsEditHeaderIcon" aria-hidden="true">edit</span>
            <div id="productsEditModalHeading">
                <h2 id="productsEditModalTitle">Edit Product</h2>
                <p>Update the product details.</p>
            </div>
            <button type="button" id="productsEditModalClose" aria-label="Close Edit Product modal">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </div>
        <form id="productsEditForm" method="POST" action="" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div id="productsEditModalBody">
                <!-- Product Image -->
                <div id="productsEditImageGroup">
                    <label for="productsEditImage">Product Image</label>
                    <div id="productsEditImageContainer">
                        <img src="{{ asset('images/noImagePlaceholderWhite.png') }}" alt="Product image preview" id="productsEditImagePreview">
                        <div id="productsEditImageActions">
                            <label for="productsEditImage" id="productsEditImageButton" aria-label="Change product image" title="Change image">
                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                            </label>
                            <button type="button" id="productsEditRemoveImage" aria-label="Remove product image" title="Remove image">
                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                            </button>
                        </div>
                    </div>
                    <input type="file" id="productsEditImage" name="image" accept=".png,.jpg,.jpeg" hidden>
                </div>
                <!-- Product Fields -->
                <div id="productsEditFields">
                    <div class="productsEditFieldGroup">
                        <label for="productsEditName">Product Name <span>*</span></label>
                        <div class="productsEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                            <input type="text" id="productsEditName" name="name" placeholder="Enter product name" required>
                        </div>
                    </div>
                    <div class="productsEditFieldGroup">
                        <label for="productsEditCategory">Category <span>*</span></label>
                        <div class="productsEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">sell</span>
                            <select id="productsEditCategory" name="category_id" required>
                                <option value="">Select category</option>
                                @foreach(($categories ?? collect())->flatMap(fn ($category) => $category->children) as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="productsEditNumberFields">
                        <div class="productsEditFieldGroup">
                            <label for="productsEditPrice">Price (₱) <span>*</span></label>
                            <div class="productsEditInputContainer">
                                <span class="productsEditCurrencyIcon">₱</span>
                                <input type="number" id="productsEditPrice" name="price" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="productsEditFieldGroup">
                            <label for="productsEditThreshold">Low stock threshold <span>*</span></label>
                            <div class="productsEditInputContainer">
                                <input type="number" id="productsEditThreshold" name="low_stock_threshold" min="0" step="1" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div id="productsEditModalFooter">
                <button type="button" id="productsEditCancelButton">Cancel</button>
                <button type="submit" id="productsEditSaveButton">
                    <span class="material-symbols-outlined" aria-hidden="true">check</span>
                    Save Changes
                </button>
            </div>
        </form>
    </section>
</div>
