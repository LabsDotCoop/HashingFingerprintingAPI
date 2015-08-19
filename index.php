<?php
/**
 * Chronolabs REST Screening Selector API
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Chronolabs Cooperative http://labs.coop
 * @license         General Public License version 3 (http://labs.coop/briefs/legal/general-public-licence/13,3.html)
 * @package         screening
 * @since           1.0.2
 * @author          Simon Roberts <wishcraft@users.sourceforge.net>
 * @version         $Id: functions.php 1000 2013-06-07 01:20:22Z mynamesnot $
 * @subpackage		api
 * @description		Screening API Service REST
 * @link			https://screening.labs.coop Screening API Service Operates from this URL
 * @filesource
 */

	global $hashing;
	error_reporting(E_ERROR);
	ini_set('display_errors', true);
		
	define('MAXIMUM_QUERIES', 250*2355);
	ini_set('memory_limit', '256M');
	//include dirname(dirname(dirname(__FILE__))).'/web/public_html/mainfile.php';
	include dirname(__FILE__).'/hashes/hashes.php';
	include dirname(__FILE__).'/functions.php';
	
	if (!is_object($GLOBALS['hashing']))
		$GLOBALS['hashing'] = new hashesAPI();
	
	$help=false;
	if ((!isset($_GET['algorithm']) || empty($_GET['algorithm'])) || (!isset($_REQUEST['data']) || empty($_REQUEST['data']))) {
		$help=true;
	} elseif (isset($_GET['algorithm']) && !empty($_GET['algorithm']) && isset($_REQUEST['data']) && !empty($_REQUEST['data'])) {
		$output = (string)trim($_GET['output']);
		$algorithm = (string)trim($_GET['algorithm']);
		$data = (string)trim($_REQUEST['data']);		
	} else {
		$help=true;
	}
	
	if ($help==true) {
		//http_response_code(400);
		include dirname(__FILE__).'/help.php';
		exit;
	}
	//http_response_code(200);
	$data = doHash($data, $output, $algorithm);
	switch ($output) {
		default:
			echo '<h1>Checksum: ' . strtoupper($algorithm) . '</h1>';
			echo '<pre style="font-family: \'Courier New\', Courier, Terminal; font-size: 0.77em;">';
			if (!is_array($data))
				echo $data;
			else
				echo "{ '". implode("' } { '", $data) . "' }";
			echo '</pre>';
			break;
		case 'raw':
			if (!is_array($data))
				echo $data;
			else
				echo "{ '". implode("' } { '", $data) . "' }";
			break;
		case 'json':
			header('Content-type: application/json');
			echo json_encode($data);
			break;
		case 'serial':
			header('Content-type: text/html');
			echo serialize($data);
			break;
		case 'xml':
			header('Content-type: application/xml');
			$dom = new XmlDomConstruct('1.0', 'utf-8');
			$dom->fromMixed(array('root'=>$data));
 			echo $dom->saveXML();
			break;
	}
?>
		