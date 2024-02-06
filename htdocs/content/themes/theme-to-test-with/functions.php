<?php

use Themosis\Core\Application;

use Themosis\Support\Facades\Page;
use Themosis\Support\Section;
use Themosis\Support\Facades\Taxonomy;
// use Themosis\Support\Facades\TaxonomyField;
// use Themosis\Support\Facades\Field;


/*
|--------------------------------------------------------------------------
| Bootstrap Theme
|--------------------------------------------------------------------------
|
| We bootstrap the theme. The following code is loading your theme
| configuration files and register theme images sizes, menus, sidebars,
| theme support features and templates.
|
*/
$theme = (Application::getInstance())->loadTheme(__DIR__, 'config');

/*
|--------------------------------------------------------------------------
| Theme i18n | l10n
|--------------------------------------------------------------------------
|
| Registers the "languages" directory for storing the theme translations.
|
| The "THEME_TD" constant is defined during bootstrap and its value is
| set based on the "style.css" [Text Domain] property located into
| the file header.
|
*/
load_theme_textdomain(
    THEME_TD,
    $theme->getPath($theme->getHeader('domain_path'))
);

/*
|--------------------------------------------------------------------------
| Theme assets locations
|--------------------------------------------------------------------------
|
| You can define your theme assets paths and URLs. You can add as many
| locations as you want. The key is your asset directory path and
| the value is its public URL.
|
*/
$theme->assets([
    $theme->getPath('dist') => $theme->getUrl('dist')
]);

/*
|--------------------------------------------------------------------------
| Theme Views
|--------------------------------------------------------------------------
|
| Register theme view paths. By default, the theme is registering
| the "views" directory but you can add as many directories as you want
| from the theme.php configuration file.
|
*/
$theme->views($theme->config('theme.views', []));

/*
|--------------------------------------------------------------------------
| Theme Service Providers
|--------------------------------------------------------------------------
|
| Register theme service providers. You can manage the list of
| services providers through the theme.php configuration file.
|
*/
$theme->providers($theme->config('theme.providers', []));

/*
|--------------------------------------------------------------------------
| Theme includes
|--------------------------------------------------------------------------
|
| Auto includes files by providing one or more paths. By default, we setup
| an "inc" directory within the theme. Use that "inc" directory to extend
| your theme features. Nested files are also included.
|
*/
$theme->includes([
    $theme->getPath('inc')
]);

/*
|--------------------------------------------------------------------------
| Theme Image Sizes
|--------------------------------------------------------------------------
|
| Register theme image sizes. Image sizes are configured in your theme
| images.php configuration file.
|
*/
$theme->images($theme->config('images'));

/*
|--------------------------------------------------------------------------
| Theme Menu Locations
|--------------------------------------------------------------------------
|
| Register theme menu locations. Menu locations are configured in your theme
| menus.php configuration file.
|
*/
$theme->menus($theme->config('menus'));

/*
|--------------------------------------------------------------------------
| Theme Sidebars
|--------------------------------------------------------------------------
|
| Register theme sidebars. Sidebars are configured in your theme
| sidebars.php configuration file.
|
*/
$theme->sidebars($theme->config('sidebars'));

/*
|--------------------------------------------------------------------------
| Theme Support
|--------------------------------------------------------------------------
|
| Register theme support. Support features are configured in your theme
| support.php configuration file.
|
*/
$theme->support($theme->config('support', []));

/*
|--------------------------------------------------------------------------
| Theme Templates
|--------------------------------------------------------------------------
|
| Register theme templates. Templates are configured in your theme
| templates.php configuration file.
|
*/
$theme->templates($theme->config('templates', []));



/*
|--------------------------------------------------------------------------
| Theme Templates
|--------------------------------------------------------------------------
|
| Register theme templates. Templates are configured in your theme
| templates.php configuration file.
|
*/
$theme->templates($theme->config('templates', []));


/*** End of default functions.php ****************************************/

/*
|--------------------------------------------------------------------------
| Custom Themosis
|--------------------------------------------------------------------------
|
| By Leon Kuijf. W.T. Group.
|
*/
$page = Page::make('website-options', __( 'Set your website options' ))
    ->setMenu('Website options')
    ->set();
// $page->route('/', function () {
//     return view('admin.home');
// });
$page->addSections([
    // new Section('general', 'General'),
    new Section('social', 'Social'),
    new Section('footer', 'Footer')
]);
$page->addSettings([
    // 'general' => [
    //     Field::text('title', [
    //         // 'rules' => 'required|min:6'
    //     ]),
    //     Field::textarea('comment')
    // ],
    'social' => [
        Field::text('twitter', [
            'rules' => 'required|url'
        ])
    ],
    'footer' => [
        Field::editor('footer_text', [
            'settings' => [
                'wpautop' => false
            ]
        ])
    ]
]);

$author = Taxonomy::make('authors', 'books', 'Authors', 'Author')
    ->setArguments([
        'hierarchical' => true, // to show already existing taxonomies
    ])
    ->set();
// TaxonomyField::make($author)
//     ->add(Field::text('publisher'))
//     ->add(Field::text('website'))
//     ->add(Field::choice('genre', [
//         'choices' => [
//             'Comedy',
//             'Fantasy',
//             'Science-Fiction',
//             'Thriller'
//         ]
//     ]))
//     ->add(Field::textarea('note'))
//     ->set();



/*
|--------------------------------------------------------------------------
| Carbon Fields 
|--------------------------------------------------------------------------
|
| By Leon Kuijf. W.T. Group.
|
*/
use Carbon_Fields\Container;
use Carbon_Fields\Block;
use Carbon_Fields\Field;

add_action( 'after_setup_theme', 'crb_load' );
add_action( 'carbon_fields_register_fields', 'myNewBlock'  );
add_action( 'carbon_fields_register_fields', 'contactForm'  );

function contactForm(){
    Block::make( __( 'Contact formulier' ) ) // cannot be changed afterwards
    ->add_fields( array(
        // Field::make( 'checkbox', 'cf_name', __( 'Show Name field (required)' ) )->set_option_value( 'yes' ),
        // Field::make( 'checkbox', 'cf_company', __( 'Show Company field' ) )->set_option_value( 'yes' ),
        // Field::make( 'checkbox', 'cf_email', __( 'Show E-mail field (required)' ) )->set_option_value( 'yes' ),
        // Field::make( 'checkbox', 'cf_phone', __( 'Show Phone number field' ) )->set_option_value( 'yes' ),
        // Field::make( 'checkbox', 'cf_message', __( 'Show Message text area' ) )->set_option_value( 'yes' ),
        
        Field::make( 'html', 'block_description' )
            ->set_html( '<h2>Contact formulier</h2><p>Vul de gegevens in en maak velden aan.</p>' ),

        Field::make( 'text', 'mail_to', __( 'E-mail to' ) )
            // ->set_attribute('pattern', '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$')
            // ->set_attribute('data-testing', 'myvalue')
            // ->set_attribute('placeholder', 'voorbeeld@domein.nl')
            ->set_help_text( __( 'To which e-mail address are the filled in forms send to?' ) )
            ->set_required( true )
            ->set_width( 20 ),
        
        Field::make( 'text', 'visitor_email_field_name', __( 'Field name where the visitor leaves his/her e-mail address' ) )
            ->set_help_text( __( 'When a confirmation e-mail has to be send to the visitor, fill in the name of the field containing the e-mail address' ) )
            ->set_width( 20 ),

        Field::make( 'text', 'success_text', __( 'Message when send successful' ) )
            ->set_default_value('Bedankt voor uw bericht! We nemen deze zo snel mogelijk in behandeling.')
            ->set_required( true ),
        Field::make( 'text', 'failure_text', __( 'Message when a failure has been detected' ) )
            ->set_default_value('Sorry, er is iets fout gegaan. Controleer het formulier op fouten. Er is geen bericht verzonden.')
            ->set_required( true ),

        Field::make( 'text', 'subject_company', __( 'Subject of the e-mail to the company' ) )
            ->set_default_value('Ingevuld contactformulier')
            ->set_required( true ),
        Field::make( 'text', 'subject_visitor', __( 'Subject of the e-mail to the visitor' ) )
            ->set_default_value('Kopie van uw bericht')
            ->set_required( true ),

        Field::make( 'complex', 'contact_form', __('Contact form') )
        ->add_fields( 'field', array(
            Field::make( 'text', 'name', __( 'Name' ) )->set_required( true )->set_help_text( __( 'Visible in the e-mail message' ) ),
            Field::make( 'text', 'label', __( 'Label' ) )->set_required( true )->set_help_text( __( 'Visible on the website' ) )->set_width( 20 ),
            Field::make( 'text', 'placeholder', __( 'Placeholder' ) )->set_help_text( __( 'Sample text in the field' ) )->set_width( 20 ),
            Field::make( 'checkbox', 'required', __( 'Required' ) ),
            Field::make( 'checkbox', 'email', __( 'E-mail format' ) ),
            Field::make( 'checkbox', 'numeric', __( 'Only numbers' ) ),
            Field::make( 'checkbox', 'alpha', __( 'Only letters (a-z / A-Z)' ) ),
            Field::make( 'checkbox', 'url', __( 'URL' ) ),
            
            /***  Min/max amount of characters, is a bit more complicated ***/
            // Field::make( 'text', 'min', __( 'Minimum value' ) )->set_width( 20 )->set_help_text( __( 'Minimum amount of characters' ) ),
            // Field::make( 'text', 'max', __( 'Minimum value' ) )->set_width( 20 )->set_help_text( __( 'Maximum amount of characters' ) ),

        ) )
        ->add_fields( 'textarea', array(
            Field::make( 'text', 'name', __( 'Name' ) )->set_required( true ),
            Field::make( 'text', 'label', __( 'Label' ) ),
            Field::make( 'text', 'placeholder', __( 'Placeholder' ) ),
            Field::make( 'checkbox', 'required', __( 'Required' ) ),
        ) )
    ))
    ->set_description( __( 'Custom Bock for contact form' ) )
    ->set_category( 'custom-wt-category', __( 'Custom blocks (by W.T. Media & Events)' ), 'smiley' )
    ->set_icon( 'feedback' )
    ->set_keywords( [ __( 'wt' ), __( 'custom' ), __( 'extra' ) ] )
    ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        /**** 10-1-2024 Leon Kuijf. Set some initial values. Adding fields AFTER block has already been created ends up in an undefined array key ****/
        /**** Or put in comment: add_action( 'carbon_fields_register_fields', 'contactForm'  ), so the block can be removed *****/
        // if(!isset($fields['anchor'])) $fields['anchor'] = '';
        if(!isset($fields['subject_company'])) $fields['subject_company'] = '';
        if(!isset($fields['subject_visitor'])) $fields['subject_visitor'] = '';
?>
        <div class="wtBlock">
            <form action="post" class="wtContactForm">
                <?php
                $formParams = new \stdClass();
                $formParams->mail_to = $fields['mail_to'];
                $formParams->visitor_email_field_name = $fields['visitor_email_field_name'];
                $formParams->subject_company = $fields['subject_company'];
                $formParams->subject_visitor = $fields['subject_visitor'];
                $formParams->success_text = $fields['success_text'];
                $formParams->failure_text = $fields['failure_text'];

                // $encryptedString = Crypt::encryptString('gewoon iets om te testen');
                echo '<input name="form_parameters" type="hidden" value="' . Crypt::encryptString(json_encode($formParams)) . '" data-wt-rules>';


                $availableRules = ['required', 'email', 'numeric', 'alpha', 'url']; // check Block declaration of all available rules.
                if(isset($fields['contact_form']) && count($fields['contact_form'])) {
                    foreach($fields['contact_form'] as $formItem) {
                        
                        $currFieldRules = [];
                        foreach($availableRules as $rule) if(isset($formItem[$rule]) && $formItem[$rule]) $currFieldRules[] = $rule;

                        /**** Always use 'data-wt-rules' attribute in a post field, because it is used as a selector by JS. ****/

                        echo '<div>';
                        if($formItem['_type'] == 'field') {
                            echo '
                                <label for="' . $formItem['_id'] . '">' . esc_html($formItem['label']) . '</label>
                                <input name="' . esc_html($formItem['name']) . '" id="' . $formItem['_id'] . '" type="text" placeholder="' . esc_html($formItem['placeholder']) . '" data-wt-rules="' . implode(' ', $currFieldRules) . '">
                            ';
                        }
                        if($formItem['_type'] == 'textarea') {
                            echo '
                                <label for="' . $formItem['_id'] . '">' . esc_html($formItem['label']) . '</label>
                                <textarea name="' . esc_html($formItem['name']) . '" id="' . $formItem['_id'] . '" cols="10" rows="5" placeholder="' . esc_html($formItem['placeholder']) . '" data-wt-rules="' . implode(' ', $currFieldRules) . '"></textarea>
                            ';
                        }
                        echo '</div>';

                    }
                }
                ?>
                <button type="submit">Verzenden</button>
            </form><!-- /.wtContactForm -->
        </div><!-- /.wtBlock -->
<?php
        
        echo '<pre>';
        print_r($fields);
        echo '</pre>';

    });
}

function myNewBlock(){
    Block::make( __( 'TEST block for association fields' ) ) // cannot be changed afterwards
	->add_fields( array(
        // Field::make( 'text', 'anchor', __( 'Anchor (Link menu items to this block with: #[Anchor])' ) ),
		Field::make( 'text', 'heading', __( 'Block Heading' ) ),
		// Field::make( 'image', 'image', __( 'Block Image' ) ),
		Field::make( 'rich_text', 'content', __( 'Block Content' ) ),
        Field::make( 'association', 'crb_association', __( 'Association' ) )
        ->set_types( array(
            array(
                'type'      => 'post',
                'post_type' => 'post',
            )
        ) ),
	) )
    ->set_description( __( 'Custom Bock for testing with association fields.' ) )
    // ->set_category( 'layout' )
    ->set_category( 'custom-wt-category', __( 'Custom blocks (by W.T. Media & Events)' ), 'smiley' )
    ->set_icon( 'heart' )
    ->set_keywords( [ __( 'wt' ), __( 'custom' ), __( 'extra' ) ] )
    // ->set_mode( 'both' )
    // ->set_editor_style( 'crb-my-shiny-gutenberg-block-stylesheet-BACKEND' )
    // ->set_style( 'crb-my-shiny-gutenberg-block-stylesheet-FRONTEND' )

    /*
    ->set_inner_blocks( true )
    ->set_inner_blocks_position( 'below' )
    ->set_inner_blocks_template( array(
		array( 'core/heading' ),
		array( 'core/paragraph' )
	) )
    ->set_inner_blocks_template_lock( 'insert' )
    ->set_parent( 'carbon-fields/product' )
    ->set_allowed_inner_blocks( array(
		'core/paragraph',
		'core/list'
	) )
	->set_render_callback( function () {
	} )
    */

	->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        /**** 10-1-2024 Leon Kuijf. Set some initial values. Adding fields AFTER block has already been created ends up in an undefined array key ****/
        if(!isset($fields['anchor'])) $fields['anchor'] = '';
        // list($url, $width, $height) = wp_get_attachment_image_src($fields['image'], 'full');
?>
        <div class="wtBlock">
            <div class="wtbContent">
                <div class="wtbText">
                    <div class="wtbInnerText">
                        <div class="wtb_heading">
                            <h1><?php echo esc_html( $fields['heading'] ); ?></h1>
                        </div><!-- /.wtb_heading -->
                        <div class="wtb_content">
                            <?php
                                echo apply_filters( 'the_content', $fields['content'] );
                                // echo $fields['content'];
                            ?>
                            <div>
                                <?php
                                /*
                                ?>
                                <pre><?php print_r($fields['crb_association']) ?></pre>
                                <?php
                                */

                                foreach($fields['crb_association'] as $assoc) {
                                    $post = get_post($assoc['id']);
                                    echo apply_filters('the_content', $post->post_title);
                                    echo apply_filters('the_content', $post->post_content);
                                }

                                // $post = get_post($my_postid);

                                ?>
                            </div>
                        </div><!-- /.wtb_content -->
                    </div><!-- /.wtbInnerText -->
                </div><!-- /.wtbText -->
            </div><!-- /.wtbContent -->
        </div><!-- /.wtBlock -->
<?php
	} );
}


function crb_load() {
    require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}
