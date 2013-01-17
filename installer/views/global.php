<!DOCTYPE html>

<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js" lang="en"> <![endif]-->

<head>
	<meta charset="utf-8">

	<!-- You can use .htaccess and remove these lines to avoid edge case issues. -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title>PyroCMS Installer</title>

	<!-- <link href="<?php echo base_url(); ?>assets/css/style.css" rel="stylesheet"> -->
	<link href="<?php echo base_url(); ?>assets/css/main.css" rel="stylesheet">
	<link href="<?php echo base_url(); ?>assets/css/animate.css" rel="stylesheet">

	<script type="text/javascript">
		var base_url = '<?php echo base_url(); ?>',
				pass_match = ['<?php echo lang('installer.passwords_match'); ?>','<?php echo lang('installer.passwords_dont_match'); ?>'];
	</script>
	<script src="<?php echo base_url(); ?>assets/js/matchmedia.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/picturefill.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>

	<script src="<?php echo base_url(); ?>assets/js/jquery.complexify.js"></script>

	<script src="<?php echo base_url(); ?>assets/js/installer.js"></script>
</head>

<body id="installer">
	<!-- Part 1: Wrap all page content here -->
  <div id="wrap">
  
		<!-- Fixed navbar -->
		<header class="navbar navbar-static-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" ontouchstart="" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<div class="brand"><span id="logo"></span></div>		
					<!-- <div class="brand"></div> -->
					<div class="nav-collapse collapse">
						<ul id="lang">
							<?php foreach($language_nav as $lang => $info):?>
							<li>
								<a href="<?php echo $info['action_url']; ?>" title="<?php echo $info['name']; ?>">
									<div data-picture data-alt="<?php echo $info['name']; ?>">
										<div data-src="<?php echo $info['image_url']; ?>" data-media="(-webkit-min-device-pixel-ratio: 2),(min-device-pixel-ratio: 2)"></div>
										<div data-src="<?php echo $info['image_url']; ?>" data-media="(-webkit-min-device-pixel-ratio: 1),(min-device-pixel-ratio: 1)"></div>
										<noscript><img src="<?php echo $info['image_url']; ?>" alt="<?php echo $info['name']; ?>"/></noscript>
									</div>
								</a>
							</li>
							<?php endforeach; ?>
						</ul>
					</div><!-- end !nav-collapse -->
				</div><!-- end !container -->
			</div><!-- end !nav-inner -->
		</header><!-- end !navbar -->

		<div class="container">
			<div class="row-fluid">
				<div class="span12">
					
					<nav id="menu">
						<ul>
							<li><?php echo anchor('', lang('intro'), $this->uri->segment(2, '') == '' ? 'id="current"' : ''); ?></li>
							<li><span id="<?php echo $this->uri->segment(2, '') == 'step_1' ? 'current' : '' ?>"><?php echo lang('step1'); ?></span></li>
							<li><span id="<?php echo $this->uri->segment(2, '') == 'step_2' ? 'current' : '' ?>"><?php echo lang('step2'); ?></span></li>
							<li><span id="<?php echo $this->uri->segment(2, '') == 'step_3' ? 'current' : '' ?>"><?php echo lang('step3'); ?></span></li>
							<li><span id="<?php echo $this->uri->segment(2, '') == 'step_4' ? 'current' : '' ?>"><?php echo lang('step4'); ?></span></li>
							<li><span id="<?php echo $this->uri->segment(2, '') == 'complete' ? 'current' : '' ?>"><?php echo lang('final'); ?></span></li>
						</ul>
					</nav>
					
					<div class="progress progress-striped active">
						<div class="bar" style="width: 60%;"></div>
					</div>
					
				</div>
			</div>
		</div><!-- end !container -->
		
		<div class="container">
			<div class="row-fluid">
				<div class="span12">

				<!-- Message type 1 (flashdata) -->
				<?php if($this->session->flashdata('message')): ?>
					<ul class="block-message success <?php echo ($this->session->flashdata('message_type')) ? $this->session->flashdata('message_type') : 'block-message success'; ?>">
						<li><?php if($this->session->flashdata('message')) { echo $this->session->flashdata('message'); }; ?></li>
					</ul>
				<?php endif; ?>
		
				<!-- Message type 2 (validation errors) -->
				<?php if ( validation_errors() ): ?>
					<div id="notification">
						<ul class="block-message error">
							<?php echo validation_errors('<li>', '</li>'); ?>
						</ul>
					</div>
				<?php endif; ?>
		
				<?php echo $page_output . PHP_EOL; ?>
				
				</div>
			</div>
		</div><!-- end !container -->
			
		<div id="push"></div>
	</div><!-- end !wrap -->
	
	<footer id="footer">
		<div class="container">
			<span>Copyright &copy; 2009 - <?php echo date('Y'); ?> PyroCMS LLC</span>
			<br>
			<span id="version"><?php echo CMS_VERSION.' '.'Community'; ?></span>
		</div>
	</footer>

	<script src="<?php echo base_url(); ?>assets/js/bootstrap/bootstrap.min.js"></script>
</body>
</html>
