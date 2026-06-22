

<?php $__env->startSection('title', 'Services'); ?>

<?php $__env->startSection('content'); ?>

<div class="topbar">
    <h1>All <span>Services</span></h1>
    <div style="display: flex; gap: 10px;">
      <a href="/admin/gift/create" style="background: #f5a623; color: #1a1a1a; padding: 8px 20px; border-radius: 25px; text-decoration: none; font-weight: bold;">+ Add Gift Item</a>
      <a href="/admin/laser/create" style="background: #1a1a1a; color: #f5a623; padding: 8px 20px; border-radius: 25px; text-decoration: none; font-weight: bold;">+ Add Laser Work</a>
      <a href="/admin/event/create" style="background: #f5a623; color: #1a1a1a; padding: 8px 20px; border-radius: 25px; text-decoration: none; font-weight: bold;">+ Add Event</a>
    </div>
</div>

<!-- GIFT & DESIGN -->
<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin-bottom: 30px;">
    <h2 style="margin-bottom: 20px;">Gift & Design</h2>
    <?php if($gifts->isEmpty()): ?>
        <p style="color: #777;">No gift items yet!</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">Item Name</th>
                    <th style="padding: 12px; text-align: left;">Category</th>
                    <th style="padding: 12px; text-align: left;">Price</th>
                    <th style="padding: 12px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $gifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><?php echo e($gift->item_name); ?></td>
                    <td style="padding: 12px;"><?php echo e($gift->category); ?></td>
                    <td style="padding: 12px;">Rs. <?php echo e(number_format($gift->price, 2)); ?></td>
                    <td style="padding: 12px;">
                        <a href="/admin/gift/<?php echo e($gift->gift_design_id); ?>/edit" style="color: #f5a623;">Edit</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- LASER WORK -->
<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); margin-bottom: 30px;">
    <h2 style="margin-bottom: 20px;">Laser Work</h2>
    <?php if($laserWorks->isEmpty()): ?>
        <p style="color: #777;">No laser work items yet!</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">Product Name</th>
                    <th style="padding: 12px; text-align: left;">Category</th>
                    <th style="padding: 12px; text-align: left;">Price</th>
                    <th style="padding: 12px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $laserWorks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $laserWork): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><?php echo e($laserWork->product_name); ?></td>
                    <td style="padding: 12px;"><?php echo e($laserWork->product_category); ?></td>
                    <td style="padding: 12px;">Rs. <?php echo e(number_format($laserWork->price, 2)); ?></td>
                    <td style="padding: 12px;">
                        <a href="/admin/laser/<?php echo e($laserWork->laser_id); ?>/edit" style="color: #f5a623;">Edit</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- EVENTS -->
<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    <h2 style="margin-bottom: 20px;">Events</h2>
    <?php if($events->isEmpty()): ?>
        <p style="color: #777;">No events yet!</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">Event Name</th>
                    <th style="padding: 12px; text-align: left;">Type</th>
                    <th style="padding: 12px; text-align: left;">Date</th>
                    <th style="padding: 12px; text-align: left;">Price</th>
                    <th style="padding: 12px; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;"><?php echo e($event->event_name); ?></td>
                    <td style="padding: 12px;"><?php echo e($event->event_type); ?></td>
                    <td style="padding: 12px;"><?php echo e($event->event_date); ?></td>
                    <td style="padding: 12px;">Rs. <?php echo e(number_format($event->price, 2)); ?></td>
                    <td style="padding: 12px;">
                        <a href="/admin/event/<?php echo e($event->event_id); ?>/edit" style="color: #f5a623;">Edit</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\HoneyBeeshop\resources\views/admin/services.blade.php ENDPATH**/ ?>