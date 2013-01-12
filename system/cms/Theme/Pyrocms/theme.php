<?php 

namespace Theme\Pyrocms;

use \Settings;
use \SimplePie;

class Theme extends \Library\ThemeAbstract {

    public $name			= 'PyroCMS - Admin Theme';
    public $author			= 'PyroCMS Dev Team';
    public $author_website	= 'http://pyrocms.com/';
    public $website			= 'http://pyrocms.com/';
    public $description		= 'PyroCMS admin theme. HTML5 and CSS3 styling.';
    public $version			= '1.0.0';
	public $type			= 'admin';
	public $options 		= array(
		'pyrocms_recent_comments' => array(
			'title' 		=> 'Recent Comments',
			'description'   => 'Would you like to display recent comments on the dashboard?',
			'default'       => 'yes',
			'type'          => 'radio',
			'options'       => 'yes=Yes|no=No',
			'is_required'   => true
		),

		'pyrocms_news_feed' => 	array(
			'title' 		=> 'News Feed',
			'description'   => 'Would you like to display the news feed on the dashboard?',
			'default'       => 'yes',
			'type'          => 'radio',
			'options'       => 'yes=Yes|no=No',
			'is_required'   => true
		),

		'pyrocms_quick_links' => array(
			'title' 		=> 'Quick Links',
			'description'   => 'Would you like to display quick links on the dashboard?',
			'default'       => 'yes',
			'type'          => 'radio',
			'options'       => 'yes=Yes|no=No',
			'is_required'   => true
		),

		'pyrocms_analytics_graph' => array(
			'title' 		=> 'Analytics Graph',
			'description'   => 'Would you like to display the graph on the dashboard?',
			'default'       => 'yes',
			'type'          => 'radio',
			'options'       => 'yes=Yes|no=No',
			'is_required'   => true
		),
	);
	
	/**
	 * Run() is triggered when the theme is loaded for use
	 *
	 * This should contain the main logic for the theme.
	 */
	public function run()
	{
		// only load these items on the dashboard
		if (ci()->module == '' && ci()->method !== 'login' && ci()->method !== 'help')
		{
			// don't bother fetching the data if it's turned off in the theme
			if (ci()->theme_options->pyrocms_analytics_graph == 'yes')	{
				self::populateAnalytics();
			}
			if (ci()->theme_options->pyrocms_news_feed == 'yes') {
				self::populateRssFeed();
			}
			if (ci()->theme_options->pyrocms_recent_comments == 'yes')	{
				self::populateRecentComments();
			}
		}
	}

	/**
	 * Get Analytics
	 *
	 * Fetch Google Analytics information for the dashboard graph
	 *
	 * @return	void
	 */
	public function populateAnalytics()
	{
		if ( ! (Settings::get('ga_email') and Settings::get('ga_password') and Settings::get('ga_profile'))) {
			return;
		}
		
		// Not false? Return it
		if (($cached_response = ci()->cache->get('analytics'))) {
			$data['analytic_visits'] = $cached_response['analytic_visits'];
			$data['analytic_views'] = $cached_response['analytic_views'];
		}

		else {
			try {
				$analytics = new \Library\Analytics(array(
					'username' => Settings::get('ga_email'),
					'password' => Settings::get('ga_password'),
				));

				// Set by GA Profile ID if provided, else try and use the current domain
				$analytics->setProfileById('ga:'.Settings::get('ga_profile'));

				$end_date = date('Y-m-d');
				$start_date = date('Y-m-d', strtotime('-1 month'));

				$analytics->setDateRange($start_date, $end_date);

				$visits = $analytics->analytics->getVisitors();
				$views = $analytics->analytics->getPageviews();

				/* build tables */
				if (count($visits)) {
					foreach ($visits as $date => $visit) {
						$year = substr($date, 0, 4);
						$month = substr($date, 4, 2);
						$day = substr($date, 6, 2);

						$utc = mktime(date('h') + 1, null, null, $month, $day, $year) * 1000;

						$flot_datas_visits[] = '[' . $utc . ',' . $visit . ']';
						$flot_datas_views[] = '[' . $utc . ',' . $views[$date] . ']';
					}

					$flot_data_visits = '[' . implode(',', $flot_datas_visits) . ']';
					$flot_data_views = '[' . implode(',', $flot_datas_views) . ']';
				}

				$data['analytic_visits'] = $flot_data_visits;
				$data['analytic_views'] = $flot_data_views;

				// Call the model or library with the method provided and the same arguments
				ci()->cache->set('analytics', array('analytic_visits' => $flot_data_visits, 'analytic_views' => $flot_data_views), 60 * 60 * 6); // 6 hours
			}
			catch (Exception $e) {
				$data['messages']['notice'] = sprintf(lang('cp_google_analytics_no_connect'), anchor('admin/settings', lang('cp_nav_settings')));
			}
		}

		// make it available in the theme
		ci()->template->set($data);
	}

	/**
	 * Get RSS Feed
	 *
	 * Fetch articles for whatever RSS feed is in settings
	 */
	public function populateRssFeed()
	{
		// Dashboard RSS feed (using SimplePie)
		$pie = new SimplePie;
		$pie->set_cache_location(ci()->config->item('simplepie_cache_dir'));
		$pie->set_feed_url(Settings::get('dashboard_rss'));
		$pie->init();
		$pie->handle_content_type();
		
		ci()->template->rss_items = $pie->get_items(0, Settings::get('dashboard_rss_count'));
	}

	/**
	 * Get Recent Comments
	 *
	 * Fetch recent comments and work out what they attach to.
	 */
	public function populateRecentComments()
	{
		ci()->load->library('comments/comments');
		ci()->load->model('comments/comment_m');

		ci()->lang->load('comments/comments');

		$recent_comments = ci()->comment_m->get_recent(5);
		
		ci()->template->recent_comments = ci()->comments->process($recent_comments);
	}
}

/* End of file Theme.php */