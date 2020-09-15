		<a class="clear"><?php echo e($langg->lang436); ?></a>
		<?php if(count($datas) > 0): ?>
		<a id="order-notf-clear" data-href="<?php echo e(route('vendor-order-notf-clear',Auth::guard('web')->user()->id)); ?>" class="clear" href="javascript:;">
			<?php echo e($langg->lang437); ?>

		</a>
		<ul>
		<?php $__currentLoopData = $datas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<li>
				<a href="<?php echo e(route('vendor-order-show',$data->order_number)); ?>"> <i class="fas fa-newspaper"></i> <?php echo e($langg->lang438); ?></a>
			</li>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

		</ul>

		<?php else: ?> 

		<a class="clear" href="javascript:;">
			<?php echo e($langg->lang439); ?>

		</a>

		<?php endif; ?>