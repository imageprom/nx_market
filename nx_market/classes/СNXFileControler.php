<?php
/**
 * Imageprom
 * @package    nx
 * @subpackage nx_market
 * @copyright  2014 Imageprom
 */

namespace NXMarket;

interface INXFileManager {
	public function GetSourceFile();
	public function SetSourceFile();
	public function GetXmlFile($updateFile = true);
	public function Archive($prefix = '', $deleteFile = true);

}

class CNXFileManager implements INXFileManager {

	private $root;
	private $updateFolder;
	private $sourceFile;
	private $temp;
	private $bad;
	private $type;
	private $globalUpateFolder;
	private $archiveFolder;
	
	public function __construct($sourceFile = 'price.xml', $root = false, $updateFolder = '/update', $tempFolder = '/temp',  $badFolder = '/bad',  $archiveFolder = '/archive') { 
		try {
			
			if(!$root) $root = CNXConfig::$path['root'];
			$this->root = $root;
			$this->updateFolder = $updateFolder;
			$this->temp = $tempFolder;
			$this->bad = $badFolder;
			$this->updateFolder = $updateFolder;
			$this->sourceFile = $sourceFile;
			$this->globalUpateFolder = $this->root.$this->updateFolder;
			$this->archiveFolder = $archiveFolder;

			if (strpos($sourceFile, '.xml')){
				$this->type = 'xml';
			}
            elseif (strpos($sourceFile, '.txt')){
                $this->type = 'txt';
            }
            elseif (strpos($sourceFile, '.csv')){
                $this->type = 'csv';
            }
			elseif (strpos($sourceFile, '.zip')){
				$this->type = 'zip';
			}
			else {
				throw new Exception('Wrong type of file '.$sourceFile);
			}
		}
		
		catch (Exception $e) {
			echo "Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();
		} 
	}

	public function SetSourceFile($sourceFile = 'price.xml') {
		try {

			if (strpos($sourceFile, '.xml')){
				$this->type = 'xml';
				$this->sourceFile = $sourceFile;
			}

            elseif  (strpos($sourceFile, '.txt')){
                $this->type = 'txt';
                $this->sourceFile = $sourceFile;
            }

            elseif  (strpos($sourceFile, '.csv')){
                $this->type = 'csv';
                $this->sourceFile = $sourceFile;
            }

            elseif (strpos($sourceFile, '.zip')){
				$this->type = 'zip';
				$this->sourceFile = $sourceFile;
			}
			else {
				throw new Exception('Wrong type of file '.$sourceFile);
			}
		}

		catch (Exception $e) {
			echo "Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();
		} 
	}

    public function GetSourceFile()  {
        $path = $this->root.$this->updateFolder.'/'.$this->type.'/'.$this->sourceFile;
        return $path;
    }

	public function GetXmlFile($updateFile = true) {
		try {
			
			$xmlFile = $this->sourceFile;
					
			if($this->type == 'zip') {
				
				$xmlFile = str_replace('.zip', '.xml',  $xmlFile);
				$outputXML = $this->globalUpateFolder.$this->temp.'/'.$xmlFile;
				if(!$updateFile && is_readable($outputXML)) return $outputXML;
						
				$result = array();
		
				exec('unzip -o '.$this->globalUpateFolder.'/'.$this->type.'/'.$this->sourceFile.' -d '.$this->globalUpateFolder.$this->temp, $result);
				
				if (!$result[0] || !is_readable($outputXML)) {
					throw new Exception('Cant not unzipe file'.$this->sourceFile, 1);
				}
				else {
					return $outputXML;
				}
			}
			
			else {
				$outputXML = $this->globalUpateFolder.$this->temp.'/'.$xmlFile;

				if(!$updateFile && is_readable($outputXML)) return $outputXML;
				elseif(copy($this->globalUpateFolder.'/'.$this->type.'/'.$this->sourceFile, $outputXML)) {
					return $outputXML;
				}
			
				else {
					throw new Exception('Copy error '.$this->sourceFile, 2);
				}
			}
			
		}
		
		catch (Exception $e) {
			echo "Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();
			
			if($e->getCode() == 1) {
				copy($this->globalUpateFolder.'/'.$this->type.'/'.$this->sourceFile, $this->globalUpateFolder.$this->bad);
			}
			return false;
		} 
	}

    public function LoadFile() {
        try {

            $hand = '';

            if($_REQUEST['hand'] == 1 ){
                $hand = ' через форму ';
            }

            NXMessages::WriteLog(NXMessages::FormatLogMessage('Начата загрузка файла'.$hand, '#MESSAGE#', true));

            if(isset($_FILES) && count($_FILES)) {
                foreach($_FILES as $file) {

                    if (!strpos($this->sourceFile, '.txt')){
                        if($file['tmp_name']) {
                            if(copy($file['tmp_name'], $this->GetSourceFile())) {
                                NXMessages::FormatLogMessage('Загружен файл'.$hand, '#MESSAGE#', true);
                                NXMessages::WriteLog(NXMessages::FormatLogMessage('Загружен файл'.$hand, '#MESSAGE#', true));
                                return true;
                            }
                            else {
                                NXMessages::FormatLogMessage('Ошибка загрузки файла'.$hand, '#MESSAGE#', true);
                                NXMessages::WriteLog(NXMessages::FormatLogMessage('Ошибка загрузки файла'.$hand, '#MESSAGE#', true));
                            }
                        }
                    }
                    else {
                        throw new Exception('Wrong type of file '.$this->sourceFile);
                    }
                }
            }

        }

        catch (Exception $e) {
            echo "Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();
        }
    }

    public function GetFile($updateFile = true) {
        try {

            $xmlFile = $this->sourceFile;

            if($this->type == 'zip') {

                $xmlFile = str_replace('.zip', '.txt',  $xmlFile);
                $outputXML = $this->globalUpateFolder.$this->temp.'/'.$xmlFile;
                if(!$updateFile && is_readable($outputXML)) return $outputXML;

                $result = array();

                exec('unzip -o '.$this->globalUpateFolder.'/'.$this->type.'/'.$this->sourceFile.' -d '.$this->globalUpateFolder.$this->temp, $result);

                if (!$result[0] || !is_readable($outputXML)) {
                    throw new Exception('Cant not unzipe file'.$this->sourceFile, 1);
                }
                else {
                    return $outputXML;
                }
            }

            else {
                $outputXML = $this->globalUpateFolder.$this->temp.'/'.$xmlFile;
                $target = $this->globalUpateFolder.'/'.$this->type.'/'.$this->sourceFile;

                if(!$updateFile && is_readable($outputXML)) return $outputXML;
                elseif(copy($target, $outputXML)) {

                    return $outputXML;
                }

                else {
                    throw new Exception('Copy error '.$target, 2);
                }
            }

        }

        catch (Exception $e) {
            echo "Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();

            if($e->getCode() == 1) {
                copy($this->globalUpateFolder.'/'.$this->type.'/'.$this->sourceFile, $this->globalUpateFolder.$this->bad);
            }
            return false;
        }
    }
	
	public function Archive($prefix = '', $deleteFile = true) {
		try{
		    

			$archiveName = str_replace('.xml', '', $this->sourceFile);
			$archiveName = str_replace('.zip', '', $archiveName);
            $archiveName = str_replace('.csv', '', $archiveName);
            $archiveName = str_replace('.txt', '', $archiveName);
			$archiveName = $prefix.$archiveName.'_'.date('H-i-s_j-m-Y').'.xml';

			$temp_xml = $this->GetXmlFile(false);

	        if(copy($temp_xml, $this->globalUpateFolder.$this->archiveFolder.'/'.$archiveName)) {
				
				unlink($temp_xml);
				if($deleteFile) unlink ($this->globalUpateFolder.'/'.$this->type.'/'.$this->sourceFile);
				return true;
			}
			
			else throw new Exception('Copy error '.$this->sourceFile, 2);
		}
		
		catch (Exception $e) {
			echo "Error (File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();
			return false;
		} 
	}
}



/**
* 
*/
class CNXTailControler {

   	private $errors;
	private $warnings;
	
	private $path;
	private $filename;
	private $archive;
	private $source;

 
	function CNX_file_controler ($site_root, $source, $folder="/update/ostatki/", $archive) {
	   
	   //File System
	   $file_root = $site_root;
	   $this->path=$file_root.$folder;
	   $this->filename=$source;
	   $this->archive=$file_root.$archive;
  	   
	}
			
	function Archive ($town, $pseudo) {
	
	   // $filename=$this->path.$town."/".$this->filename;
        $filename = $this->path.$this->filename;		   
		if (!copy($filename,  $this->archive.$pseudo."_".date('His_j-m-Y').".xml")) {$this->warning[] = " Не удалось скопировать в архив ".$filename ; return false;}
		return true;
	}
	
	function Delete ($town) {
	   
	   // if($town) $src=$this->path.$town."/".$this->filename;
	   //else $src=$this->path.$this->filename;
	    $src = $this->path.$this->filename;
	    unlink($src);
		return true;
	}
	
	function GetFile($town = false) {
	    
		//if($town) $src=$this->path.$town."/".$this->filename;
		//else $src=$this->path.$this->filename;
		
		$src = $this->path.$this->filename;
		
		if (!file_exists($src) ) {
		    $this->warnings[] = " Внимание! Файл-источник /".$src." не найден! "; return false;
		}
				    
		$src_content=file_get_contents($src);
		$arr_content=xmlstr_to_array($src_content);
					  
		return $arr_content;			
	}
	
	
	function GetErrors() {
		if (count($this->errors)==0) return false;
		return $this->errors;
	}
	
	function GetWarnings() {
		if (count($this->warnings)==0) return false;
		return $this->warnings;
	}

}