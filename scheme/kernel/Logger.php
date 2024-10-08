<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
/**
 * ------------------------------------------------------------------
 * LavaLust - an opensource lightweight PHP MVC Framework
 * ------------------------------------------------------------------
 *
 * MIT License
 *
 * Copyright (c) 2020 Ronald M. Marasigan
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package LavaLust
 * @author Ronald M. Marasigan <ronald.marasigan@yahoo.com>
 * @since Version 1
 * @link https://github.com/ronmarasigan/LavaLust
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
* ------------------------------------------------------
*  Class Logger
* ------------------------------------------------------
 */
class Logger {

    /**
     * log error etc
     *
     * @param string $type | debug, error
     * @param string $header
     * @param string $message
     * @param string $filename
     * @param string $linenum
     * @return void
     */
    public function log($type = '', $header = '', $message = '', $filename = '', $linenum = '')
    {
        $logfile = config_item('log_dir').'log.txt';
        if (! file_exists($logfile)) { 
            mkdir(config_item('log_dir'), 0777, true);
            $fh = fopen($logfile, 'w');
            fclose($fh);
        } 

        $date = date("d/m/Y G:i:s");

        if($type == 'debug' && (config_item('log_threshold') == 2 || config_item('log_threshold') == 3))
        {
            $err = "Date: ".$date."\n"."Debug Message: ".$header;
            $err .= "\n------------------------------------------------------------------\n\n";
            error_log($err, 3, $logfile);
        } else if($type == 'error' && (config_item('log_threshold') == 1 || config_item('log_threshold') == 3))
        {
            $message = is_array($message)? implode("\n", $message): $message;
            $err = "Date: ".$date."\n"."Exception Class: ".$header."\n"."Error Message: ".$message."\n"."Filename: ".$filename."\n"."Line Number: ".$linenum;
            $err .= "\n------------------------------------------------------------------\n\n";
            error_log($err, 3, $logfile);
        }  
    }
 }