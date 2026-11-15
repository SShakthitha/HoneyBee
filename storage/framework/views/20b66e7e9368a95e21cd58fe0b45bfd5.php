<?php $__env->startSection('title', 'Home'); ?>
<?php $__env->startSection('meta_description', 'Shop handcrafted gifts, custom designs, laser work, and event services from HoneyBee Gifts & Designs.'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/home.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $categoryImage = fn (string $category) => optional($galleryImages->firstWhere('category', $category))->image;
        $categoryCards = [
            ['route' => 'events', 'image' => $categoryImage('Events'), 'icon' => 'bi-stars', 'label' => 'Event', 'description' => 'Decor & styling for every celebration'],
            ['route' => 'gift.design', 'image' => $categoryImage('Gift & Design'), 'icon' => 'bi-gift', 'label' => 'Gift & Design', 'description' => 'Curated gifts, personalised with care'],
            ['route' => 'laser.work', 'image' => $categoryImage('Laser Work'), 'icon' => 'bi-box', 'label' => 'Laser Design', 'description' => 'Precision engraving on wood & more'],
        ];
    ?>

    <div class="hb-original">
        <section class="hb-original__hero" id="home">
            <div class="hb-original__hero-shapes" aria-hidden="true"><span>⬡</span><span>⬡</span><span>⬡</span><span>⬡</span></div>
            <div class="container"><div class="hb-original__hero-content"><span class="hb-original__eyebrow">Gifts &amp; Designs</span><h1>Sweet Moments, <span>Handcrafted</span> Just For You</h1><p>HoneyBee Gifts &amp; Designs brings your ideas to life — from personalised gifts and frames to unforgettable event decor and precision laser engraving. One studio, endless creativity.</p></div></div>
            <a href="#services" class="hb-original__scroll" aria-label="Explore services"><i class="bi bi-chevron-down"></i></a>
        </section>

        <section class="hb-original__category-section" id="services"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">What We Do</span><h2>Explore Our Categories</h2><p>Three specialties, one studio — pick a path to see what we can create for you.</p></div><div class="hb-original__category-band">
            <?php $__currentLoopData = $categoryCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route($card['route'])); ?>" class="hb-original__category-panel">
                    <?php if($categoryImageUrl = \App\Support\PublicImage::url($card['image'])): ?><img src="<?php echo e($categoryImageUrl); ?>" alt="<?php echo e($card['label']); ?>"><?php endif; ?>
                    <span class="hb-original__category-fallback" aria-hidden="true"></span><span class="hb-original__category-overlay"></span>
                    <span class="hb-original__category-content"><span class="hb-original__category-icon"><i class="bi <?php echo e($card['icon']); ?>"></i></span><span class="hb-original__category-label"><?php echo e($card['label']); ?></span><span class="hb-original__category-description"><?php echo e($card['description']); ?></span></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div></div></section>

        <?php if($promotions->isNotEmpty()): ?>
            <section class="hb-original__offers" id="offers"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">Limited Time</span><h2>Special Offers &amp; Promotions</h2><p>Sweet deals across gifts, events, and laser work.</p></div>
                <div class="hb-original__offers-grid">
                    <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route($promotion->destinationRoute())); ?>" class="hb-original__offer-card">
                            <span class="hb-original__ribbon"><?php echo e($promotion->button_text ?: 'Shop Now'); ?></span>
                            <div class="hb-original__offer-image"><?php if($promotionImageUrl = \App\Support\PublicImage::url($promotion->image)): ?><img src="<?php echo e($promotionImageUrl); ?>" alt="<?php echo e($promotion->title); ?>"><?php endif; ?><span><?php echo e($promotion->title); ?></span></div>
                            <div class="hb-original__offer-body"><h3><?php echo e($promotion->title); ?></h3><p><?php echo e($promotion->short_description ?: 'Limited Time Only'); ?></p><div class="hb-original__price"><strong><?php echo e($promotion->button_text ?: 'Shop Now'); ?></strong></div><span class="hb-original__offer-button"><?php echo e($promotion->button_text ?: 'Shop Now'); ?></span></div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div></section>
        <?php else: ?>
            <section class="hb-original__offers" id="offers"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">Limited Time</span><h2>Special Offers &amp; Promotions</h2><p>Sweet deals across gifts, events, and laser work.</p></div>
                <?php if($featuredItems->isNotEmpty()): ?><div class="hb-original__offers-grid">
                    <?php $__currentLoopData = $featuredItems->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $discount = $item['has_offer_price'] ? $item['discount_percentage'] : null; ?>
                        <a href="<?php echo e(route($item['route'])); ?>" class="hb-original__offer-card">
                            <?php if($discount): ?><span class="hb-original__ribbon"><?php echo e($discount); ?>% Off</span><?php else: ?><span class="hb-original__ribbon">Featured</span><?php endif; ?>
                            <div class="hb-original__offer-image"><?php if($featuredImageUrl = \App\Support\PublicImage::url($item['image'])): ?><img src="<?php echo e($featuredImageUrl); ?>" alt="<?php echo e($item['name']); ?>"><?php endif; ?><span><?php echo e($item['type']); ?></span></div>
                            <div class="hb-original__offer-body"><h3><?php echo e($item['name']); ?></h3><p><?php echo e($item['type']); ?> created by HoneyBee.</p><?php if($discount): ?><div class="hb-original__price"><del>Rs. <?php echo e(number_format($item['price'], 2)); ?></del><strong>Rs. <?php echo e(number_format($item['selling_price'], 2)); ?></strong></div><?php elseif($item['price']): ?><div class="hb-original__price"><strong>Rs. <?php echo e(number_format($item['selling_price'], 2)); ?></strong></div><?php endif; ?><span class="hb-original__offer-button">Claim Offer</span></div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div><?php else: ?><p class="hb-original__empty">New HoneyBee creations will appear here soon.</p><?php endif; ?>
            </div></section>
        <?php endif; ?>

        <section class="hb-original__gallery-section" id="gallery"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">Our Work</span><h2>Gallery</h2><p>A glimpse into our gifts, events, and laser craftsmanship.</p></div>
            <?php if($galleryImages->isNotEmpty()): ?><div class="hb-original__gallery-grid"><?php $__currentLoopData = $galleryImages->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $galleryImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><figure class="hb-original__gallery-item"><?php if($galleryImageUrl = \App\Support\PublicImage::url($galleryImage->image)): ?><img src="<?php echo e($galleryImageUrl); ?>" alt="<?php echo e($galleryImage->title ?? $galleryImage->category . ' creation'); ?>"><?php else: ?><span class="hb-original__category-fallback" aria-label="Gallery image unavailable"></span><?php endif; ?><figcaption><?php echo e($galleryImage->title ?? $galleryImage->category); ?></figcaption></figure><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php else: ?><p class="hb-original__empty">Our latest work will be added here soon.</p><?php endif; ?>
        </div></section>

        <section class="hb-original__contact" id="contact"><div class="container"><div class="hb-original__section-heading"><span class="hb-original__eyebrow">Get In Touch</span><h2>Contact Us</h2><p>Have a question or ready to place an order? Reach out — we'd love to hear from you.</p></div><div class="hb-original__contact-grid"><a href="<?php echo e(route('contact')); ?>" class="hb-original__contact-item"><span><i class="bi bi-telephone"></i></span><h3>Phone</h3><p>Contact our HoneyBee team</p></a><a href="<?php echo e(route('contact')); ?>" class="hb-original__contact-item"><span><i class="bi bi-envelope"></i></span><h3>Email</h3><p>Send us an enquiry</p></a><a href="<?php echo e(route('contact')); ?>" class="hb-original__contact-item"><span><i class="bi bi-chat-dots"></i></span><h3>Message Us</h3><p>Start a custom request</p></a></div><div class="hb-original__contact-action"><a href="<?php echo e(route('contact')); ?>">Message Us</a></div></div></section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('homepage-footer'); ?>
<footer class="hb-original-footer"><div class="container"><div class="hb-original-footer__inner"><div class="hb-original-footer__brand"><div><img src="<?php echo e(asset('images/logo.webp')); ?>" alt="HoneyBee Logo"><span>Honey<span>Bee</span></span></div><p>Gifts, events &amp; laser work crafted with care — bringing your ideas to life, one detail at a time.</p></div><div><h3>Explore</h3><a href="<?php echo e(route('home')); ?>">Home</a><a href="<?php echo e(route('gift.design')); ?>">Gift &amp; Design</a><a href="<?php echo e(route('events')); ?>">Events</a><a href="<?php echo e(route('laser.work')); ?>">Laser Work</a></div><div><h3>Company</h3><a href="#offers">Offers</a><a href="#gallery">Gallery</a><a href="<?php echo e(route('about')); ?>">About</a><a href="<?php echo e(route('contact')); ?>">Contact</a></div><div><h3>Contact</h3><a href="<?php echo e(route('contact')); ?>"><i class="bi bi-envelope"></i> Send an enquiry</a><a href="<?php echo e(route('contact')); ?>"><i class="bi bi-chat-dots"></i> Message HoneyBee</a></div></div><p class="hb-original-footer__bottom">&copy; <?php echo e(now()->year); ?> HoneyBee Gifts <span>&amp;</span> Designs. All rights reserved.</p></div></footer>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\HoneyBeeshop\resources\views/home.blade.php ENDPATH**/ ?>