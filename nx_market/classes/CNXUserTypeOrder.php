<?php
/**
 * Imageprom
 * @package    nx
 * @subpackage nx_market
 * @copyright  2014 Imageprom
 */

namespace NXMarket;

class CNXUserTypeOrder extends \CUserTypeString {
   
   function GetUserTypeDescription() {
      return array(
         'USER_TYPE_ID' => 'nx_order',
         'CLASS_NAME' => 'NXMarket\CNXUserTypeOrder',
         'DESCRIPTION' => 'NXMarket - заказ',
         'BASE_TYPE' => 'string',
      );
   }

   function GetDBColumnType($arUserField) {
      global $DB;
      switch(strtolower($DB->type))
      {
         case 'mysql':
            return 'longtext';
         case 'oracle':
            return 'varchar2(200000 char)';
         case 'mssql':
            return 'varchar(200000)';
      }
   }

   function PrepareSettings($arUserField) {
      $size = intval($arUserField['SETTINGS']['SIZE']);
      $rows = intval($arUserField['SETTINGS']['ROWS']);
      $min = intval($arUserField['SETTINGS']['MIN_LENGTH']);
      $max = intval($arUserField['SETTINGS']['MAX_LENGTH']);

      return array(
         'SIZE' =>  ($size <= 1? 40: ($size > 255? 225: $size)),
         'ROWS' =>  ($rows <= 1?  4: ($rows >  50?  50: $rows)),
         'REGEXP' => $arUserField['SETTINGS']['REGEXP'],
         'MIN_LENGTH' => $min,
         'MAX_LENGTH' => $max,
         'DEFAULT_VALUE' => $arUserField['SETTINGS']['DEFAULT_VALUE'],
      );
   }

   static protected function GetNXFieldName($code) {
      $field = array(
         'ID' => 'Ид.',
         'PRICE' => 'Цена',
         'SUM' => 'Стоим.',
         'COUNT' => 'Кол-во',
         'NAME' => 'Товар',
         'NOTE' => 'Доп. поля',
         'art' => 'Арт.',
         'price_old' => 'Старая цена',
         'real_id' => 'ID товара',
      );

      if(!$field[$code]) return $code;
      else return $field[$code];

   }

   static protected function GetNXFieldSort($code) {
      $field = array(
         'ID' => 10,
         'PRICE' => 30,
         'COUNT' => 40,
         'SUM' => 50,
         'NAME' => 20,
         'NOTE' => 500,
      );

      if(!$field[$code]) return 100;
      else return $field[$code];

   }

   static function ArCmp($a, $b) {

      $a = self::GetNXFieldSort($a);
      $b = self::GetNXFieldSort($b);

      if ($a == $b) {
        return 0;
      }
      return ($a < $b) ? -1 : 1;
   }

   function GetEditFormHTML($arUserField, $arHtmlControl)
   {
      if($arUserField['ENTITY_VALUE_ID']<1 && strlen($arUserField['SETTINGS']['DEFAULT_VALUE'])>0)
         $arHtmlControl['VALUE'] = htmlspecialcharsbx($arUserField['SETTINGS']['DEFAULT_VALUE']);
        

      $orderData = json_decode(html_entity_decode ($arHtmlControl['VALUE']), true);
      $arColumn = array_keys(reset($orderData));
      $arColumn[] = 'SUM'; 
      uasort($arColumn, 'self::ArCmp');

      if(is_array($orderData)) {

         $orderTable = '<table cellpadding="5" style="width:100%; border:1px solid #9ea7b1; border-collapse: collapse;"><tr>';

         foreach ($arColumn as $column) {
            $orderTable .= '<th style="border:1px solid #9ea7b1; white-space:nowrap">'.self::GetNXFieldName($column).'</th>';
         }

         $orderTable .= '</tr>';

         foreach ($orderData as $row) {
            
            $orderTable .= '<tr>';
            $row['SUM'] = $row['PRICE'] * $row['COUNT'];

            foreach ($arColumn as $column) {
               
               $value = $row[$column];
               $cell = '';
               $style = '';

               switch ($column) {
                  case 'NOTE':
                     $style = 'style="border:1px solid #9ea7b1;"';
                     $cell .= '<small>';
                        foreach ($value as $key => $note) {
                           $cell .= '<span style="white-space:nowrap; display:block;">';
                           if(!$note) $note = ' - ';
                           $cell .= self::GetNXFieldName($key).': '.$note;
                           $cell .= '</span>';
                        }
                        $cell .= '</small>';
                     break;
                  
                  case 'SUM':
                  case 'PRICE':
                     $cell = nx_fprice($value);
                     $style = 'style="text-align:right; border:1px solid #9ea7b1;"';
                     break;

                  case 'ID':
                  case 'COUNT':
                     $cell = $value;
                     $style = 'style="text-align:center; border:1px solid #9ea7b1;"';
                     break;

                  default:
                     $style = 'style="border:1px solid #9ea7b1;"';
                     $cell = $value;
                     break;
               }
              
               $orderTable .= '<td '.$style.'>'.$cell.'</td>';

            }

            $orderTable .= '</tr>';

         }

         $orderTable .= '</table>';
         $orderTable = '<div style="width:100%; margin-bottom:2em;">'.$orderTable.'</div>';

      }

      else $orderTable = '';

      
      if($arUserField['SETTINGS']['ROWS'] < 2)
      {
         $arHtmlControl['VALIGN'] = 'middle';
         return $orderTable.'<input type="text" '.
            'name="'.$arHtmlControl['NAME'].'" '.
            'size="'.$arUserField['SETTINGS']['SIZE'].'" '.
            ($arUserField['SETTINGS']['MAX_LENGTH']>0? 'maxlength="'.$arUserField['SETTINGS']['MAX_LENGTH'].'" ': '').
            'value="'.$arHtmlControl['VALUE'].'" '.
            ($arUserField['EDIT_IN_LIST']!='Y'? 'disabled="disabled" ': '').
            '>';
      }
      else
      {
         return $orderTable.'<textarea '.
            'name="'.$arHtmlControl['NAME'].'" '.
            'cols="'.$arUserField['SETTINGS']['SIZE'].'" '.
            'rows="'.$arUserField['SETTINGS']['ROWS'].'" '.
            ($arUserField['SETTINGS']['MAX_LENGTH']>0? 'maxlength="'.$arUserField['SETTINGS']['MAX_LENGTH'].'" ': '').
            ($arUserField['EDIT_IN_LIST']!="Y"? 'disabled="disabled" ': '').
            '>'.$arHtmlControl['VALUE'].'</textarea>';
      }
   }

   function GetAdminListViewHTML($arUserField, $arHtmlControl) {
      if(strlen($arHtmlControl['VALUE']) > 0) {

         $orderTable = '';
         $orderData = json_decode(html_entity_decode ($arHtmlControl['VALUE']), true);
         $arColumn = array_keys(reset($orderData));
         $arColumn[] = 'SUM'; 
         uasort($arColumn, 'self::ArCmp');

         foreach ($orderData as $row) {

            $orderTable .= '<div style="border-bottom:1px dashed #9ea7b1; margin-bottom:1em;">';
            $row['SUM'] = $row['PRICE'] * $row['COUNT'];

            foreach ($arColumn as $column) {
               
               $value = $row[$column];

               $cell = '';
               $style = '';

               switch ($column) {
                  case 'NOTE':
                     $style = '';
                     $cell .= '<small style="display:block;">';
                        foreach ($value as $key => $note) {
                           $cell .= '<span style="white-space:nowrap; display:block;">';
                           if($note){
                              $cell .= self::GetNXFieldName($key).': '.$note;
                           }
                           $cell .= '</span>';
                        }
                        $cell .= '</small>';
                     break;
                  
                  case 'SUM':
                  case 'PRICE':
                     $cell = nx_fprice($value);
                     $style = 'style="margin:0;"';
                     break;

                  case 'ID':
                  case 'COUNT':
                     $cell = $value;
                     $style = 'style="margin:0;"';
                     break;

                   case 'NAME':
                     $cell = $value;
                     $style = 'style="margin:0 0 1em 0;"';
                     break;

                  default:
                     $style = 'style="margin:0;"';
                     $cell = $value;
                     break;
               }

               $orderTable .= '<p '.$style.'><b>'.self::GetNXFieldName($column).'</b>: '.$cell.'</p>';
              
            }

            $orderTable .= '</div>';

         }

         $orderTable = '<div style="min-width:200px;">'.$orderTable.'</div>';
         return $orderTable;


      }

      else
         return ' ';

   }
}