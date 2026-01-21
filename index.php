<?php get_header(); ?>

<main id="primary" class="site-main py-20 px-6">
    <div class="container mx-auto">
        <?php if ( have_posts() ) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                <?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('group'); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="mb-6 overflow-hidden border-4 border-black">
                                <?php the_post_thumbnail('large', array('class' => 'w-full h-auto transform group-hover:scale-105 transition-transform duration-500')); ?>
                            </div>
                        <?php endif; ?>
                        
                        <header class="entry-header">
                            <h2 class="text-3xl font-black uppercase leading-none mb-4">
                                <a href="<?php the_permalink(); ?>" class="hover:text-accent transition-colors"><?php the_title(); ?></a>
                            </h2>
                        </header>

                        <div class="entry-content text-gray-600 line-clamp-3 mb-6">
                            <?php the_excerpt(); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="inline-block font-bold uppercase tracking-widest border-b-2 border-black pb-1 hover:border-accent hover:text-accent transition-all">Read More</a>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="text-2xl font-bold uppercase">Nothing found.</p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
