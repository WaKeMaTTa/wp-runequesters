<?php

/**
* English language file
*
* @author       Stefan Gabos <contact@stefangabos.ro>
*/

$this->language = array(

	'clear_date'    => __('Clear date', WPRQ_TEXTDOMAIN),
	'csrf_detected' => __('There was a problem with your submission!<br>Possible causes may be that the submission has taken too long, or it represents a duplicate request.<br>Please try again.', WPRQ_TEXTDOMAIN),
	'days'          => array(
		__('Sunday', WPRQ_TEXTDOMAIN),
		__('Monday', WPRQ_TEXTDOMAIN),
		__('Tuesday', WPRQ_TEXTDOMAIN),
		__('Wednesday', WPRQ_TEXTDOMAIN),
		__('Thursday', WPRQ_TEXTDOMAIN),
		__('Friday', WPRQ_TEXTDOMAIN),
		__('Saturday', WPRQ_TEXTDOMAIN)
		),
	'days_abbr'     => false,   // will use the first 2 letters from the full name
	'months'        => array(
		__('January', WPRQ_TEXTDOMAIN),
		__('February', WPRQ_TEXTDOMAIN),
		__('March', WPRQ_TEXTDOMAIN),
		__('April', WPRQ_TEXTDOMAIN),
		__('May', WPRQ_TEXTDOMAIN),
		__('June', WPRQ_TEXTDOMAIN),
		__('July', WPRQ_TEXTDOMAIN),
		__('August', WPRQ_TEXTDOMAIN),
		__('September', WPRQ_TEXTDOMAIN),
		__('October', WPRQ_TEXTDOMAIN),
		__('November', WPRQ_TEXTDOMAIN),
		__('December', WPRQ_TEXTDOMAIN)
		),
	'months_abbr'   => false,   // will use the first 3 letters from the full name
	'new_captcha'   => __('Get a new code', WPRQ_TEXTDOMAIN),
	'other'         => __('Other...', WPRQ_TEXTDOMAIN),
	'select'        => __('- select -', WPRQ_TEXTDOMAIN),
	'spam_detected' => __('Possible spam attempt detected. The posted form data was rejected.', WPRQ_TEXTDOMAIN),
	'today'         => __('Today', WPRQ_TEXTDOMAIN),

	);

?>