

<?php $__env->startSection('title', 'Staff'); ?>

<?php $__env->startSection('content'); ?>
<div class="topbar">
    <h1>All <span>Staff</span></h1>
</div>

<div class="admin-toolbar">
    <button type="button" class="btn btn-honey" data-bs-toggle="modal" data-bs-target="#addStaffModal">
        <i class="fa-solid fa-plus me-1"></i> Add Staff
    </button>
</div>

<div class="admin-card">
    <div class="admin-card-body">
        <?php if($staff->isEmpty()): ?>
            <div class="text-center text-muted py-5">No staff members yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle admin-datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Hire Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>#<?php echo e($s->staff_id); ?></td>
                            <td><?php echo e($s->full_name); ?></td>
                            <td><?php echo e($s->role); ?></td>
                            <td><?php echo e($s->email); ?></td>
                            <td><?php echo e($s->phone); ?></td>
                            <td><?php echo e($s->hire_date); ?></td>
                            <td class="text-end">
                                <span class="action-buttons">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editStaffModal<?php echo e($s->staff_id); ?>"
                                            title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="<?php echo e(route('admin.staff.delete', $s->staff_id)); ?>"
                                          method="POST"
                                          onsubmit="return confirm('Do you want to remove this staff member?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('admin.staff.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Add Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" name="role" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hire Date</label>
                            <input type="date" name="hire_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Save Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editStaffModal<?php echo e($s->staff_id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('admin.staff.update', $s->staff_id)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header" style="background:#f5a623;">
                    <h5 class="modal-title fw-bold">Edit Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" value="<?php echo e($s->full_name); ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" name="role" value="<?php echo e($s->role); ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="<?php echo e($s->email); ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" value="<?php echo e($s->phone); ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hire Date</label>
                            <input type="date" name="hire_date" value="<?php echo e($s->hire_date); ?>" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-honey">Update Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\HoneyBeeshop\resources\views/admin/staff/index.blade.php ENDPATH**/ ?>