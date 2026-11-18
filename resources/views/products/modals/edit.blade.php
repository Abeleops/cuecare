
<!-- Edit Product Modal -->
<div id="productsEditModalOverlay" data-reopen="{{ $errors->productEdit->any() ? 'true' : 'false' }}" data-edit-id="{{ old('_edit_id') }}" data-update-url="{{ route('products.update', ['product' => '__ID__']) }}" class="productsModalOverlay" hidden>
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
            @if ($errors->productEdit->any())
                <div role="alert" tabindex="-1" data-validation-summary>
                    <p>Please correct the following</p>
                    <ul>@foreach ($errors->productEdit->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
                </div>
            @endif
            @method('PUT')
            <input type="hidden" name="_edit_id" value="{{ old('_edit_id') }}">
            <input type="hidden" name="remove_image" id="productsRemoveImageFlag" value="{{ old('remove_image', '0') }}">
            @can('InventoryAdjust')
                <input type="hidden" name="original_stock" value="{{ old('original_stock') }}">
            @endcan
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
                    <input type="file" id="productsEditImage" name="image" accept=".png,.jpg,.jpeg,.webp" hidden>
                            @error('image', 'productEdit') <p role="alert">{{ $message }}</p> @enderror
                </div>
                <!-- Product Fields -->
                <div id="productsEditFields">
                    <div class="productsEditFieldGroup">
                        <label for="productsEditName">Product Name <span>*</span></label>
                        <div class="productsEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                            <input type="text" id="productsEditName" name="name" placeholder="Enter product name" required value="{{ $errors->productEdit->any() ? old('name') : '' }}">
                        </div>
                        @error('name', 'productEdit') <p role="alert">{{ $message }}</p> @enderror
                    </div>
                    <div class="productsEditFieldGroup">
                        <label for="productsEditCategory">Category <span>*</span></label>
                        <div class="productsEditInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">sell</span>
                            <select id="productsEditCategory" name="category_id" required>
                                <option value="">Select category</option>
                                @foreach ($availableCategories as $category)
                                    <option value="{{ $category->id }}" @selected($errors->productEdit->any() && (string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('category_id', 'productEdit') <p role="alert">{{ $message }}</p> @enderror
                        @include('products.modals.category-creator', ['context' => 'Edit'])
                    </div>
                    <div id="productsEditNumberFields">
                        <div class="productsEditFieldGroup">
                            <label for="productsEditPrice">Price (₱) <span>*</span></label>
                            <div class="productsEditInputContainer">
                                <span class="productsEditCurrencyIcon">₱</span>
                                <input type="number" id="productsEditPrice" name="price" min="0" step="0.01" required value="{{ $errors->productEdit->any() ? old('price') : '' }}">
                            </div>
                            @error('price', 'productEdit') <p role="alert">{{ $message }}</p> @enderror
                        </div>
                        @can('InventoryAdjust')
                            <div class="productsEditFieldGroup">
                                <label for="productsEditStockQuantity">Stock quantity <span>*</span></label>
                                <div class="productsEditInputContainer">
                                    <input type="number" id="productsEditStockQuantity" name="stock_quantity" min="0" max="2147483647" step="1" required value="{{ $errors->productEdit->any() ? old('stock_quantity') : '' }}">
                                </div>
                                @error('stock_quantity', 'productEdit') <p role="alert">{{ $message }}</p> @enderror
                                @error('original_stock', 'productEdit') <p role="alert">{{ $message }}</p> @enderror
                            </div>
                        @endcan
                        <div class="productsEditFieldGroup">
                            <label for="productsEditThreshold">Low stock threshold <span>*</span></label>
                            <div class="productsEditInputContainer">
                                <input type="number" id="productsEditThreshold" name="low_stock_threshold" min="0" step="1" required value="{{ $errors->productEdit->any() ? old('low_stock_threshold') : '' }}">
                            </div>
                            @error('low_stock_threshold', 'productEdit') <p role="alert">{{ $message }}</p> @enderror
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
