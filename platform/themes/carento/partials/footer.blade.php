@php
    $backgroundColor = theme_option('footer_background_color', '#FFFFFF');
    $textColor = theme_option('footer_text_color', theme_option('text_color', '#3E4073'));
    $headingColor = theme_option('footer_heading_color', theme_option('primary_color', '#14176C'));
    $backgroundImage = theme_option('footer_background_image');
    $borderColor = theme_option('footer_border_color', '#CFDDE2');
    $backgroundImage = $backgroundImage ? RvMedia::getImageUrl($backgroundImage) : null;
@endphp

<style>
    /* =========================================
       FOOTER SPACING & BORDER REMOVAL
       ========================================= */
    /* Remove the top line and reduce overall footer height */
    .footer {
        border-top: none !important;
        border-bottom: none !important;
        padding-top: 0px !important; /* Reduced from theme default */
        padding-bottom: 0px !important; /* Reduced from theme default */
    }

    /* Remove the line above the copyright text */
    .footer .footer-bottom {
        border-top: none !important;
        border-bottom: none !important;
        margin-top: 0px !important; /* Adjusted to balance the height */
        padding-top: 0 !important; 
    }

    /* =========================================
       NEWSLETTER & BUTTON STYLING
       ========================================= */
    /* Make Newsletter Input smaller and pill-shaped */
    .footer form input[type="email"],
    .footer form input[type="text"],
    .footer .newsletter-form input {
        height: 45px !important; /* Reduced height */
        border-radius: 50px !important; /* Pill shape */
        padding: 8px 24px !important;
    }

    /* Make Subscribe Button smaller and pill-shaped */
    .footer form button[type="submit"],
    .footer .newsletter-form button {
        height: 45px !important; /* Match input height perfectly */
        border-radius: 50px !important; /* Pill shape */
        padding: 0 32px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Maintain a gap directly below the newsletter form */
    .footer form,
    .footer .newsletter-form,
    .footer .widget_newsletter {
        margin-bottom: 20px !important; 
    }
</style>

{!! apply_filters('ads_render', null, 'footer_before', ['class' => 'mb-2']) !!}

<footer class="footer" @style([
    "--footer-background-color: $backgroundColor",
    "--footer-heading-color: $headingColor",
    "--footer-text-color: $textColor",
    "--footer-border-color: transparent", /* Overrides the theme border variable to make lines invisible */
    "--footer-background-image: url($backgroundImage)" => $backgroundImage,
])>
    <div class="container">
        <div class="footer-top">
            {!! dynamic_sidebar('top_footer_sidebar') !!}
        </div>
        <div class="row">
            {!! dynamic_sidebar('footer_sidebar') !!}
        </div>
        <div class="footer-bottom mt-50">
            <div class="row align-items-center justify-content-center">
                {!! dynamic_sidebar('bottom_footer_sidebar') !!}
            </div>
        </div>
    </div>
</footer>

{!! apply_filters('ads_render', null, 'footer_after', ['class' => 'mt-2']) !!}