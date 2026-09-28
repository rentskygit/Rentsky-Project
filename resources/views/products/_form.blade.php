@php
    $isEdit = isset($product);
    $old = fn($key, $default = '') => old($key, $default);
@endphp

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-info-circle"></i> Información básica
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label" for="name">Nombre del producto *</label>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ $old('name', $isEdit ? $product['name'] : '') }}"
                       placeholder="Ej: Camiseta negra básica"
                       required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label" for="sku">SKU *</label>
                <input type="text"
                       id="sku"
                       name="sku"
                       class="form-control @error('sku') is-invalid @enderror"
                       value="{{ $old('sku', $isEdit ? $product['sku'] : '') }}"
                       placeholder="Ej: CAM-NEG-001"
                       required>
                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label" for="category_id">Categoría *</label>
                <select id="category_id"
                        name="category_id"
                        class="form-control @error('category_id') is-invalid @enderror"
                        required>
                    <option value="">— Selecciona una categoría —</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ $old('category_id', $isEdit ? ($product['category']['id'] ?? '') : '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label" for="slug">Slug (opcional)</label>
                <input type="text"
                       id="slug"
                       name="slug"
                       class="form-control @error('slug') is-invalid @enderror"
                       value="{{ $old('slug', $isEdit ? $product['slug'] : '') }}"
                       placeholder="Se genera automáticamente si lo dejas vacío">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="description">Descripción</label>
        <textarea id="description"
                  name="description"
                  class="form-control @error('description') is-invalid @enderror"
                  rows="4"
                  placeholder="Describe el producto...">{{ $old('description', $isEdit ? $product['description'] : '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-currency-dollar"></i> Precios y stock
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="price">Precio de venta *</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number"
                           id="price"
                           name="price"
                           step="0.01"
                           min="0"
                           class="form-control @error('price') is-invalid @enderror"
                           value="{{ $old('price', $isEdit ? $product['price_raw'] : '') }}"
                           placeholder="0.00"
                           required>
                </div>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="original_price">Precio original (antes)</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number"
                           id="original_price"
                           name="original_price"
                           step="0.01"
                           min="0"
                           class="form-control @error('original_price') is-invalid @enderror"
                           value="{{ $old('original_price', $isEdit ? $product['original_price_raw'] : '') }}"
                           placeholder="Opcional">
                </div>
                <small class="text-muted">Si lo llenas, se mostrará como precio tachado.</small>
                @error('original_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="stock">Stock disponible *</label>
                <input type="number"
                       id="stock"
                       name="stock"
                       min="0"
                       step="1"
                       class="form-control @error('stock') is-invalid @enderror"
                       value="{{ $old('stock', $isEdit ? $product['stock'] : 0) }}"
                       required>
                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-tags"></i> Estado y etiquetas
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="status">Estado *</label>
                <select id="status"
                        name="status"
                        class="form-control @error('status') is-invalid @enderror"
                        required>
                    @php $currentStatus = $old('status', $isEdit ? $product['status'] : 'active'); @endphp
                    <option value="active"       {{ $currentStatus === 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive"     {{ $currentStatus === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    <option value="out_of_stock" {{ $currentStatus === 'out_of_stock' ? 'selected' : '' }}>Sin stock</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="badge">Etiqueta</label>
                <select id="badge"
                        name="badge"
                        class="form-control @error('badge') is-invalid @enderror">
                    @php $currentBadge = $old('badge', $isEdit ? $product['badge'] : 'none'); @endphp
                    <option value="none"        {{ $currentBadge === 'none' ? 'selected' : '' }}>Ninguna</option>
                    <option value="sale"        {{ $currentBadge === 'sale' ? 'selected' : '' }}>Oferta</option>
                    <option value="liquidation" {{ $currentBadge === 'liquidation' ? 'selected' : '' }}>Liquidación</option>
                    <option value="new"         {{ $currentBadge === 'new' ? 'selected' : '' }}>Nuevo</option>
                </select>
                @error('badge')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label">Destacado</label>
                <div class="form-check" style="padding-top: 0.5rem;">
                    @php $isFeatured = old('featured', $isEdit ? $product['featured'] : false); @endphp
                    <input type="hidden" name="featured" value="0">
                    <input type="checkbox"
                           id="featured"
                           name="featured"
                           value="1"
                           class="form-check-input"
                           {{ $isFeatured ? 'checked' : '' }}>
                    <label class="form-check-label" for="featured">
                        Mostrar en la página principal
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">
        <i class="bi bi-images"></i> Imágenes
    </div>

    @if($isEdit && !empty($product['images']))
        <div class="form-group">
            <label class="form-label">Imágenes actuales</label>
            <div class="existing-images">
                @foreach($product['images'] as $image)
                    <label class="existing-image">
                        <img src="{{ $image['url'] }}" alt="Imagen">
                        <input type="checkbox"
                               name="delete_images[]"
                               value="{{ $image['id'] }}">
                        <span class="existing-image-overlay">
                            <i class="bi bi-trash"></i> Eliminar
                        </span>
                        @if($image['is_primary'])
                            <span class="existing-image-primary">Principal</span>
                        @endif
                    </label>
                @endforeach
            </div>
            <small class="text-muted">Marca las imágenes que quieras eliminar y guarda.</small>
        </div>
    @endif

    <div class="form-group">
        <label class="form-label" for="images">
            {{ $isEdit ? 'Agregar nuevas imágenes' : 'Imágenes del producto' }}
        </label>
        <input type="file"
               id="images"
               name="images[]"
               class="form-control @error('images.*') is-invalid @enderror"
               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
               multiple>
        <small class="text-muted">
            Formatos: JPG, PNG, GIF, WEBP. Máximo 2MB por imagen.
        </small>
        @error('images.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

        <div id="imagePreview" class="image-preview"></div>
    </div>
</div>