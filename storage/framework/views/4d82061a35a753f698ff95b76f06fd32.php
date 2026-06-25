

<?php $__env->startSection('title', 'Orders'); ?>

<?php $__env->startSection('content'); ?>

<div class="topbar">
    <h1>All <span>Orders</span></h1>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    <?php if($orders->isEmpty()): ?>
        <p style="text-align: center; color: #777; padding: 40px;">No orders yet! 🐝</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">Order ID</th>
                    <th style="padding: 12px; text-align: left;">Customer</th>
                    <th style="padding: 12px; text-align: left;">Service</th>
                    <th style="padding: 12px; text-align: left;">Amount</th>
                    <th style="padding: 12px; text-align: left;">Status</th>
                    <th style="padding: 12px; text-align: left;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">#<?php echo e($order->order_id); ?></td>
                    <td style="padding: 12px;"><?php echo e($order->customer->full_name ?? 'N/A'); ?></td>
                    <td style="padding: 12px;"><?php echo e($order->service->service_name ?? 'N/A'); ?></td>
                    <td style="padding: 12px;">Rs. <?php echo e(number_format($order->paid_amount, 2)); ?></td>
                    <td style="padding: 12px;">
                      <form action="/admin/orders/<?php echo e($order->order_id); ?>/status" method="POST">
                        <?php echo csrf_field(); ?>
                        <select name="status" onchange="this.form.submit()"
                          style="padding: 5px 10px; border-radius: 20px; border: 1px solid #ddd; background: #f5a623; font-weight: bold; cursor: pointer;">
                          <option value="pending" <?php echo e($order->status == 'pending' ? 'selected' : ''); ?>>Pending</option>
                          <option value="processing" <?php echo e($order->status == 'processing' ? 'selected' : ''); ?>>Processing</option>
                          <option value="completed" <?php echo e($order->status == 'completed' ? 'selected' : ''); ?>>Completed</option>
                          <option value="cancelled" <?php echo e($order->status == 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                        </select>
                      </form>
                    </td>
                    <td style="padding: 12px;"><?php echo e($order->order_date); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\HoneyBeeshop\resources\views/admin/orders.blade.php ENDPATH**/ ?>