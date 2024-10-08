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
*  Encryption Config
* ------------------------------------------------------
 */
Class Encryption {
    
    /**
     * Encryption Key
     *
     * @var string
     */
    private $encryption_key;

    /**
     * Cipher type
     *
     * @var string
     */
    private $method;
    
    public function __construct($method = '') {
        if(empty(config_item('encryption_key')))
        {
            throw new RuntimeException('Encryption key is empty. Please provide the key in your config.php file.');
        }
        $this->encryption_key = config_item('encryption_key');
        $this->method = empty($method) ? 'AES-256-CBC' : $method;
    }
    /**
     * Encypt input value
     *
     * @param string $input
     * @param string $key
     * @param string $method
     * @return void
     */
    public function encrypt($input)
    {
        $encrypt_iv = $this->_gen_encrypt_iv($this->encryption_key, openssl_cipher_iv_length($this->method));

        return base64_encode(openssl_encrypt($input, $this->method, $this->encryption_key, 0, $encrypt_iv));
    }
    
    /**
     * Decrypt input value
     *
     * @param string $input
     * @param string $key
     * @param string $method
     * @return void
     */
    public function decrypt($input)
    {
        $encrypt_iv = $this->_gen_encrypt_iv($this->encryption_key, openssl_cipher_iv_length($this->method));

        return openssl_decrypt(base64_decode($input), $this->method, $this->encryption_key, 0, $encrypt_iv);
    }

    /**
     * Generate Encrypt IV
     *
     * @param string $key
     * @param string $size
     * @return string
     */
    public function _gen_encrypt_iv($key, $size)
    {
        $hash = base64_encode(sha1($key));
        while(strlen($hash) < $size){
            $hash = $hash.$hash;
        }
        return substr($hash, 0, $size);
    }
}
