

<?php $__env->startSection('title', 'Add Gift Item'); ?>

<?php $__env->startSection('content'); ?>

<div class="topbar">
    <h1>Add <span>Gift & Design</span> Item</h1>
    <a href="<?php echo e(route('admin.services.gift')); ?>">← Back</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">

    <?php if($errors->any()): ?>
        <div style="background: #f8d7da; color: #842029; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <strong>Please correct the following:</strong>
            <ul style="margin: 8px 0 0; padding-left: 20px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form action="/admin/gift/store" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

            <div>
                <label>Item Name *</label>
                <input type="text" name="item_name" required style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Category *</label>
                <select name="category" required style="width:100%;padding:10px;">
                    <option value="">Select a category</option>
                    <option value="Gift" <?php if(old('category') === 'Gift'): echo 'selected'; endif; ?>>Gift</option>
                    <option value="Frame" <?php if(old('category') === 'Frame'): echo 'selected'; endif; ?>>Frame</option>
                </select>
            </div>

            <div>
                <label>Material</label>
                <input type="text" name="material" style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Size</label>
                <input type="text" name="size" style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Price *</label>
                <input type="number" name="price" required style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Customization</label>
                <input type="text" name="customization_option" style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Offer Price</label>
                <input type="number" name="offer_price" style="width:100%;padding:10px;">
            </div>

            <div>
                <label>Image</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" style="width:100%;padding:10px;">
                <small style="color:#666;">JPG, PNG, or WebP — maximum 10 MB.</small>
            </div>

        </div>

        <div style="margin-top: 20px;">
            <label>Description</label>
            <textarea name="description" rows="4" style="width:100%;padding:10px;"></textarea>
        </div>

        <button type="submit" style="margin-top:20px;padding:12px 25px;background:#f5a623;">
            ➕ Add Item
        </button>

    </form>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\HoneyBeeshop\resources\views/admin/gift/create.blade.php ENDPATH**/ ?>