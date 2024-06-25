<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterfaceEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $data = [
		    [
			    'name' => 'blog'
		    ],
		    [
			    'name' => 'entry'
		    ],
		    [
			    'name' => 'services'
		    ],
		    [
			    'name' => 'contacts'
		    ],
		    [
			    'name' => 'order_now'
		    ],
		    [
			    'name' => 'name'
		    ],
		    [
			    'name' => 'email'
		    ],
		    [
			    'name' => 'phone'
		    ],
		    [
			    'name' => 'message'
		    ],
		    [
			    'name' => 'send_message'
		    ],
		    [
			    'name' => 'enter_your_name'
		    ],
		    [
			    'name' => 'invalid_email'
		    ],
		    [
			    'name' => 'invalid_phone_number'
		    ],
		    [
			    'name' => 'leave_your_message'
		    ],
		    [
			    'name' => 'congratulation'
		    ],
		    [
			    'name' => 'you_just_sent_a_message'
		    ],
		    [
			    'name' => 'home'
		    ],
		    [
			    'name' => 'order_hosting'
		    ],
		    [
			    'name' => 'password'
		    ],
		    [
			    'name' => 'login_to_account'
		    ],
		    [
			    'name' => 'forgot_password'
		    ],
		    [
			    'name' => 'restore'
		    ],
		    [
			    'name' => 'company_id'
		    ],
		    [
			    'name' => 'incorrect_password'
		    ],
		    [
			    'name' => 'restore_password'
		    ],
		    [
			    'name' => 'confirm_password'
		    ],
		    [
			    'name' => 'password_successfully_saved'
		    ],
		    [
			    'name' => 'global_title'
		    ],
		    [
			    'name' => 'leave_an_order'
		    ],
		    [
			    'name' => 'address'
		    ],
		    [
			    'name' => 'ceo'
		    ],
		    [
			    'name' => 'web_development'
		    ],
		    [
			    'name' => 'service_landing_page'
		    ],
		    [
			    'name' => 'service_landing_page_url'
		    ],
		    [
			    'name' => 'service_website_card'
		    ],
		    [
			    'name' => 'service_website_card_url'
		    ],
		    [
			    'name' => 'service_online_store'
		    ],
		    [
			    'name' => 'service_online_store_url'
		    ],
		    [
			    'name' => 'service_corporate_business_site'
		    ],
		    [
			    'name' => 'service_corporate_business_site_url'
		    ],
		    [
			    'name' => 'service_blog_url'
		    ],
		    [
			    'name' => 'service_individual_development'
		    ],
		    [
			    'name' => 'service_individual_development_url'
		    ],
		    [
			    'name' => 'website_development'
		    ],
		    [
			    'name' => 'page_not_found'
		    ],
		    [
			    'name' => 'incorrect_address'
		    ],
		    [
			    'name' => 'server_error'
		    ],
		    [
			    'name' => 'request_error'
		    ],
		    [
			    'name' => 'contact_phone'
		    ],
		    [
			    'name' => 'contact_email'
		    ],
		    [
			    'name' => 'no_results_found'
		    ],
	    ];

	    DB::table('interface_entities')->insert($data);
    }
}
