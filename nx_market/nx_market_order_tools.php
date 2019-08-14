<?php
/**
 * Imageprom
 * @package    nx
 * @subpackage nx_market
 * @copyright  2014 Imageprom
 */

namespace NXMarket;

use Bitrix\Main\Application;
use Bitrix\Main\DB\MssqlConnection;
use Bitrix\Main\DB\OracleConnection;
use Bitrix\Main\Entity;
use Bitrix\Main\Type\DateTime;


/**
 * @param $XML
 * @return array|bool
 */

function xml_to_array($XML) {
	// Clean up white space
	$XML = trim($XML);
	$returnVal = false; // Default if just text;
   
	// Expand empty tags
	$emptyTag = '<(.*)/>';
	$fullTag = '<\\1></\\1>';
	$XML = preg_replace ("|$emptyTag|", $fullTag, $XML);
    
    $XML = trim($XML);
 
	$matches = array();
	//if (preg_match_all('|<(.*)>(.*)</\\1>|Ums', trim($XML), $matches))
		
	if (preg_match_all('|<(.*)>(.*)</\\1>|Ums', $XML, $matches)) { 
	    if (count($matches[1]) > 0) $returnVal = array(); // If we have matches then return an array else just text
		foreach ($matches[1] as $index => $outerXML)
		{
			$attribute = $outerXML;
			$value = xml_to_array($matches[2][$index]);
			if (! isset($returnVal[$attribute])) $returnVal[$attribute] = array();
				$returnVal[$attribute][] = $value;
		}
	}
	 
	// Bring un-indexed singular arrays to a non-array value.
	if (is_array($returnVal)) foreach ($returnVal as $key => $value) {
		if (is_array($value) && count($value) == 1 && key($value) === 0) {
			$returnVal[$key] = $returnVal[$key][0];
		}
	}
	return $returnVal;
}

/**
 * Convert xml string to php array - useful to get a serializable value
 * @param $xmlstr
 * @return array|string
 * @author Adrien aka Gaarf
 */

function xmlstr_to_array($xmlstr) {
  $doc = new \DOMDocument();
  $doc->loadXML($xmlstr);
  return domnode_to_array($doc->documentElement);
}

/**
 * @param $node
 * @return array|string
 */

function domnode_to_array($node) {
  $output = array();
  switch ($node->nodeType) {
   case XML_CDATA_SECTION_NODE:
   case XML_TEXT_NODE:
    $output = trim($node->textContent);
   break;
   case XML_ELEMENT_NODE:
    for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
     $child = $node->childNodes->item($i);
     $v = domnode_to_array($child);
     if(isset($child->tagName)) {
       $t = $child->tagName;
       if(!isset($output[$t])) {
        $output[$t] = array();
       }
       $output[$t][] = $v;
     }
     elseif($v) {
      $output = (string) $v;
	   //$output = iconv("UTF-8", "WINDOWS-1251", $output);
     }
    }
    if(is_array($output)) {
     if($node->attributes->length) {
      $a = array();
      foreach($node->attributes as $attrName => $attrNode) {
       $a[$attrName] = (string) $attrNode->value;
      }
      $output['@attributes'] = $a;
     }
     foreach ($output as $t => $v) {
      if(is_array($v) && count($v)==1 && $t!='@attributes') {
       $output[$t] = $v[0];
      }
     }
    }
   break;
  }
  return $output;
}


/**
 * @param $message
 */

function WriteLog($message) {

  $fp = fopen($_SERVER['DOCUMENT_ROOT'].CNXConfig::$path['update'].CNXConfig::$path['log'], 'a+');
    fwrite($fp, $message);
  fclose($fp);
}

/**
 * @param $name
 * @param $result
 * @return string
 * @throws phpmailerException
 */

function SendMail ($name, $result) {

    $log = PHP_EOL.'======= '.$name.' ======= ';

    if($res = $result['LOG']['ERROR']->GetTextLog('#MESSAGE#'.PHP_EOL)) {
        $log .=  PHP_EOL.' ------- ERROR -------'.PHP_EOL.$res;
    }

    if($res = $result['LOG']['WARNING']->GetTextLog('#MESSAGE#'.PHP_EOL)) {
        $log .=  PHP_EOL.' ------- WARNING -------'.PHP_EOL.$res;
    }

    if($res = $result['LOG']['LOG']->GetTextLog('#MESSAGE#'.PHP_EOL)) {
        $log .=  PHP_EOL.' ------- LOG -------'.PHP_EOL.$res;
    }

    WriteLog($log);

    $message = '';

    if(count($result) > 0)  {
      $message .= '<h3 style="color:green;">Результаты работы</h3><div style="color:green;">';
      $message .= '<p>Обновлено разделов: '.intval($result['UPDATE_SECTION']).'</p>';
      $message .= '<p>Добавлено разделов: '.intval($result['NEW_SECTION']).'</p>';
      $message .= '<p>Обновлено элементов: '.intval($result['UPDATE_ELEMENT']).'</p>';
      $message .= '<p>Добавлено элементов: '.intval($result['NEW_ELEMENT']).'</p>';
      $message .= '<p>Деактивировано элементов: '.intval($result['DEACTIVATION_ELEMENTS']).'</p>';
      $message .= '<p>Время обновления раздела: '.$result['TIME_MINUTE'].' минут</p>';
      $message .= '</div>';

      if($res = $result['LOG']['ERROR']->GetTextLog()) $message .= '<div style="color:red;font-size:8pt;">'.$res.'</div>';
    }

    echo PHP_EOL.'======= '.$name.' ======= '.PHP_EOL.
        'Обновлено разделов: '.intval($result['UPDATE_SECTION']).PHP_EOL.
        'Добавлено разделов: '.intval($result['NEW_SECTION']).PHP_EOL.
        'Обновлено элементов: '.intval($result['UPDATE_ELEMENT']).PHP_EOL.
        'Добавлено элементов: '.intval($result['NEW_ELEMENT']).PHP_EOL.
        'Деактивировано элементов: '.intval($result['DEACTIVATION_ELEMENTS']).PHP_EOL.
        'Время обновления раздела: '.$result['TIME_MINUTE'].' минут'.PHP_EOL;

  
    $mail = new PHPMailer();
    $mail->CharSet = 'utf-8';
    $mail->AddReplyTo(CNXConfig::$mail['to'],'NoReply');
    $mail->SetFrom(CNXConfig::$mail['from'], 'Update of you_project.ru');

    foreach (CNXConfig::$mail['bcc'] as  $bcc) {
        $mail->AddBCC($bcc, '');
    }
  
    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional, comment out and test
    $mail->Subject = 'Обновление '.$name;
    $mail->MsgHTML($message);
  
    $mail->Send();

    return 'ok'.PHP_EOL;
}

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}