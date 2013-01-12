<?php namespace Module\Templates;

/**
 * Email Template Events Class
 *
 * @author      Stephen Cozart
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Templates
 */
class Events {

    protected $fallbacks = array();

    public function __construct()
    {
        $this->fallbacks = array(
            'comments'	=> array('comments'	=> 'email/comment'),
            'contact'	=> array('contact'	=> 'email/contact')
        );

        //register the email event
        \Library\Events::register('email', array($this, 'send_email'));
    }

    public function send_email($data = array())
    {
        $slug = $data['slug'];
        unset($data['slug']);

        ci()->load->model('templates/email_templates_m');

		//get all email templates
		$templates = ci()->email_templates_m->get_templates($slug);

        //make sure we have something to work with
        if ( ! empty($templates))
        {
			$lang	   = isset($data['lang']) ? $data['lang'] : Settings::get('site_lang');
			$from	   = isset($data['from']) ? $data['from'] : Settings::get('server_email');
            $from_name = isset($data['name']) ? $data['name'] : null;
			$reply_to  = isset($data['reply-to']) ? $data['reply-to'] : $from;
			$to		   = isset($data['to']) ? $data['to'] : Settings::get('contact_email');

            // perhaps they've passed a pipe separated string, let's switch it to commas for CodeIgniter
            if ( ! is_array($to)) $to = str_replace('|', ',', $to);

            $subject = array_key_exists($lang, $templates) ? $templates[$lang]->subject : $templates['en']->subject ;
            $subject = ci()->parser->parse_string($subject, $data, true);

            $body = array_key_exists($lang, $templates) ? $templates[$lang]->body : $templates['en']->body ;
            $body = ci()->parser->parse_string($body, $data, true);

            ci()->email->from($from, $from_name);
            ci()->email->reply_to($reply_to);
            ci()->email->to($to);
            ci()->email->subject($subject);
            ci()->email->message($body);
			
			// To send attachments simply pass an array of file paths in Events::trigger('email')
			// $data['attach'][] = /path/to/file.jpg
			// $data['attach'][] = /path/to/file.zip
			if (isset($data['attach']))
			{
				foreach ($data['attach'] AS $attachment)
				{
					ci()->email->attach($attachment);
				}
			}

			return (bool) ci()->email->send();
        }

        //return false if we can't find the necessary templates
        return false;
    }
}
/* End of file Events.php */