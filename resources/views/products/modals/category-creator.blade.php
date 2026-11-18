<div class="productsCategoryCreator" data-category-creator data-url="{{ route('categories.store') }}">
    <button type="button" class="productsCategoryToggle" aria-expanded="false" aria-controls="products{{ $context }}NewCategory">
        <span class="material-symbols-outlined" aria-hidden="true">add</span>
        Add category
    </button>
    <span class="productsCategoryResult" role="status" aria-live="polite" data-category-result></span>
    <div id="products{{ $context }}NewCategory" class="productsCategoryFields" hidden>
        <label for="products{{ $context }}CategoryName">Category name</label>
        <input id="products{{ $context }}CategoryName" type="text" maxlength="100" placeholder="e.g. Coffee" data-category-name>
        <label for="products{{ $context }}CategoryGroup">Group</label>
        <select id="products{{ $context }}CategoryGroup" data-category-group>
            <option value="food">Food</option>
            <option value="beverage">Beverages</option>
            <option value="alcoholic">Alcoholic Drinks</option>
        </select>
        <p class="productsCategoryMessage" role="status" aria-live="polite" data-category-message></p>
        <div class="productsCategoryActions">
            <button type="button" data-category-cancel>Cancel</button>
            <button type="button" data-category-save>Save category</button>
        </div>
    </div>
</div>
