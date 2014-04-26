<?php

class DTFileCheckSum {

    private $counter;
    private $sum;
    private $files;
	private $_pluginsDir;

    function __construct() {
        $this->counter = 0;
        $this->sum = '';
		$this->_pluginsDir=ABSPATH.'wp-content/plugins/'; // TODO plugins dir is dynamic?
    }

    function listFolderFiles($dir) {
        $ffs = scandir($dir);

        foreach ($ffs as $ff) {

            if ($ff != '.' && $ff != '..') {
                if (!is_dir($dir . '/' . $ff)) {
                    $sum = md5_file($dir . '/' . $ff);
                   
                    $this->files[$dir . '/' . $ff] = $sum;
                    $this->counter++;
                    $this->sum.= $sum;
                } else {
                   
                }
                if (is_dir($dir . '/' . $ff)) {
                    $this->listFolderFiles($dir . '/' . $ff);
                }

             
            }
        }
    }

    public function getCounter() {
        return $this->counter;
    }

    public function getSum() {
        return $this->sum;
    }

    public function getFiles() {
        return $this->files;
    }

	public function getChanges($options) {

		$limit = (isset($options['limit'])) ? $options['limit'] : 10;
		$offset = (isset($options['offset'])) ? $options['limit'] : 0;
		$changeset = (isset($options['changeset'])) ? $options['changeset'] : null;

		$data=get_option(DTFILECHECKLOG);
		$returnVar = array();
		$changeset=0;

		if (!is_array($data))
			return array();

		foreach ($data as $row) {
			$returnVar[] = (object) array (
									'date' =>$row[0],
									'filecount' =>$row[1],
									'md5' =>$row[2],
									'filechanges' =>$row[3],
									'changeset' => $changeset
									);
			$changeset++;
		}

		return array_reverse($returnVar);
	}

	public function runCron() {

		$this->listFolderFiles(ABSPATH);
		$info = get_option(DTFILECHECKLOG);
		$infoOld = $info;

		$date = new DateTime();
		$lastFiles = get_option(DTFILECHECKLOGLIST);

		if ($infoOld[0][2] != md5($this->getSum())) {
			$diff1 = array_diff_assoc($this->getFiles(), $lastFiles);
			$diff2 = array_diff_assoc($lastFiles, $this->getFiles());
			$diff = array_merge($diff1, $diff2);
		}

		// Altered or deleted?
		$diffFiltered=array();
		$currentFiles=$this->getFiles();
		foreach ($diff as $thisFile => $md5sum) {
			$diffFiltered[$thisFile]=array();
			if (isset($currentFiles[$thisFile])) {
				if (!isset($lastFiles[$thisFile])) {
					$diffFiltered[$thisFile]['change'] = 'ADDED';
					$diffFiltered[$thisFile]['md5current']=$currentFiles[$thisFile];
				} else {
					$diffFiltered[$thisFile]['change'] = 'ALTERED';
					$diffFiltered[$thisFile]['md5current']=$currentFiles[$thisFile];
					$diffFiltered[$thisFile]['md5previous']=$lastFiles[$thisFile];
				}
			} else {
				$diffFiltered[$thisFile]['change'] = 'DELETED';
			}
		}

		$info[] = array($date->format('Y-m-d H:i:s'), $this->getCounter(), md5($this->getSum()), $diffFiltered);

		update_option(DTFILECHECKLOG, $info);
		update_option(DTFILECHECKLOGLIST, $this->getFiles());
	}

	function findHooks($givenHooks)
	{
		global $wp_filter;

		$this->listFolderFiles($this->_pluginsDir);
		$files=$this->getFiles();
		$return=array();
		foreach ($givenHooks as $searchHook)
		{
			if (!isset($wp_filter[$searchHook]))
				continue;

			foreach ($wp_filter[$searchHook] as $hookkey => $hookArray)
			{
				foreach ($hookArray as $functionName => $functionValues)
				{

					$hookIsClass=false;
					if (gettype($functionValues['function']) == "string") {
						$searchFor = $functionName;
					} else {
						$rawName = print_r($functionValues['function'][0], true);
						$searchFor = substr($rawName, 0, strpos($rawName, ' Object'));
						$hookIsClass=true;
					}

					$pluginFile=false;
					foreach ($files as $filename => $md5) {
						if (strpos($filename, 'php') === false)
							continue;
						$contents = file_get_contents($filename);
						if (strpos($contents, $searchFor) !== false) {
							$pluginFile=$filename;
							break;
						}
					}
					reset($files);

					$pluginData=get_plugin_data($pluginFile);
					$return[] = (object)array(
												'hook' => $searchHook,
												'class' => $hookIsClass,
												'function' => $searchFor,
												'plugin' => $pluginData['Name'],
												'file' => $pluginFile
											);
				}
			}
		}

		return $return;
	}

	function findFunctions($givenFunctions)
	{

		$this->listFolderFiles($this->_pluginsDir);
		$files=$this->getFiles();
		$return=array();
		$pluginNames=array();
		$pluginOutput=array();

		foreach ($files as $filename => $md5) {
			if (strpos($filename, 'php') === false)
				continue;
			try
			{
				$contents = file_get_contents($filename);
			} catch (Exception $e) { 
				echo 'Failed to open file. '.$e->getMessage();	
				next;
			}

			$matches=array();
			$relFileName = str_replace($this->_pluginsDir, '', $filename);
			preg_match('/\/([a-zA-Z0-9\-_]*)/', $relFileName, $matches);
			$pluginParentDir = $matches[1];

			if (strpos($contents, 'Plugin Name')) {
				$pluginData=get_plugin_data($filename);
				$pluginNames[$pluginParentDir]=$pluginData['Name']; 
			}

			foreach ($givenFunctions as $searchFunction)
			{
				if (strpos($contents, $searchFunction) !== false && strpos($filename, 'diagnostic-tool') === false)
				{
					$pluginLoc=str_replace($this->_pluginsDir, '', $filename);
					$pluginOutput[] = array($searchFunction.')', $pluginLoc, $pluginParentDir);
				}
			}
		}

		foreach ($pluginOutput as $key => $values)
		{
			$pluginData=get_plugin_data($values[1]);

			// TODO: not clean
			if ($pluginData['Name'] == '')
			{
				$pluginData['Name'] = $pluginNames[$values[2]];				
			}
	
			$return[] = (object) array(
									'function' => $values[0],
									'file' => $values[1],
									'plugin' => $pluginData['Name']
							);
		}

		return $return;

	}

}
