<?php

ob_start();

	class MY_Controller extends CI_Controller

	{

		function __construct()

		{

			parent::__construct();

			

			if(!$this->session->has_userdata('is_logged_in'))

			{

				redirect('Authentication/login');

			}

			

		}

	}

?>



    