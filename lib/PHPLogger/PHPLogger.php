<?php
	define("LOCAL_STORAGE_DIR",  './log/');
	define("LOG_FILE_NAME", 'log_' . date('m-d-y') . '.txt');		// The name of the log file
	
	define("DEBUG","DEBUG");
	define("INFO","INFO");
	define("NOTICE","NOTICE");
	define("WARNING","WARNING");
	define("ERROR","ERROR");
	
	
	/**
	 * This class is very helpful to write a Log in PHP
	 * just define the severity and the message
	 * You need to intialize the PHPLogger class and call it in the function
	 * This class will help you a lot in debugging the application.
	 * If practiced regularlly, this debug information will also solve the long run application errors
	 *
	 * The way to use this Logger is defined in testLogger.php file
	 *
	 * In case of any errors please report to singhgurdeep@gmail.com
	 */
class PHPLogger {
	private static $instance = 0;
	public static $USER_GROUP_RIGHTS = 0770;
	private $fileHandle;
	private $warningFileHandle;
	private $errorFileHandle;
	private static $severityDescription = Array(
			DEBUG => "Debug",
	 		INFO => "Info",
	 		NOTICE => "Notice",
	 		WARNING => "Warning",
	 		ERROR => "Error");

	/**
	 * PHPLogger constructor
	 *
	 * @access private
	 */
	private function __construct() {

		$this->severityDescription = 0;

		if (!file_exists(LOCAL_STORAGE_DIR)) {
			mkdir(LOCAL_STORAGE_DIR, self::$USER_GROUP_RIGHTS);
		}

		$this->fileHandle = fopen(LOCAL_STORAGE_DIR . LOG_FILE_NAME, "at+");

		if ($this->fileHandle === false) {
			print "Failed to obtain a handle to log file '" . LOG_FILE_NAME . "'";
		}

		$warningLogFile = LOCAL_STORAGE_DIR . "warnings_" . date("m-d-y") . ".txt";
		$this->warningFileHandle = fopen($warningLogFile, "at+");
		if ($this->warningFileHandle === false) {
			print "Failed to obtain a handle to warning log file '" . $warningLogFile . "'";
		}

		$errorLogFile = LOCAL_STORAGE_DIR . "errors.txt";
		$this->errorFileHandle = fopen($errorLogFile, "at+");
		if ($this->errorFileHandle === false) {
			print "Failed to obtain a handle to error log file '" . $errorLogFile . "'";
		}

	}

	/**
	 * returns an instance of the PHPLogger object
	 *
	 * @access public
	 * @static
	 *
	 * @return PHPLogger
	 */
	public static function getInstance() {
		if (!is_object(PHPLogger::$instance)) {
			PHPLogger::$instance = new PHPLogger();
		}
		return PHPLogger::$instance;
	}

	public function write($textMessage, $severityLevel = DEBUG, $action) {
		if ($severityLevel != DEBUG &&
		    $severityLevel != INFO &&
		    $severityLevel != NOTICE &&
		    $severityLevel != WARNING &&
		    $severityLevel != ERROR) {

		    print "Invalis severity input...";
			$severityLevel = DEBUG;
		}
		$textMessage = $this->formatMessage($textMessage, $severityLevel);

		if ($this->fileHandle !== false) {

			if (fwrite($this->fileHandle, $textMessage) === false) {
				print "There was an error writing to log file.";
			}
		}

		// if severity is WARNING then write to warning file
		if ($severityLevel == WARNING) {
			if ($this->warningFileHandle !== false) {
				fwrite($this->warningFileHandle, $textMessage);
			}
		}

		// if severity is ERROR then write to error file
		else if ($severityLevel == ERROR) {
			if ($this->errorFileHandle !== false) {
				fwrite($this->errorFileHandle, $textMessage);
			}
		}


	}

	/**
	 * closes the handle to the log file
	 *
	 * @access public
	 */
	public function close() {
		$success = fclose($this->fileHandle);
		if ($success === false) {
			// Failure to close the log file
			$this->write("PHPLogger failed to close the handle to the log file", ERROR_SEVERITY);
		}

		fclose($this->warningFileHandle);
		fclose($this->errorFileHandle);

		PHPLogger::$instance = 0;
	}

	/**
	 * formats the error message in representable manner
	 *
	 * @param message this is the message to be formatted
	 *
	 * @return $severity Severity level of the message
	 */
	private function formatMessage($message, $severity) {
		$msg = date("m-d-y") . " " . date("G:i:s") . " ";
		$msg .= $_SERVER['REMOTE_ADDR'];

		$IPLength = strlen($_SERVER['REMOTE_ADDR']);
		$numWhitespaces =  15 - $IPLength;
		for ($i=0; $i<$numWhitespaces; $i++) {
			$msg .= " ";
		}

		$msg .= " ".$severity.": ";

		//get the file name
		$lastSlashIndex = strrpos($_SERVER['PHP_SELF'], "/");
		if ($lastSlashIndex !== false) {
			$fileName = substr($_SERVER['PHP_SELF'], $lastSlashIndex+1);
		}
		else {
			$fileName = $_SERVER['PHP_SELF'];
		}

		$msg .= $fileName . "\t";

		$msg .= $this->getSeverityDescription($severity);
		$msg .= ": " . $message . "\r\n";

		return $msg;


	}

	/**
	 * returns the severityLevel
	 *
	 * @access private
	 * @param $severity One of severity levels supported by the logger
	 */
	private function getSeverityDescription($severity) {
		if (!array_key_exists($severity, self::$severityDescription)) {
			print "Invalid severity Level feed";
			return "Error";
		}		
		
		return @self::$severityDescription[$severityLevel];
	}
}


?>
