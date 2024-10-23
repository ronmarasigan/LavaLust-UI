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
 * Class DBForge
 */
class DBForge {

    /**
     * LavaLust Super Object
     *
     * @var object
     */
    protected $_lava;

    /**
     * Table Columns
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Primary Key
     *
     * @var array
     */
    protected $primary_key = [];

    /**
     * Foreign Keys
     *
     * @var array
     */
    protected $foreign_keys = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_lava =& lava_instance();
        $this->_lava->call->database();
    }

    /**
     * Add Field Definition
     *
     * @param array $fields
     * @return void
     */
    public function add_field($fields)
    {
        foreach ($fields as $field => $details) {
            $this->fields[$field] = $details;
        }
    }

    /**
     * Define the primary key for the table
     *
     * @param mixed $key
     * @param boolean $primary
     * @return void
     */
    public function add_key($key, $primary = FALSE)
    {
        if ($primary) {
            $this->primary_key[] = $key;
        }
    }

    /**
     * Add Foreign Key Constraint
     *
     * @param string $field
     * @param string $reference_table
     * @param string $reference_field
     * @param string $on_delete
     * @param string $on_update
     * @return void
     */
    public function add_foreign_key($field, $reference_table, $reference_field, $on_delete = 'CASCADE', $on_update = 'CASCADE')
    {
        $this->foreign_keys[] = [
            'field' => $field,
            'reference_table' => $reference_table,
            'reference_field' => $reference_field,
            'on_delete' => $on_delete,
            'on_update' => $on_update
        ];
    }

    /**
     * Create Table
     *
     * @param string $table_name
     * @param boolean $if_not_exists
     * @return void
     */
    public function create_table($table_name, $if_not_exists = TRUE)
    {
        $columns = [];
        foreach ($this->fields as $field => $details) {
            $col = "$field {$details['type']}";
            if (isset($details['constraint'])) {
                $col .= "({$details['constraint']})";
            }
            if (isset($details['unsigned']) && $details['unsigned'] === TRUE) {
                $col .= " UNSIGNED";
            } else {
                $col .= " SIGNED";
            }
            if (isset($details['auto_increment']) && $details['auto_increment']) {
                $col .= " AUTO_INCREMENT";
            }
            if (isset($details['unique']) && $details['unique']) {
                $col .= " UNIQUE";
            }
            $columns[] = $col;
        }

        if (!empty($this->primary_key)) {
            $columns[] = "PRIMARY KEY (" . implode(", ", $this->primary_key) . ")";
        }

        foreach ($this->foreign_keys as $fk) {
            $columns[] = "FOREIGN KEY ({$fk['field']}) REFERENCES {$fk['reference_table']}({$fk['reference_field']}) ON DELETE {$fk['on_delete']} ON UPDATE {$fk['on_update']}";
        }

        $sql = "CREATE TABLE " . ($if_not_exists ? "IF NOT EXISTS " : "") . "$table_name (" . implode(", ", $columns) . ")";
        $this->execute($sql);
        echo "Table '$table_name' created successfully.\n";
    }

    /**
     * Drop Table
     *
     * @param string $table_name
     * @return void
     */
    public function drop_table($table_name)
    {
        $sql = "DROP TABLE IF EXISTS $table_name";
        $this->execute($sql);
        echo "Table '$table_name' dropped successfully.\n";
    }

    // Execute an SQL query
    protected function execute($sql)
    {
        return $this->_lava->db->raw($sql);
    }
}
