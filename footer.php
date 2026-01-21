<!-- Overwriting the file -->
    <footer id="colophon" class="site-footer py-12 px-6 bg-black text-white mt-20">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="footer-info">
                <h2 class="text-2xl font-black uppercase mb-4"><?php bloginfo( 'name' ); ?></h2>
                <p class="text-gray-400"><?php bloginfo( 'description' ); ?></p>
            </div>
            <div class="footer-links">
                <h3 class="text-lg font-bold uppercase mb-4">Shop</h3>
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'footer-menu',
                    'container'      => false,
                    'menu_class'     => 'space-y-2 text-gray-400',
                ) );
                ?>
            </div>
            <div class="footer-newsletter">
                <h3 class="text-lg font-bold uppercase mb-4">Stay Vibe</h3>
                <form class="flex">
                    <input type="email" placeholder="Email" class="bg-gray-900 border-none px-4 py-2 w-full focus:ring-2 focus:ring-accent">
                    <button class="bg-white text-black px-6 py-2 font-bold uppercase">Join</button>
                </form>
            </div>
        </div>
        <div class="container mx-auto mt-12 pt-8 border-t border-gray-800 text-center text-sm text-gray-500">
            &copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>. Built for the Vibe.
        </div>
    </footer>
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
