<?php

class Krishinc_Catalogrequest_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCatalogRequestPostUrl()
	{		 
        return $this->_getUrl('catalogrequest/index/createpost', array('_secure'=>true));		
	}

	public function getAllMarket()
	{
		return array(
			'Parent'	=>'Parent',
			'Home Educator' => 'Home Educator',
			'Preschool Teacher or Coordinator' => 'Preschool Teacher/Coordinator',
			'K-2 Teacher' => 'K-2 Teacher',
			'3-6 Teacher' => '3-6 Teacher',
			'K-6 Coordinator or Administrator' =>'K-6 Coordinator or Administrator',
			'Jr. High Teacher or Coordinator or Admin' => 'Jr. High Teacher/Coord/Admin',
            'High School Teacher or Coordinator or Admin' => 'High School Teacher/Coord/Admin',
            'K-12 Coordinator or Administrator' => 'K-12 Coord/Admin',
            'College Instructor' => 'College Instructor',
            'School of Ed. Professor' => 'School of Ed. Instructor',
            'Adult Ed. Teacher' => 'Adult Ed. Teacher',
            'Educational Consultant' => 'Educational Consultant',
            'Therapist' => 'Therapist',
            'Tutor' => 'Tutor',
            'Other' => 'Other',
		);
	}	
	
	public function getAllSubjects()
	{
		return array(
					   'N/A' => 'N/A',
			           'Multiple Subjects' => 'Multiple Subjects',
			           'Gifted' => 'Gifted',
			           'Remedial or at risk' => 'Remedial or at risk',
			           'Special Needs or Title I' => 'Special Needs or Title I',
			           'Language Arts' => 'Language Arts',
			           'Math' => 'Math',
			           'Science' => 'Science',
			           'Math and Science' => 'Math and Science',
			           'Social Studies' => 'Social Studies',
			           'ESL' => 'ESL',
		);
	}
	public function getAllHearAbout()
	{
		return array(
				    'Mail Catalog' => 'Mail Catalog',
		            'Friend/Teacher' => 'Friend/Teacher',
		            'Search Engine' => 'Search Engine',
		            'Review' => 'Review',
		            'Social Media' => 'Facebook, Twitter, etc',
		            'Forum/Blog' => 'Forum/Blog',
		            'Web Directory' => 'Web Directory',
		            'Reseller' => 'Retailer',
		            'Advertisement' => 'Advertisement',
		            'Other Source' => 'Other Source',
		);
	}
	
	
	/**
	 * ******Added for Export enhancement
	 *
	 * @return string
	 */
	public function getAllHearAboutForExport()
	{
		return array(
					''=>'WEB',
					'N/A'=>'WEB',
				    'Mail Catalog' => 'UNK',
		            'Friend/Teacher' => 'Friend',
		            'Search Engine' => 'WBS',
		            'Review' => 'RJ',
		            'Social Media' => 'WBM',
		            'Forum/Blog' => 'WBO',
		            'Web Directory' => 'WBO',
		            'Reseller' => 'Reseller',
		            'Advertisement' => 'RJ',
		            'Other Source' => 'WEB',
		);
	}
	
	/**
	 * ******Added for Export enhancement
	 *
	 * @return string
	 */
	public function getMarketValueForExport()
	{
		$market = array(
				    ''=>'P',
				    'N/A'=>'P',
					'Preschool Teacher or Coordinator'=>'E',
					'K-2 Teacher'=>'E',
					'3-6 Teacher'=>'E',
					'K-6 Coordinator or Administrator'=>'E',
					'Jr. High Teacher or Coordinator or Admin'=>'E',
					'High School Teacher or Coordinator or Admin'=>'E',
					'K-12 Coordinator or Administrator'=>'E',
					'College Instructor'=>'E',
					'School of Ed. Professor'=>'E',
					'Adult Ed. Teacher'=>'E',
					'Educational Consultant'=>'E',
					'Therapist'=>'E',
					'Tutor'=>'E',
					'Parent'=>'P',
					'Other'=>'P',
					'Home Educator'=>'H'
			);
			 
			return $market;
			
	}
}