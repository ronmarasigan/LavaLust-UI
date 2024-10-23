#!/usr/bin/php -q
<?php
(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('CLI only');

// Constant to define the application folder structure
define('APP_DIR', dirname(__DIR__, 1) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);

// Get command line arguments
$options = getopt('', ['make:']);

if (!isset($argv[1]) || !isset($argv[2])) {
    die("Usage: php cli.php make:controller ControllerName OR php cli.php make:model ModelName\n");
}

// Detect whether to create a controller or a model
$make_type = strtolower(str_replace('make:', '', $argv[1]));
$file_type = ($make_type === 'controller') ? 'Controller' : (($make_type === 'model') ? 'Model' : null);

if (!$file_type) {
    die("Invalid option. Use either 'make:controller' or 'make:model'\n");
}

// Get the class name
$class_name = ucfirst($argv[2]);

// Define the base content for the class file
$content = "<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class {class} extends {extends} {
    
    public function __construct()
    {
        parent::__construct();
    }
}
?>
";

// Set the file path based on the type of file (controller or model)
$sub_dir = strtolower($file_type) . 's';
$extends = ($file_type === 'Controller') ? 'Controller' : 'Model';

// If it's a model, append "_model" to the class name for convention
if ($file_type === 'Model') {
    $class_name .= '_model';
}

$file_path = APP_DIR . $sub_dir . '/' . $class_name . '.php';

// Check if the file already exists
if (!file_exists($file_path)) {
    // Create the file and replace placeholders in the content
    $file_handle = fopen($file_path, 'w');
    $search = ['{class}', '{extends}'];
    $replace = [$class_name, $extends];
    $final_content = str_replace($search, $replace, $content);

    // Write the content to the file
    fwrite($file_handle, $final_content);
    fclose($file_handle);

    echo success($file_type . ' "' . $class_name . '" was successfully created at ' . $file_path);
} else {
    echo danger($file_type . ' "' . $class_name . '" already exists.');
}

/**
 * Print error message in red
 */
function danger($string = '', $padding = true)
{
    $length = strlen($string) + 4;
    $output = '';

    if ($padding) {
        $output .= "\e[0;41m" . str_pad(' ', $length, " ", STR_PAD_LEFT) . "\e[0m" . PHP_EOL;
    }
    $output .= "\e[0;41m" . ($padding ? '  ' : '') . $string . ($padding ? '  ' : '') . "\e[0m" . PHP_EOL;
    if ($padding) {
        $output .= "\e[0;41m" . str_pad(' ', $length, " ", STR_PAD_LEFT) . "\e[0m" . PHP_EOL;
    }

    return $output;
}

/**
 * Print success message in green
 */
function success($string = '')
{
    return "\e[0;32m" . $string . "\e[0m" . PHP_EOL;
}
?>
