<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

class DownloadFile
{
    private string $uniqid='';
    private string $logDirectory='';
    private string $completeFileDirectory='';
    private string $temporaryFileDirectory='';
    private DOMDocument $dom;
    private string $logFile='';
	
    public function __construct()
    {
        $this->uniqid = uniqid();
        self::createLogDirectory();
        self::createLogFile();
        self::createCompleteFileDirectory();
        self::createTemporaryFileDirectory();
        $this->dom = new DOMDocument();
    }

    public function download(?array $argv=[])
    {
        self::log(__METHOD__."()");
	if($argv === null){
            self::log(__METHOD__."() SCRIPT ERROR:".PHP_EOL.": argv === null");
            exit();
	}
	if(!array_key_exists(1,$argv)){
            self::log(__METHOD__."() SCRIPT ERROR:".PHP_EOL." SET URL AS AN ARGUMENT OF SCRIPT");
            exit();
	}
        /*
         * SET URL
         */
	$url = $argv[1];
        self::log(__METHOD__."() URL:".PHP_EOL.$url);
	$client = new Client();
	$response = $client->request('GET', $url);

	$body = $response->getBody("<script>");
	self::readDOM($body);
    }

    public function download2(?array $argv=[])
    {
        self::log(__METHOD__."()");
        if($argv === null){
            self::log(__METHOD__."() SCRIPT ERROR:".PHP_EOL.": argv === null");          
           exit();
	}
	if(!array_key_exists(1,$argv)){
            self::log(__METHOD__."() SCRIPT ERROR:".PHP_EOL." SET URL AS AN ARGUMENT OF SCRIPT");
            exit();
	}	
	$url = $argv[1];
        $ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	// Dla stron HTTPS:
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$content = curl_exec($ch);
        if (curl_errno($ch)) {
            self::log(__METHOD__."() cURL ERROR:".PHP_EOL.curl_error($ch));
            echo 'Błąd cURL: ' . curl_error($ch);
	}
	else {
            //echo $content;
            self::readDOM($content);
	}
	curl_close($ch);
    }
	
    private function readDOM(string $content='')
    {
        self::log(__METHOD__."()");
		@$this->dom->loadHTML($content);
		$title = self::getPageTitle();

		self::readScripts($title);
		

	}

        function getPageTitle():string
        {
            try {
                self::log(__METHOD__."()");
                $title = $this->dom->getElementsByTagName('title')->item(0)->nodeValue;
                //echo $title."\r\n";
                $titleClean = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
                return $titleClean;
                //echo $titleClean; // Output: "Hll This is a tring with spcil chracters"
                
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
                return '';
            }
            return '';
        }
 
	private function readScripts(string $title='')
	{
            self::log(__METHOD__."()");
            $found = false;
		$scripts = $this->dom->getElementsByTagName('script');
		
		foreach ($scripts as $script) {
			$content = $script->nodeValue;
			$src = $script->getAttribute('src');
    
			if (!empty($src)) {
				//echo "External script: " . $src . "\n";
			} else {
				//echo "Inline script content:\n" . trim($content) . "\n\n";
				$found = self::lookForHlsM3u8( trim($content), trim($title));
			}
                        if($found){
                            break;
                        }
		}
	}
	
    private function lookForHlsM3u8(string $content='', string $title='')
    {
        self::log(__METHOD__."()");
	$test = explode('setVideoHLS(\'',$content);
	if(!array_key_exists(1,$test)){
            return;
	}
	$output_array = [];
	$m3u8 = null;
	$result = preg_match('/https\:\/\/(.)*hls.m3u8/',$content,$output_array);
		
	foreach($output_array as $output){
            if($output !== ''){
                $m3u8 = $output;
		break;
	}
	}
	if($m3u8 === null){
            return false;
	}
	//echo $m3u8;
	self::getContentOfHlsM3u8($m3u8,$title);
        return true;
    }

    private function getContentOfHlsM3u8(string $m3u8='',string $title='')
    {
        self::log(__METHOD__."()");

        $ch = curl_init();

        $url = $m3u8;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Dla stron HTTPS:
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $content = curl_exec($ch);

            $info = curl_getinfo($ch);

            if (curl_errno($ch)) {
                self::log(__METHOD__."() cURL ERROR:".PHP_EOL.curl_error($ch));
                curl_close($ch);
                return false;
            }
            if($info['http_code']!==200){
                self::log(__METHOD__."() cURL HTTP CODE ERROR:".PHP_EOL.$info['http_code']);
                curl_close($ch);
                return false;
            }

            if ($content === false || empty($content)) {
                self::log(__METHOD__."() cURL CONTENT ERROR:".PHP_EOL."No content received for {$url}. It might be an empty file or a download failure.");
                curl_close($ch);
                return false;
            }
            curl_close($ch);
            self::parseContentOfHlsM3u8($content,$m3u8,$title);
    }

    private function parseContentOfHlsM3u8(string $content='', string $m3u8='', string $title='')
    {
        self::log(__METHOD__."() content:".PHP_EOL.$content);
        $qualitySettings = [
            'hls-1080p','hls-720p','hls-480p','hls-360p','hls-250p'
        ];
        $tmp = preg_split('/\r\n|\r|\n/', $content);
        $downloadFileParts = null;
            
        foreach($qualitySettings as $quality){
            foreach($tmp as $data){
                if(preg_match('/^'.$quality.'/', $data)){
                    self::log(__METHOD__."() FOUND - ".$data);
                    $downloadFileParts = $data;
                    break 2;
                }
            }
        }
        self::downloadFileParts($downloadFileParts,$m3u8,$title);
    }

    private function downloadFileParts(?string $downloadFileParts=null, string $m3u8='', string $title='')
    {
        self::log(__METHOD__."()");
        if($downloadFileParts === null){
            self::log(__METHOD__."() ERROR:".PHP_EOL." downloadFileParts === null");
            return;
        }
        self::log(__METHOD__."() QUALITY:".PHP_EOL.$downloadFileParts);
        self::log(__METHOD__."() URL:".PHP_EOL.$m3u8);
        $quality = explode('.',$downloadFileParts);
        self::log(__METHOD__."() QUALITY[0]:".PHP_EOL.$quality[0]);
        $filesURL = substr($m3u8, 0,-8);
        self::log(__METHOD__."() FILES URL:".PHP_EOL.$filesURL);
        $curlError = false;
        $i = 1;
        /*
         * CREATE FILE LIST
         */
        $fileList = $this->temporaryFileDirectory."list.txt";
        /*
         * FILE TO DELETE
         */
        $toDelete=[];
        while($curlError !== true){
            /*
             *  Initialize cURL
             */
            $ch = curl_init();
            $fileName = $quality[0].strval($i++).".ts";
            $destination = $this->temporaryFileDirectory.$fileName;
            $url = $filesURL.$fileName;
            self::log(__METHOD__."() FILE URL:".PHP_EOL.$url);
            /*
             *  Set cURL options
             */
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the transfer as a string
            curl_setopt($ch, CURLOPT_HEADER, 0); // Don't return the header
            /*
             *  Optional: Set a timeout for the download (in seconds)
             */
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            /*
             *  Optional: Follow redirects
             */
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            /*
             *  Optional: User-Agent header (some servers require this)
             */
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

            // Optional: Handle SSL certificate issues (use with caution, better to fix certificate problems)
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            /*
             * Execute cURL and get the content
             */
            $fileContent = curl_exec($ch);

            $info = curl_getinfo($ch);

            if (curl_errno($ch)) {
                self::log(__METHOD__."() cURL ERROR:".PHP_EOL.curl_error($ch));
                $curlError = true;
                break;
            }
            if($info['http_code']!==200){
                //echo "cURL Error http_code " . $info['http_code'] . "\r\n";
                self::log(__METHOD__."() cURL HTTP CODE ERROR:".PHP_EOL.$info['http_code']);
                $curlError = true;
                break;
            }

            if ($fileContent === false || empty($fileContent)) {
                self::log(__METHOD__."() cURL CONTENT ERROR:".PHP_EOL."No content received for {$url}. It might be an empty file or a download failure.");
                $curlError = true;
                break;
            }

            $bytesWritten = file_put_contents($destination, $fileContent);

            if ($bytesWritten !== false) {
                self::log(__METHOD__."()".PHP_EOL."Successfully downloaded {$url}".PHP_EOL."Saved to {$destination} ({$bytesWritten} bytes).");
                $fileListToDelete = self::createFFMPEGList($fileList,$destination,$toDelete);
            }
            else {
                self::log(__METHOD__."()Error saving file:".PHP_EOL." {$destination}\r\n");
            }
            $toDelete[]=$destination;

            curl_close($ch);

        }
        self::log(__METHOD__."() MERGE");
        $command = "ffmpeg -loglevel quiet -f concat -safe 0 -i ".escapeshellarg($fileList)." -c copy ".escapeshellarg($this->completeFileDirectory.$title."_".$this->uniqid.".mp4");
        //$command = "ffmpeg -f concat -safe 0 -i ".escapeshellarg($fileList)." -c copy ".escapeshellarg($downloadCompleteDirectory.DIRECTORY_SEPARATOR.$title."_".$this->uniqid.".mp4");
        $output = shell_exec($command);

        self::log(__METHOD__."() FFMPEG".PHP_EOL.$output);

        if($fileListToDelete){
            $toDelete[]=$fileList;
        }
        /*
         * CLEAN
         */
        self::clean($toDelete);
    }
    
    private function createFFMPEGList(string $fileListPath='', string $filennamepart='')
    {
        $result = file_put_contents($fileListPath, "file '".$filennamepart."'\r\n", FILE_APPEND | LOCK_EX);
        if ($result === false) {
            error_log("Failed to write to file: {$fileListPath}"); // Log error internally
            return false;
        }
        return true;
    }
    
    private function clean(array $toDelete=[])
    {
        self::log(__METHOD__."()");
        foreach($toDelete as $del){
            unlink($del);
        }
        if(!rmdir($this->temporaryFileDirectory))
        {
            self::log(__METHOD__."() FAILED TO REMOVE DIRECTORY `".$this->temporaryFileDirectory."`");
        }
    }

    private function createLogDirectory():void
    {
        $this->logDirectory=__DIR__.DIRECTORY_SEPARATOR.'log';
        /*
         * CREATE DIRECTORY
         */
        self::createDirectory($this->logDirectory);
    }
    
    private function createCompleteFileDirectory():void
    {
        $this->completeFileDirectory=__DIR__.DIRECTORY_SEPARATOR.'complete';
        /*
         * CREATE DIRECTORY
         */
        self::createDirectory($this->completeFileDirectory);
        $this->completeFileDirectory.=DIRECTORY_SEPARATOR;
    }

    private function createTemporaryFileDirectory():void
    {
        $this->temporaryFileDirectory=__DIR__.DIRECTORY_SEPARATOR.'temporary';
        /*
         * CREATE DIRECTORY
         */
        self::createDirectory($this->temporaryFileDirectory);
        $this->temporaryFileDirectory.=DIRECTORY_SEPARATOR.$this->uniqid;
        /*
         * CREATE UNIQUE TEMPORARY DIRECTORY
         */
        self::createDirectory($this->temporaryFileDirectory);
        $this->temporaryFileDirectory.=DIRECTORY_SEPARATOR;
    }

    private function createDirectory(string $directoryName=''):void
    {
        $result = true;
        if (!file_exists($directoryName)) {
            $result = mkdir($directoryName, 0777, true);
        }
        if(!$result || !is_dir($directoryName)){
            echo "[".$this->uniqid."] ".__METHOD__."() ERROR CREATE DIRECTORY ".$directoryName."\r\n";
            exit();
        }
    }
    
    private function log(string $log=''):void
    {
        file_put_contents($this->logFile, "[".$this->uniqid."] ".$log.PHP_EOL, FILE_APPEND);
    }
    
    private function createLogFile():void
    {
        $date = date("Y-m-d_H-i-s");     
        $this->logFile = $this->logDirectory.DIRECTORY_SEPARATOR.'output_'.$date.'_'.$this->uniqid.'.log';
        $file = fopen($this->logFile, "w") or die("Unable to open file!");
        fwrite($file, "");
        fclose($file);
    }
}

$downloadFile = new DownloadFile();

$downloadFile->download($argv);
exit();