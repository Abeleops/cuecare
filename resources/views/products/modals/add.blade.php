
<!-- Add Product Modal -->
<div id="productsAddModalOverlay" data-reopen="{{ $errors->productCreate->any() ? 'true' : 'false' }}" class="productsModalOverlay" hidden>
    <section id="productsAddModal" class="productsModal" role="dialog" aria-modal="true" aria-labelledby="productsAddModalTitle">
        <div id="productsAddModalHeader">
            <span class="material-symbols-outlined productsModalHeaderIcon" aria-hidden="true">deployed_code</span>
            <div id="productsAddModalHeading">
                <h2 id="productsAddModalTitle">Add Product</h2>
                <p>Add a new product to the inventory.</p>
            </div>
            <button type="button" id="productsAddModalClose" aria-label="Close Add Product modal">
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
        </div>
        <form id="productsAddForm" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            @if ($errors->productCreate->any())
                <div role="alert" tabindex="-1" data-validation-summary>
                    <p>Please correct the following</p>
                    <ul>@foreach ($errors->productCreate->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
                </div>
            @endif
            <div id="productsAddModalBody">
                <div id="productsAddImageGroup">
                    <label for="productsAddImage">Product Image</label>
                    <label for="productsAddImage" id="productsAddImageUpload">
                        <span class="material-symbols-outlined" aria-hidden="true">image</span>
                        <span>Upload Image</span>
                        <small>PNG, JPG, WebP (Max 2MB)</small>
                    </label>
                    <input type="file" id="productsAddImage" name="image" accept=".png,.jpg,.jpeg,.webp" hidden>
                            @error('image', 'productCreate') <p role="alert">{{ $message }}</p> @enderror
                </div>
                <div id="productsAddFields">
                    <div class="productsAddFieldGroup">
                        <label for="productsAddName">Product Name <span>*</span></label>
                        <div class="productsAddInputContainer">
                            <span class="material-symbols-outlined" aria-hidden="true">inventory_2</span>
                            <input type="text" id="productsAddName" name="name" placeholder="Enter product name" required value="{{ $errors->productCreate->any() ? old('name') : '' }}">
                        </div>
                        @error('name', 'productCreate') <p role="alert">{{ $message }}</p> @enderror
                    </div>
                    <div id="productsAddNumberFields">
                        <div class="productsAddFieldGroup">
                            <label for="productsAddPrice">Price (₱) <span>*</span></label>
                            <div class="productsAddInputContainer">
                                <span class="productsAddCurrencyIcon">₱</span>
                                <input type="number" id="productsAddPrice" name="price" placeholder="Enter price" min="0" step="0.01" required value="{{ $errors->productCreate->any() ? old('price') : '' }}">
                            </div>
                            @error('price', 'productCreate') <p role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div class="productsAddFieldGroup">
                            <label for="productsAddStock">Stock Quantity <span>*</span></label>
                            <div class="productsAddInputContainer">
                                <span class="material-symbols-outlined" aria-hidden="true">tag</span>
                                <input type="number" id="productsAddStock" name="stock_quantity" placeholder="Enter quantity" min="0" step="1" required value="{{ $errors->productCreate->any() ? old('stock_quantity') : '' }}">
                            </div>
                            @error('stock_quantity', 'productCreate') <p role="alert">{{ $message }}</p> @enderror
                        </div>
                        <div class="productsAddFieldGroup">
                            <label for="productsAddThreshold">Low Stock Threshold <span>*</span></label>
                            <div class="productsAddInputContainer">
                                <input type="number" id="productsAddThreshold" name="low_stock_threshold"
                                    min="0" step="1" required value="{{ $errors->productCreate->any() ? old('low_stock_threshold') : '10' }}">
                            </div>
                            @error('low_stock_threshold', 'productCreate') <p role="alert">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="productsAddFieldGroup">
                        <label for="productsAddCategory">Category <span>*</span></label>
                        <div class="productsCategorySelect">
                            <span class="material-symbols-outlined" aria-hidden="true">sell</span>
                            <select id="productsAddCategory" name="category_id" required>
                                <option value="">Select category</option>
                                @foreach ($availableCategories as $category)
                                    <option value="{{ $category->id }}" @selected($errors->productCreate->any() && (string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('category_id', 'productCreate') <p role="alert">{{ $message }}</p> @enderror
                        @include('products.modals.category-creator', ['context' => 'Add'])
                    </div>
                </div>
            </div>
            <div id="productsAddModalFooter">
                <button type="button" id="productsAddCancelButton">Cancel</button>
                <button type="submit" id="productsAddSaveButton">
                    <span class="material-symbols-outlined" aria-hidden="true">add</span>
                    Add Product
                </button>
            </div>
        </form>
    </section>
</div>
