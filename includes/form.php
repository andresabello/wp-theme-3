<?php 
/*Step 1: Register the database for the plugin*/
function ac_form_init() {
    $args = array(
      'public' => false,
      'label'  => 'ac Forms'
    );
    register_post_type( 'ac_form', $args );
}
add_action( 'init', 'ac_form_init' );
/**
 * Step 2: Creates the option page menu item for the admin menu
 *
 * @since 4.0.1
 */
function register_ac_forms_menu_page(){
    add_menu_page( 'Forms Report', 'Forms Performance', 'manage_options', 'ac_forms_menu', 'ac_forms_menu_page', '', 6 );
}
add_action( 'admin_menu', 'register_ac_forms_menu_page' );
/*The Page itself*/
function ac_forms_menu_page(){
	/*Get posts with post type ac_forms*/
	$args = array(
		'posts_per_page'   => -1,
		'offset'           => 0,
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'post_type'        => 'ac_form',
		'post_status'      => 'publish',
		'suppress_filters' => true 
	);
	$posts = get_posts($args);
	/*Start count at 1*/
	$count = 1;
	echo '<div class="ac-admin-show wrap">';
	echo '<h1 class="widefat">Form Reports: </h1><hr style="margin-bottom: 40px;">';
	/*Present the data*/
	foreach ($posts as $post) {
		$post_meta = get_post_meta( $post->ID );
		echo '<h3>Submission ' . $count . ':</h3>'; 
		echo '<ul class="ac-data-show">';
		foreach ($post_meta as $field => $value) {
			echo '<li>'. $field .': '. $value[0] .'</li>';
		}
		echo '</ul>';
		echo '<hr>';
		/*Increment the count only within this loop*/
		$count ++;
	}
	echo '</div>';
}
/**
 * Step 3: Create the widget for the contact form 
 *ac Form widget class 
 *
 * @since 4.0.1
 */
class ac_form extends WP_Widget {
	/*Class Constructor and naming*/
	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'ac_form', 
			// Widget name will appear in UI
			'Addiction Contact Form', 
			// Widget description
			array( 'description' => 'Addiction Contact Form for clients seeking help.'  ) 
		);
	}
	public function widget( $args, $instance ) {
		/*Variables*/
		/**
		 * Filter the content of the ac Form widget title.
		 *
		 * @since 4.0.1
		 *
		 * @param string    $widget_text The widget content.
		 * @param WP_Widget $instance    WP_Widget instance.
		 */
		$title = apply_filters( 'widget_title', $instance['title'] );
		/**
		 * Filter the content of the ac Form widget description.
		 *
		 * @since 4.0.1
		 *
		 * @param string    $widget_text The widget content.
		 * @param WP_Widget $instance    WP_Widget instance.
		 */
		$description = apply_filters( 'widget_text', empty( $instance['description'] ) ? '' : $instance['description'], $instance );
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		?>
		<div id="ac-form">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
				<?php
				if ( ! empty( $title ) )
				echo $args['before_title']  . $title . $args['after_title'];
				echo '<p>' . $description . '</p>';
				wp_nonce_field('ac_form_ajaxhandler','ac_form_nonce')
				?>				
				<!-- Name -->
				<input type="text" name="ac_name" placeholder="Your Name">
				<!-- Phone -->
				<input type="text" name="ac_phone" placeholder="Your Phone Number">
				<!-- Email -->
				<input type="text" name="ac_email" placeholder="Your Email Address">
				<!-- Treatment for -->
				<select name="ac_select" id="ac_select">
					<option value="Choose an option">I am seeking treatment for:</option>
					<option value="Addicted person’s spouse / significant other">Addicted person’s spouse / significant other</option>
					<option value="Addicted person’s mother">Addicted person’s mother</option>
					<option value="Addicted person’s father">Addicted person’s father</option>
					<option value="Addicted person’s grandparent">Addicted person’s grandparent</option>
					<option value="Addicted person’s brother/sister">Addicted person’s brother/sister</option>
					<option value="Addicted person’s family">Addicted person’s family</option>
					<option value="Addicted person’s friend">Addicted person’s friend</option>
					<option value="Self">Self</option>
					<option value="Other">Other</option>
				</select>
				<!-- Drug of Coice -->
				<input type="text" name="ac_choice" placeholder="What is your drug of choice?">
				<!-- Time using drug -->
				<input type="text" name="ac_time" placeholder="How long have you been using?">
				<!-- Insurance -->
				<fieldset>
					<label for="ac_insurance">Do you have Insurance?</label><input type="radio" name="ac_insurance" id="ac_insurance" value="yes">YES &nbsp; <input type="radio" name="ac_insurance" value="no">NO					
				</fieldset>
				<fieldset>
					<label for="ac_treatment">Have you been in treatment before?</label><input type="radio" name="ac_treatment" id="ac_treatment" value="yes">YES &nbsp; <input type="radio" name="ac_treatment" value="no">NO					
				</fieldset>
				<textarea name="ac_message" class="widefat">Questions or comments...</textarea>
				<!-- Captcha -->
				<label for="total">Are you human?</label>				
				<span class="rand1"></span> + <span class="rand2"></span> = <input type="text" id="total">
				<!-- STOP BOTS -->
				<input type="checkbox" name="interested">
				<!-- Submit -->
				<button id="ac-submit" name="ac-submit" type="submit">Take the First Step</button>
				<p id="form-response"></p>
				<p>We respect your <a href="<?php echo home_url();?>/privacy-policy/">privacy</a>.<br>
				All information provided is confidential.</p>
			</form>		
		</div>
		<?php
		// This is where you run the code and display the output
		echo $args['after_widget'];
	}
	// Widget Backend 
	public function form( $instance ) {
		/*Title*/
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'description' => '') );
		$title = strip_tags($instance[ 'title' ]);
		/*Description*/	
		$description = esc_textarea($instance['description']);
		// Widget admin form
		?>
		<!-- Title field -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<!-- Desctiption field -->
		<p>
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description before form (HTML enabled):' ); ?></label> 
			<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" roac="10"><?php echo $description; ?></textarea>
		</p>
		<?php 
	}
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/*Title of the form*/
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		/*Description*/		
		if ( current_user_can('unfiltered_html') )
			$instance['description'] =  $new_instance['description'];
		else
			$instance['description'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['description']) ) ); // wp_filter_post_kses() expects slashed
		/*Return the instances*/
		return $instance;
	}
} // Class ac_form ends here
/*Register and load widget*/
function ac_load_widget() {
	register_widget( 'ac_form' );
}
add_action( 'widgets_init', 'ac_load_widget' );
/*Allow html in emails*/
function ac_set_content_type(){
    return "text/html";
}
add_filter( 'wp_mail_content_type','ac_set_content_type' );
/**/
function acform_render( $atts, $description = '' ) {
	//Attributes for the form
	$atts = shortcode_atts( array(
		'type' => 'desktop',
		'title' => 'Contact us Now',
	), $atts, 'acform' );
		$content = '<div id="ac-form">';
		$content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';
		$content .= '<img src="' . plugins_url( 'includes/css/email.png', __FILE__ ) . '" > ';
		$content .= wp_nonce_field('ac_form_ajaxhandler','ac_form_nonce'); 
		$content .= '<h3>' . $atts['title'] . '</h3>';
		$content .= $description . '<br>';
		$content .= '<label for="ac_name">Name:</label>';
		$content .= '<input type="text" name="ac_name" placeholder="Your Name">';
		$content .= '<label for="ac_phone">Phone number:</label>';
		$content .= '<input type="text" name="ac_phone">';
		$content .= '<label for="ac_email">Email:</label>';		
		$content .= '<input type="email" name="ac_email" placeholder="Email">';
		$content .= '<label for="ac_select">Seeking treatment for:</label>';
		$content .= '<select name="ac_select">
						<option value="Seeking Treatment for">Seeking Treatment for</option>
						<option value="Addicted person’s spouse / significant other">Addicted person’s spouse / significant other</option>
						<option value="Addicted person’s mother">Addicted person’s mother</option>
						<option value="Addicted person’s father">Addicted person’s father</option>
						<option value="Addicted person’s grandparent">Addicted person’s grandparent</option>
						<option value="Addicted person’s brother/sister">Addicted person’s brother/sister</option>
						<option value="Addicted person’s family">Addicted person’s family</option>
						<option value="Addicted person’s friend">Addicted person’s friend</option>
						<option value="Self">Self</option>
						<option value="Other">Other</option>
					</select>';
		$content .= '<label for="ac_choice">Drug of choice:</label>';
		$content .= '<input type="text" name="ac_choice">';
		$content .= '<label for="ac_time">How long have you been using?</label>';
		$content .= '<input type="text" name="ac_time">';
		$content .= '<fieldset>';
		$content .= '<label for="ac_insurance">Do you have Insurance?</label><input type="radio" name="ac_insurance" value="yes">Yes &nbsp; <input type="radio" name="ac_insurance" value="no">No';					
		$content .= '</fieldset>';
		$content .= '<fieldset>';
		$content .=	'<label for="ac_treatment">Have you been in treatment before?</label><input type="radio" name="ac_treatment" value="yes">Yes &nbsp; <input type="radio" name="ac_treatment" value="no">No';
		$content .= '</fieldset>';
		$content .= '<label for="ac_message">Questions or comments:</label>';				
		$content .= '<textarea name="ac_message" class="widefat">Question or Comment</textarea>';			
		$content .= '<label for="total">Are you human?</label>';				
		$content .= '<span class="rand1"></span> + <span class="rand2"></span> = <input type="text" id="total">';
		$content .= '<input type="checkbox" name="interested">';
		$content .= '<button id="ac-submit" name="ac-submit" type="submit">Take the First Step</button>';
		$content .= '<div class="info">We respect your <a href="' .  home_url() . '/privacy-policy/">privacy</a>.<br>';
		$content .= 'All information provided is confidential.</div></form></div>';		
		return $content;
}
add_shortcode( 'acform', 'acform_render', 20);
/*Do shortcodes in text widgets*/
add_filter('widget_text', 'do_shortcode');
// /*Step 4: Send email, store data, and present data. Also, auto respond and send to other database*/
/*Ajax Handling*/
function ac_form_ajaxhandler() {
    if ( !wp_verify_nonce( $_POST['nonce'], "ac_form_nonce")) {
        exit("Busted!");
    }
	// /*Get ip information from user*/
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	/*State Abbreviations*/
	$states = array(
	    'AL'=>'Alabama',
	    'AK'=>'Alaska',
	    'AZ'=>'Arizona',
	    'AR'=>'Arkansas',
	    'CA'=>'California',
	    'CO'=>'Colorado',
	    'CT'=>'Connecticut',
	    'DE'=>'Delaware',
	    'DC'=>'District of Columbia',
	    'FL'=>'Florida',
	    'GA'=>'Georgia',
	    'HI'=>'Hawaii',
	    'ID'=>'Idaho',
	    'IL'=>'Illinois',
	    'IN'=>'Indiana',
	    'IA'=>'Iowa',
	    'KS'=>'Kansas',
	    'KY'=>'Kentucky',
	    'LA'=>'Louisiana',
	    'ME'=>'Maine',
	    'MD'=>'Maryland',
	    'MA'=>'Massachusetts',
	    'MI'=>'Michigan',
	    'MN'=>'Minnesota',
	    'MS'=>'Mississippi',
	    'MO'=>'Missouri',
	    'MT'=>'Montana',
	    'NE'=>'Nebraska',
	    'NV'=>'Nevada',
	    'NH'=>'New Hampshire',
	    'NJ'=>'New Jersey',
	    'NM'=>'New Mexico',
	    'NY'=>'New York',
	    'NC'=>'North Carolina',
	    'ND'=>'North Dakota',
	    'OH'=>'Ohio',
	    'OK'=>'Oklahoma',
	    'OR'=>'Oregon',
	    'PA'=>'Pennsylvania',
	    'RI'=>'Rhode Island',
	    'SC'=>'South Carolina',
	    'SD'=>'South Dakota',
	    'TN'=>'Tennessee',
	    'TX'=>'Texas',
	    'UT'=>'Utah',
	    'VT'=>'Vermont',
	    'VA'=>'Virginia',
	    'WA'=>'Washington',
	    'WV'=>'West Virginia',
	    'WI'=>'Wisconsin',
	    'WY'=>'Wyoming',
	);
	/*Get page url*/
	$url = home_url();
	$ipdetails = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
	/*Add Abbreviation to json state call*/
	$state = ' ';
	foreach ($states as $key => $value) {
		if ( $ipdetails->region === $value ){
			$state = $key;
		}
	}
	// Form variables
	$name = $_POST['ac_name'];
	$phone = $_POST['ac_phone'];
	$email = trim($_POST['ac_email']);
	$option = $_POST['ac_select'];
	$choice = $_POST['ac_choice'];
	$ac_time = $_POST['ac_time'];
	$insurance = $_POST['ac_insurance'];
	$treatment = $_POST['ac_treatment'];
	$questions = $_POST['ac_message'];
	$captcha = $_POST['ac_code'];
	$total = $_POST['total'];
	$total1 = $_POST['total1'];
	/*Time*/
	$current_time = time(); 
	/*Start new form class to get widgets information*/
	$ac_form_class = new ac_form();
	$settings = $ac_form_class->get_settings();
	$to = array('helpline@fordetox.com', 'pbrooke@wstreatment.com' , 'newimage100@aol.com');
	$subject = $state . ' Contact Form';
	// Test input values for errors
	$errors = array();
	if(isset($_POST['interested'])) { // honey pot check
    die();
    } else {
	if(!$name) {
	  $errors[] = "No Name";
	}
	if(!$email) {
	  $errors[] = "No Email";
	} else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	  $errors[] = "Valid Email";
	}
	if(!ctype_digit($phone)) {
		if(!$phone) {
			$errors[] = "No Phone";
		} else {
			$errors[] = "Valid Phone";
		}
	}
	if(!$total1) {
		$errors[] = "No Number";
	} else if($total != $total1) {
		$errors[] = "Answer";
	}
	if($errors) {
		// Output errors and die with a failure message
		die( json_encode($errors) );
	} else {
		$message  = 'URL:' . home_url() . '<br>';
 		$message .= 'Name: ' . $name . '<br>';
	   	$message .= 'Phone: ' . $phone . '<br>'; 
	    $message .= 'Email: ' . $email . '<br>';
	    $message .= 'Select: ' . $option . '<br>';
	    $message .= 'Drug of Choice: ' . $choice . '<br>';
	    $message .= 'Time using drug: ' . $ac_time . '<br>';
	    $message .= 'Insurance: ' . $insurance . '<br>';
	    $message .= 'Treatment: ' . $treatment . '<br>';	
	    $message .= 'Message: ' . $questions ;
	    $headers[] = 'From: Microsite ' . $state . ' /<helpline@fordetox.com>';
		/*Send Email*/
	    wp_mail( $to, $subject, $message, $headers );
	    /*Insert Post with Post Meta*/
	    $ac_post = array(
			'post_title'    => $name . 'contact form',
			'post_content'  => $message,
			'post_status'   => 'publish',
			'post_type'		=> 'ac_form'
		);
		/*Insert the post while getting the id*/
		$post_id =  wp_insert_post( $ac_post );
		/*Update the meta to the database*/
		add_post_meta( $post_id, 'name', $name, true);
		add_post_meta( $post_id, 'phone', $phone, true);
		add_post_meta( $post_id, 'email', $email, true);
		add_post_meta( $post_id, 'select', $option, true);
		add_post_meta( $post_id, 'choice', $choice, true);
		add_post_meta( $post_id, 'time', $ac_time, true);
		add_post_meta( $post_id, 'insurance', $insurance, true);
		add_post_meta( $post_id, 'treatment', $treatment, true);
		add_post_meta( $post_id, 'questions', $questions, true);
		add_post_meta( $post_id, 'ip', $ip, true);
		add_post_meta( $post_id, 'current_time', $current_time, true);
		add_post_meta( $post_id, 'url', $url, true);
		if( $insurance === "no"){
			$insurance = false;
		}
		else{
			$insurance = true;
		}
		if( $treatment === "no"){
			$treatment = false;
		}
		else{
			$treatment = true;
		}
		//Send Form to Reports
		$username = 'Bh2P64xc30Ojq51NaXBvgWzDpzrqkHyd';
		$password = 'goleador7';
		$ac_url = 'http://formresults123.com/v1/forms';
		$method = 'POST';
		$data = array(
			'name'		=> $name, 
			'phone'		=> $phone, 
			'email'		=> $email, 
			'person' 	=> $option,
			'drug'		=> $choice, 
			'time'		=> $ac_time, 
			'insurance' => $insurance, 
			'treatment'	=> $treatment, 
			'comment'	=> $questions, 
			'ip' 		=> $ip, 
			'url' 		=> $url, 
			'sent'		=> $current_time 	
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $ac_url);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ; 
		curl_setopt($ch, CURLOPT_USERPWD,"$username:$password"); 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		$success = array();
		$success[] = 'success';
		die(json_encode($success));			
	}
	die(json_encode($errors));
}
}
// creating Ajax call for WordPress
add_action( 'wp_ajax_nopriv_ac_form_ajaxhandler', 'ac_form_ajaxhandler' );
add_action( 'wp_ajax_ac_form_ajaxhandler', 'ac_form_ajaxhandler' );