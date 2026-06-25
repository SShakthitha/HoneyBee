

<?php $__env->startSection('title', 'Gift & Design'); ?>

<?php $__env->startSection('content'); ?>

<!-- HERO -->
<section style="background: linear-gradient(135deg, #1a1a1a, #333); color: white; padding: 60px 40px; text-align: center;">
    <h1 style="font-size: 40px; margin-bottom: 15px;">🎁 Gift & <span style="color: #f5a623;">Design</span></h1>
    <p style="color: #ccc; font-size: 16px;">Custom gifts and creative designs for every occasion</p>
</section>

<!-- PRODUCTS -->
<section style="padding: 60px 40px;">

<?php if($gifts->isEmpty()): ?>
    <div style="text-align: center; padding: 60px;">
        <p style="font-size: 20px; color: #777;">No products available yet. Check back soon! 🐝</p>
    </div>
<?php else: ?>
    <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
        <?php $__currentLoopData = $gifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background: #fff; border: 1px solid #eee; border-radius: 15px; width: 280px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); overflow: hidden;">

            <?php if($gift->image): ?>
                <img src="<?php echo e(asset('images/gifts/' . $gift->image)); ?>" style="width: 100%; height: 200px; object-fit: cover;">
            <?php else: ?>
                <div style="width: 100%; height: 200px; background: #f9f0e0; display: flex; align-items: center; justify-content: center; font-size: 48px;">🎁</div>
            <?php endif; ?>

            <div style="padding: 20px;">
                <h3 style="font-size: 18px; margin-bottom: 8px;"><?php echo e($gift->item_name); ?></h3>
                <p style="color: #777; font-size: 14px; margin-bottom: 10px;"><?php echo e($gift->category); ?></p>

                <!-- PRICE -->
                <p style="font-weight: bold; font-size: 18px; margin-bottom: 15px;">
                    <?php if($gift->offer_price): ?>
                        <del style="color:#999;">
                            Rs. <?php echo e(number_format($gift->price, 2)); ?>

                        </del>
                        <br>
                        <span style="color:#f5a623;">
                            Rs. <?php echo e(number_format($gift->offer_price, 2)); ?>

                        </span>
                    <?php else: ?>
                        <span style="color:#f5a623;">
                            Rs. <?php echo e(number_format($gift->price, 2)); ?>

                        </span>
                    <?php endif; ?>
                </p>

                <!-- WHATSAPP -->
                <a href="https://wa.me/94717714267?text=Hi!%20I%20want%20to%20order%20<?php echo e(urlencode($gift->item_name)); ?>%20-%20Rs.<?php echo e($gift->offer_price ?? $gift->price); ?>"
                   target="_blank"
                   style="background-color: #25D366; color: #fff; padding: 10px 20px; border-radius: 25px; text-decoration: none; font-weight: bold; font-size: 14px;">
                   🟢 Order via WhatsApp
                </a>

            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

</section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\HoneyBeeshop\resources\views/gift-design.blade.php ENDPATH**/ ?>