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
 * @since Version 4
 * @link https://github.com/ronmarasigan/LavaLust
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
* ------------------------------------------------------
*  Class Model / ORM
* ------------------------------------------------------
 */
class Model {
   
    /**
     * Class Constructor
     * @return void
     */
    public function __construct() {}

    /**
     * Table Name of the Database
     *
     * @var string
     */
    protected $table = '';

    /**
     * Primary Key of the Database Column
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * Column name to use for Soft Delete
     *
     * @var string
     */
    protected $soft_delete_column = 'deleted_at';

    /**
     * Allow Soft Delete of Rows (It will be added in config.config.php later on)
     *
     * @var boolean
     */
    protected $has_soft_delete = true;

    /**
     * Find Single Record
     *
     * @param integer $id
     * @param boolean $with_deleted
     * @return void
     */
    public function find($id, $with_deleted = false) {
        $this->db->table($this->table);
        $this->apply_soft_delete($with_deleted);
        return $this->db->where($this->primary_key, $id)->get();
    }

    /**
     * Return all Rows from the Database
     *
     * @param boolean $with_deleted
     * @return void
     */
    public function all($with_deleted = false) {
        $this->db->table($this->table);
        $this->apply_soft_delete($with_deleted);
        return $this->db->get_all();
    }

    /**
     * Insert Record to the Database
     *
     * @param array $data
     * @return void
     */
    public function insert($data) {
        $this->db->table($this->table)->insert($data);
        return $this->db->last_id();
    }

    /**
     * Update Record from the Database
     *
     * @param integer $id
     * @param array $data
     * @return void
     */
    public function update($id, $data) {
        return $this->db->table($this->table)->where($this->primary_key, $id)->update($data);
    }

    /**
     * Soft Delete. Check the column name to be added in the table. Check protected $soft_delete_column = 'deleted_at';
     *
     * @param integer $id
     * @return void
     */
    public function soft_delete($id) {
        if ($this->has_soft_delete) {
            return $this->update($id, [$this->soft_delete_column => date('Y-m-d H:i:s')]);
        }
        return $this->delete($id);
    }

    /**
     * Delete Records from the Database
     *
     * @param integer $id
     * @return void
     */
    public function delete($id) {
        return $this->db->table($this->table)->where($this->primary_key, $id)->delete();
    }

    /**
     * Where Clause
     *
     * @param array $conditions
     * @param boolean $with_deleted
     * @return void
     */
    public function where($conditions = [], $with_deleted = false) {
        $this->db->table($this->table);
        $this->apply_soft_delete($with_deleted);
        return $this->db->where($conditions);
    }

    /**
     * Raw SQL Query
     *
     * @param string $sql
     * @param array $params
     * @return void
     */
    public function raw($sql, $params = []) {
        return $this->db->raw($sql, $params);
    }
    /**
     * Apply Soft Delete when Displaying Records
     *
     * @param boolean $with_deleted
     * @return void
     */
    protected function apply_soft_delete($with_deleted) {
        if (!$with_deleted && $this->has_soft_delete) {
            $this->db->where_null($this->soft_delete_column);
        }
    }

    /**
     * ORM Has Many Relationship
     *
     * @param string $related_model
     * @param string $foreign_key
     * @param mixed $primary_key_value
     * @return boolean
     */
    public function has_many($related_model, $foreign_key, $primary_key_value) {
        $this->call->model($related_model);

        if ($primary_key_value) {
            return $this->{$related_model}->where([$foreign_key => $primary_key_value])->get_all();
        }
    
        return false;
    }

    /**
     * ORM Has One Relationship
     *
     * @param string $related_model
     * @param string $foreign_key
     * @param mixed $primary_key_value
     * @return boolean
     */
    public function has_one($related_model, $foreign_key, $primary_key_value) {
        $this->call->model($related_model);
    
        if ($primary_key_value) {
            return $this->{$related_model}->where([$foreign_key => $primary_key_value])->get();
        }
    
        return false;
    }
     /**
     * magic __get
     *
     * @param string $key
     * @return void
     */
    public function __get($key)
    {
        return lava_instance()->$key;
    }
}