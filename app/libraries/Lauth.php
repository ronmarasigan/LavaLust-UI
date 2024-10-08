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
 * @copyright Copyright 2020 (https://ronmarasigan.github.io)
 * @since Version 1
 * @link https://lavalust.pinoywap.org
 * @license https://opensource.org/licenses/MIT MIT License
 */

 /**
  * Auth Class
  * This will be remove in version 2.5
  * It can still be used then as independent library
  */
class Lauth {

	private $LAVA;

	public function __construct() {
		$this->LAVA =& lava_instance();
		$this->LAVA->call->database();
		$this->LAVA->call->library('session');
	}

	/**
	 * Password Default Hash
	 * @param  string $password User Password
	 * @return string  Hashed Password
	 */
	public function passwordhash($password)
	{
		$options = array(
		'cost' => 4,
		);
		return password_hash($password, PASSWORD_BCRYPT, $options);
	}

	/**
	 * [register description]
	 * @param  string $username  Username
	 * @param  string $password  Password
	 * @param  string $email     Email
	 * @param  string $usertype   Usertype
	 * @return $this
	 */
	public function register($username, $email, $password, $email_token)
	{
		$this->LAVA->db->transaction();
		$data = array(
			'username' => $username,
			'password' => $this->passwordhash($password),
			'email' => $email,
			'email_token' => $email_token
		);

		$res = $this->LAVA->db->table('users')->insert($data);
		if($res) {
			$this->LAVA->db->commit();
			return $this->LAVA->db->last_id();
		} else {
			$this->LAVA->db->roll_back();
			return false;
		}
	}

	/**
	 * Login
	 * @param  string $username Username
	 * @param  string $password Password
	 * @return string Validated Username
	 */
	public function login($email, $password)
	{				
    	$row = $this->LAVA->db
    					->table('users') 					
    					->where('email', $email)
    					->get();
		if($row) {
			if(password_verify($password, $row['password'])) {
					return $row['id'];
			} else {
				return false;
			}
		}
	}

	/**
	 * Change Password
	 *
	 * @param string $password
	 * @return void
	 */
	public function change_password($password) {
		$data = array(
					'password' => $this->passwordhash($password)
				);
		return  $this->LAVA->db
					->table('users')
					->where('user_id', $this->get_user_id())
					->update($data);
	}

	/**
	 * Set up session for login
	 * @param $this
	 */
	public function set_logged_in($user_id) {
		$session_data = hash('sha256', md5(time().$this->get_user_id()));
		$data = array(
			'user_id' => $user_id,
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'ip' => $_SERVER['REMOTE_ADDR'],
			'session_data' => $session_data
		);
		$res = $this->LAVA->db->table('sessions')
				->insert($data);
		if($res) $this->LAVA->session->set_userdata(array('session_data' => $session_data, 'user_id' => $user_id, 'logged_in' => 1));
	}

	/**
	 * Check if user is Logged in
	 * @return bool TRUE is logged in
	 */
	public function is_logged_in()
	{
		$data = array(
			'user_id' => $this->LAVA->session->userdata('user_id'),
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'session_data' => $this->LAVA->session->userdata('session_data')
		);
		$count = $this->LAVA->db->table('sessions')
						->select_count('session_id', 'count')
						->where($data)
						->get()['count'];
		if($this->LAVA->session->userdata('logged_in') == 1 && $count > 0) {
			return true;
		} else {
			if($this->LAVA->session->has_userdata('user_id')) {
				$this->set_logged_out();
			}
		}
	}

	/**
	 * Get User ID
	 * @return string User ID from Session
	 */
	public function get_user_id()
	{
		$user_id = $this->LAVA->session->userdata('user_id');
		return !empty($user_id) ? (int) $user_id : 0;
	}

	/**
	 * Get Username
	 * @return string Username from Session
	 */
	public function get_username($user_id)
	{
		$row = $this->LAVA->db
						->table('users')
						->select('username')					
    					->where('id', $user_id)
    					->limit(1)
    					->get();
    	if($row) {
    		return html_escape($row['username']);
    	}
	}

	public function set_logged_out() {
		$data = array(
			'user_id' => $this->get_user_id(),
			'browser' => $_SERVER['HTTP_USER_AGENT'],
			'session_data' => $this->LAVA->session->userdata('session_data')
		);
		$res = $this->LAVA->db->table('sessions')
						->where($data)
						->delete();
		if($res) {
			$this->LAVA->session->unset_userdata(array('user_id'));
			$this->LAVA->session->sess_destroy();
			return true;
		} else {
			return false;
		}
		
	}

	public function verify($token) {
		return $this->LAVA->db
						->table('users')
						->select('id')
						->where('email_token', $token)
						->where_null('email_verified_at')
						->get();	
	}

	public function verify_now($token) {
		return $this->LAVA->db
						->table('users')
						->where('email_token' ,$token)
						->update(array('email_verified_at' => date("Y-m-d h:i:s", time())));	

	}
	
	public function send_verification_email($email) {
		return $this->LAVA->db
						->table('users')
						->select('username, email_token')
						->where('email', $email)
						->where_null('email_verified_at')
						->get();	
	}
	
	public function reset_password($email) {
		$row = $this->LAVA->db
						->table('users')
						->where('email', $email)
						->get();
		if($this->LAVA->db->row_count() > 0) {
			$this->LAVA->call->helper('string');
			$data = array(
				'email' => $email,
				'reset_token' => random_string('alnum', 10)
			);
			$this->LAVA->db
				->table('password_reset')
				->insert($data)
				;
			return $data['reset_token'];
		} else {
			return FALSE;
		}
	}

	public function is_user_verified($email) {
		$this->LAVA->db
				->table('users')
				->where('email', $email)
				->where_not_null('email_verified_at')
				->get();
	return $this->LAVA->db->row_count();
	}

	public function get_reset_password_token($token)
	{
		return $this->LAVA->db
				->table('password_reset')	
				->select('email')			
				->where('reset_token', $token)
				->get();
	}

	public function reset_password_now($token, $password)
	{
		$email = $this->get_reset_password_token($token)['email'];
		$data = array(
			'password' => $this->passwordhash($password)
		);
		return $this->LAVA->db
				->table('users')
				->where('email', $email)
				->update($data);
	}

}

?>