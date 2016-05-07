<?php

class Vauv_Iplist_DB
{

    /**
     * @var string table name in database including wordpress prefix
     */
    var $table_name;
    var $column_formats;

    public function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'vauv_iplist';
    }

    /**
     * @return array iplist table columns
     */
    function table_columns()
    {
        return array(
            'ip' => '%d',
            'name' => '%s',
            'user' => '%s',
            'phone' => '%s'
        );
    }

    /**
     * Импорт из csv файла,
     * в котором разделителем служит ";"
     * и структура строки выглядит так:
     * ip;name;user;phone;
     * @param string $file путь к файлу
     */
    public function import($file = '')
    {
        global $wpdb;

        $wpdb->query('TRANCATE ' . $this->table_name . ';');
        $handle = fopen($file, 'r');
        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            $range = explode('.', $data[0]);
            $subnet = $range[0] . '.' . $range[1] . '.' . $range[2];
            if (!$this->isSubnetExists($subnet)) {
                $this->addSubnet($subnet);
            }
            $this->update(ip2long($data[0]), array('name' => $data[1], 'user' => $data[2], 'phone' => $data[3]));
        }
        fclose($handle);
    }

    /**
     * Адреса подсети
     * @param string $subnet
     * @return array
     */
    public function getList($subnet)
    {
        $range = explode('.', $subnet);

        $sql = array(
            'fields' => array('inet_ntoa(`ip`) AS `ip` ', '`name`', '`user`', '`phone`'),
            'where' => ' AND ip >=' . ip2long($range[0] . '.' . $range[1] . '.' . $range[2] . '.1')
                . ' AND ip <=' . ip2long($range[0] . '.' . $range[1] . '.' . $range[2] . '.255') . ' '
        );

        return $this->get($sql);
    }

    /**
     * Поиск
     * @param $string
     * @return array
     */
    public function search($string)
    {

        $sql = array(
            'fields' => array('inet_ntoa(`ip`) AS `ip` ', '`name`', '`user`', '`phone`'),
            'where' => ' AND `name` LIKE "' . $string . '%" OR `user` LIKE "' . $string . '%" OR `phone` LIKE "' . $string . '%"'
        );

        return $this->get($sql);
    }

    /**
     * Список подсетей
     * @return array
     */
    public function getSubnets()
    {
        $sql = array(
            'fields' => array('inet_ntoa(`ip`) AS `ip` ', '`name`', '`user`', '`phone`')
        );

        $iplist = $this->get($sql);

        $subnets = array();

        foreach ($iplist as $item) {
            $ip = explode('.', $item['ip']);
            $subnet = $ip[0] . '.' . $ip[1] . '.' . $ip[2];
            $subnets[$subnet] = false;
        }

        return array_keys($subnets);
    }

    /**
     * Добавить подсеть
     * Проверить присланную строку на соотвествие паттерну
     * и в случае удачи вставить в таблицу ip адреса этой подсети
     * @param string $string строковое представление подсети, например: '10.0.8'
     * @return array сообщение об ошибке в случае неудачи
     */
    public function addSubnet($string)
    {
        global $wpdb;

        $octet = explode('.', $string);
        if (count($octet) !== 3) {
            return array('error' => 'не соответствует шаблону 11.22.33');
        }

        foreach ($octet as $value) {
            if (!is_numeric($value)) {
                return array('error' => 'разрешены только числа');
            }
        }

        $subnet = $octet[0] . '.' . $octet[1] . '.' . $octet[2] . '.';

        // проверить наличие подсети
        if ($this->isSubnetExists($subnet)) {
            return array('error' => 'такая подсеть уже существует');
        }

        // в цикле добавить новые адреса
        for ($i = 1; $i < 256; $i++) {
            $data = array(
                'ip' => ip2long($subnet . $i),
                'name' => '',
                'user' => '',
                'phone' => ''
            );
            $wpdb->insert($this->table_name, $data, $this->table_columns());
        }

        return array('error' => '');
    }

    /**
     * Удалить все ip-адреса подсети
     * @param string $subnet строковое представление подсети, например: '10.0.8'
     */
    public function deleteSubnet($subnet)
    {
        $range = explode('.', $subnet);

        global $wpdb;
        $wpdb->query(
            'DELETE FROM ' . $this->table_name .
            ' WHERE `ip` >= ' . ip2long($range[0] . '.' . $range[1] . '.' . $range[2] . '.1') .
            ' AND `ip` <=' . ip2long($range[0] . '.' . $range[1] . '.' . $range[2] . '.255') . ';');
    }

    /**
     * Проверить существование подсети
     * методом поиска записей в диапазоне подсети
     * @param string $subnet строковое представление подсети, например: '10.0.8'
     * @return bool
     */
    public function isSubnetExists($subnet)
    {
        $range = explode('.', $subnet);

        $sql = array(
            'fields' => 'count',
            'where' => ' AND ip >=' . ip2long($range[0] . '.' . $range[1] . '.' . $range[2] . '.1')
                . ' AND ip <=' . ip2long($range[0] . '.' . $range[1] . '.' . $range[2] . '.255') . ' '
        );

        if ($this->get($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Проверить существование подсети
     * методом поиска в диапазоне подсети записей имеющих заполненные поля `name` и `user`
     * @param $subnet
     * @return bool строковое представление подсети, например: '10.0.8'
     */
    public function isSubnetEmpty($subnet)
    {
        global $wpdb;

        $range = explode('.', $subnet);

        $sql = 'SELECT count(*) FROM ' . $this->table_name .
            ' WHERE ip >=' . ip2long($range[0] . '.' . $range[1] . '.' . $range[2] . '.1') .
            ' AND ip <=' . ip2long($range[0] . '.' . $range[1] . '.' . $range[2] . '.255') .
            ' AND concat(`name`, `user`) != ""';

        $result = $wpdb->get_results($sql, ARRAY_N);
        if ($result[0][0] == '0') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update data in iplist table
     *
     * @param $ip
     * @param array $data
     * @return bool return false if update fails
     */
    function update($ip, $data = array())
    {

        global $wpdb;

        // Initialise column format array
        $column_formats = $this->table_columns();

        // Force fields to lower case
        $data = array_change_key_case($data);

        // Whitelist columns
        $data = array_intersect_key($data, $column_formats);

        // Reorder $subscribers_column_formats to match the order of columns given in $data
        $data_keys = array_keys($data);
        $column_formats = array_merge(array_flip($data_keys), $column_formats);

        if (false === $wpdb->update($this->table_name, $data, array('ip' => $ip), $column_formats)) {
            return false;
        }

        return true;
    }

    /**
     * Обнуляет данные присвоенные этому адресу
     *
     * @param $ip int ip2long представление адреса
     * @return bool return false if deleted fails
     */
    function delete($ip)
    {

        $this->update($ip, array('name' => '', 'user' => '', 'phone' => ''));

        // проверить диапазон, и если это была последняя запись, то удалить подсеть
        $octet = explode('.', long2ip($ip));
        $subnet = $octet[0] . '.' . $octet[1] . '.' . $octet[2] . '.';

        if ($this->isSubnetEmpty($subnet)) {
            $this->deleteSubnet($subnet);
            return 'subnet deleted';
        }
    }

    /**
     * Get data from iplist table
     *
     * @param array $query can contains the following keys:
     * 'fields' - an array of columns to include in returned roles. Or 'count' to count rows. Default: empty (all fields)
     * 'where' - string query. For example 'where' = $wpdb->prepare(' AND `id`=%d', $id)
     * 'number' - records to return. Or -1 to return all. Default: 25
     * 'offset' - offset. Default: 0
     * @return mixed
     */
    public function get($query = array())
    {

        global $wpdb;

        // Parse defaults
        $defaults = array(
            'fields' => array(),
            'number' => -1,
            'offset' => 0
        );

        $query = wp_parse_args($query, $defaults);
        extract($query);

        /* SQL SELECT */

        if (is_array($fields)) {
            //Convert fields to lowercase
            $fields = array_map('strtolower', $fields);
        } else {
            $fields = strtolower($fields);
        }

        //Return only selected fields. Empty is interpreted as all
        if (empty($fields)) {
            $select_sql = "SELECT * FROM {$this->table_name}";
        } elseif ('count' == $fields) {
            $select_sql = "SELECT COUNT(*) FROM {$this->table_name}";
        } else {
            $select_sql = "SELECT " . implode(',', $fields) . " FROM {$this->table_name}";
        }

        /* SQL Where */

        //Initialise WHERE
        $where_sql = 'WHERE 1=1';
        if (!empty($where)) {
            $where_sql .= $where;
        }

        /* SQL Limit */
        $offset = absint($offset); //Positive integer
        if ($number == -1) {
            $limit_sql = "";
        } else {
            $number = absint($number); //Positive integer
            $limit_sql = "LIMIT $offset, $number";
        }

        /* Form SQL statement */
        $sql = "$select_sql $where_sql $limit_sql";

        if ('count' == $fields) {
            return $wpdb->get_var($sql);
        }

        /* Perform query */
        $iplist = $wpdb->get_results($sql, ARRAY_A);

        return $iplist;
    }

}