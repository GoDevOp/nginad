<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace buyrtb\parsers\openrtb\parselets\common\device;
use \Exception;

class ParseDevice {
	
	public static function execute(&$Logger, \buyrtb\parsers\openrtb\OpenRTBParser &$Parser, \model\openrtb\RtbBidRequest &$RtbBidRequest, \model\openrtb\RtbBidRequestDevice &$RtbBidRequestDevice, &$device) {
	
		define('DEVICE_DESKTOP', 2);
		define('DEVICE_MOBILE', 1);
		define('DEVICE_TABLET', 5);
		
		$RtbBidRequestDevice->type = DEVICE_DESKTOP;
		
		if (!isset($Parser->json_post["device"])):
			return;
		endif;
		
		$default_device = $Parser->json_post["device"];
		
		if (isset($default_device["ip"])):
			$RtbBidRequestDevice->ip 		= $default_device["ip"];
		else:
			throw new Exception($Parser->expeption_missing_min_bid_request_params . ": device_ip");
		endif;
		
		if (isset($default_device["language"])):
			$RtbBidRequestDevice->bid_request_device_language 		= $default_device["language"];
		endif;
		 
		if (isset($default_device["ua"])):
		
			$RtbBidRequestDevice->ua 		= $default_device["ua"];
		
			if (strpos($RtbBidRequestDevice->ua, '%20') !== false):
				$RtbBidRequestDevice->ua = urldecode($RtbBidRequestDevice->ua);
			endif;
		
		endif;
		
		if (isset($default_device["model"])):
			 
			if (\mobileutil\MobileDeviceType::isPhone($default_device["model"]) === true):
			 
				$RtbBidRequestDevice->type = DEVICE_MOBILE;
				 
			elseif(\mobileutil\MobileDeviceType::isTablet($default_device["model"]) === true):
			 
				$RtbBidRequestDevice->type = DEVICE_TABLET;
				 
			endif;
			 
		elseif (isset($RtbBidRequestDevice->ua) && $RtbBidRequestDevice->ua != null):
		
			$detect = new \mobileutil\MobileDetect(null, $RtbBidRequestDevice->ua);
			 
			if ($detect->isTablet()):
			 
				$RtbBidRequestDevice->type = DEVICE_TABLET;
			 
			elseif ($detect->isMobile()):
			 
				$RtbBidRequestDevice->type = DEVICE_MOBILE;
			 
			endif;
			
		endif;
		
		if (isset($default_device["geo"])):
		
			$geo = $default_device["geo"];
			$RtbBidRequestGeo = new \model\openrtb\RtbBidRequestGeo();
			\buyrtb\parsers\openrtb\parselets\common\device\ParseGeo::execute($Logger, $Parser, $RtbBidRequest, $RtbBidRequestGeo, $geo);
			$RtbBidRequestDevice->RtbBidRequestGeo = $RtbBidRequestGeo;
				
		endif;
		
	}
	
}