<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div style="display: flex; min-height: 80vh;">

    <!-- MAIN CONTENT -->
    <div style="flex: 1; padding: 40px;">
        <h1 style="font-size: 28px; margin-bottom: 30px;">
            Dashboard <span style="color: #f5a623;">Overview</span>
        </h1>

        <!-- STATS CARDS -->
        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px;">

            <!-- TOTAL SERVICES -->
            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">🛍️</div>
                <h3 style="font-size: 32px; color: #f5a623;"><?php echo e($totalServices); ?></h3>
                <p style="color: #777;">Total Services</p>
            </div>

            <!-- GIFT & DESIGN -->
            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">🎁</div>
                <h3 style="font-size: 32px; color: #f5a623;"><?php echo e($totalGifts); ?></h3>
                <p style="color: #777;">Gift & Design</p>
            </div>

            <!-- LASER WORK -->
            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">🔥</div>
                <h3 style="font-size: 32px; color: #f5a623;"><?php echo e($totalLaserWorks); ?></h3>
                <p style="color: #777;">Laser Work</p>
            </div>

            <!-- EVENTS -->
            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">🎉</div>
                <h3 style="font-size: 32px; color: #f5a623;"><?php echo e($totalEvents); ?></h3>
                <p style="color: #777;">Events</p>
            </div>

            <!-- TOTAL ORDERS -->
            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">📦</div>
                <h3 style="font-size: 32px; color: #f5a623;"><?php echo e($totalOrders); ?></h3>
                <p style="color: #777;">Total Orders</p>
            </div>

            <!-- TOTAL CUSTOMERS -->
            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">👥</div>
                <h3 style="font-size: 32px; color: #f5a623;"><?php echo e($totalCustomers); ?></h3>
                <p style="color: #777;">Total Customers</p>
            </div>

            <!-- TOTAL STAFF -->
            <div style="background: #fff; border-radius: 15px; padding: 30px; width: 200px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center;">
                <div style="font-size: 36px; margin-bottom: 10px;">👨‍💼</div>
                <h3 style="font-size: 32px; color: #f5a623;"><?php echo e($totalStaff); ?></h3>
                <p style="color: #777;">Total Staff</p>
            </div>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\HoneyBeeshop\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>