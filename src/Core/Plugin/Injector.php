<?php

namespace LEXO\AcfSeo\Core\Plugin;

use LEXO\AcfSeo\Core\Abstracts\Singleton;
use WP_Post;

class Injector extends Singleton
{
    protected static $instance = null;

    public function run()
    {
        add_action('wp_head', [$this, 'injectFields']);
        add_filter('wp_robots', [$this, 'handleRobotsTag']);
        add_filter('get_canonical_url', [$this, 'handleCanonicalUrl'], 10, 2);
        add_filter('pre_get_document_title', [$this, 'handleWpTitle']);
    }

    public function addTitleTag()
    {
        add_theme_support('title-tag');
    }

    public function injectFields()
    {
        if (!function_exists('get_field')) {
            return;
        }

        $object = get_queried_object();

        if (!$object instanceof WP_Post) {
            // Currenlty works only on posts. The logic for taxonomies is different.
            return;
        }

        $seo_og_url = get_the_permalink($object);
        $seo_title = get_field('seo_title', $object->ID);
        $seo_title = !empty($seo_title) ? $seo_title : $object->post_title;
        $seo_description = get_field('seo_description', $object->ID);
        $seo_og_lang = isset($_SESSION['jez']) ? $_SESSION['jez'] : 'de';
        $seo_og_type = get_field('seo_social_type', $object->ID);
        $seo_og_type = !empty($seo_og_type) ? $seo_og_type : 'website';
        $seo_site_name = get_bloginfo('name');
        $seo_publisher = 'LEXO - IT & Accounting Services - Hardware, Software, IT-Security, Server, Netzwerke, Online-Shops, Buchhaltungsprogramme (https://www.lexo.ch)';
        $seo_image_field = get_field('seo_image', $object->ID);
        $seo_theme_color = get_field('seo_theme_color', $object->ID);
        $seo_theme_color = !empty($seo_theme_color) ? $seo_theme_color : '#ffffff';

        ob_start(); ?>
            <meta name="title" content="<?php echo $seo_title; ?>" />
            <meta name="description" content="<?php echo $seo_description; ?>" />
            <meta name="theme-color" content="<?php echo $seo_theme_color; ?>">
            <meta property="og:title" content="<?php echo $seo_title; ?>" />
            <meta property="og:description" content="<?php echo $seo_description; ?>">
            <meta property="og:locale" content="<?php echo $seo_og_lang; ?>">
            <meta property="og:url" content="<?php echo $seo_og_url; ?>">
            <meta property="og:type" content="<?php echo $seo_og_type; ?>">
            <meta property="og:site_name" content="<?php echo $seo_site_name; ?>">
            <meta name="publisher" content="<?php echo $seo_publisher; ?>">
            <meta name="twitter:title" content="<?php echo $seo_title; ?>">
            <meta name="twitter:description" content="<?php echo $seo_description; ?>">

            <?php if (!empty($seo_image_field)) { ?>
                <meta property="og:image" content="<?php echo $seo_image_field['sizes']['large']; ?>">
                <meta name="twitter:image" content="<?php echo $seo_image_field['sizes']['large']; ?>">
            <?php } ?>
        <?php echo ob_get_clean();

        $seo_twitter_activate = get_field('seo_twitter_activate', $object->ID);

        if ($seo_twitter_activate) {
            $seo_twitter_card = get_field('seo_twitter_card_type');
            $seo_twitter_card = !empty($seo_twitter_card) ? $seo_twitter_card : 'summary';
            $seo_twitter_site = get_field('seo_twitter_name', $object->ID);
            $seo_twitter_creator = get_field('seo_twitter_creator');
            $seo_twitter_video = get_field('seo_twitter_video', $object->ID);

            ob_start(); ?>
                <meta name="twitter:card" content="<?php echo $seo_twitter_card; ?>">

                <?php if (!empty($seo_twitter_site)) { ?>
                    <meta name="twitter:site" content="<?php echo $seo_twitter_site; ?>">
                <?php }

                if (!empty($seo_twitter_creator)) { ?>
                    <meta name="twitter:creator" content="<?php echo $seo_twitter_creator; ?>">
                <?php } ?>

                <?php if ($seo_twitter_card === 'player' && !empty($seo_twitter_video)) { ?>
                    <meta name="twitter:player" content="<?php echo $seo_twitter_video; ?>">
                    <meta name="twitter:player:height" content="200">
                    <meta name="twitter:text:player_height" content="200">
                    <meta name="twitter:text:player_width" content="300">
                    <meta name="twitter:player:width" content="300">
                <?php }
                echo ob_get_clean();
        }
    }

    public function handleWpTitle($title)
    {
        if (!function_exists('get_field')) {
            return;
        }

        $object = get_queried_object();

        if (!$object instanceof WP_Post) {
            // Currenlty works only on posts. The logic for taxonomies is different.
            return $title;
        }

        $seo_title = get_field('seo_title', $object->ID);

        return !empty($seo_title) ? $seo_title : $object->post_title;
    }

    public function handleCanonicalUrl($canonical_url, $post)
    {
        $seo_canonical = get_field('seo_canonical_url', $post->ID);

        return !empty($seo_canonical) ? $seo_canonical : $canonical_url;
    }

    public function handleRobotsTag($robots)
    {
        if (get_option('blog_public') == 0) {
            return $robots;
        }

        $object = get_queried_object();

        if (!$object instanceof WP_Post) {
            // Currenlty works only on posts. The logic for taxonomies is different.
            return $robots;
        }

        $seo_robot_index = get_field('seo_meta_robots_index_setting', $object->ID);
        $seo_robot_follow = get_field('seo_meta_robots_follow_setting', $object->ID);

        if (!empty($seo_robot_index) && !empty($seo_robot_follow)) {
            $robots['noindex'] = $seo_robot_index === 'noindex' ? true : false;
            $robots['nofollow'] = $seo_robot_follow === 'nofollow' ? true : false;

            if ($robots['noindex'] || $robots['nofollow']) {
                $robots['max-image-preview'] = false;
            }
        }

        return $robots;
    }
}
