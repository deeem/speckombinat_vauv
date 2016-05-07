<?php

class Vauv_Phones_DB
{

    /**
     * @var array subscribers table meta-info
     */
    var $subscribers;

    public function __construct()
    {
        global $wpdb;

        $this->subscribers = array(
            'table_name' => $wpdb->prefix . 'vauv_phones',
            'column_formats' => array(
                'department' => '%d',
                'name' => '%s',
                'position' => '%s',
                'phone' => '%s',
                'parents' => '%s'),
            'sql_query_defaults' => array(
                'fields' => array(),
                'orderby' => 'name',
                'order' => 'desc',
                'number' => -1,
                'offset' => 0
            )
        );
    }

    /**
     * Список организаций
     * @return array элементы "первого" уровня (id родителя равен 0) без потомков в формате:
     * array(
     *       array('id' => 1, 'text' => 'name1'),
     *       array('id' => 2, 'text' => 'name2'),
     *       array('id' => 3, 'text' => 'name3')
     * );
     */
    public function getOrganizations()
    {
        $vauv_plugins = get_option('vauv_plugins');
        $departments = json_decode($vauv_plugins['phones']['departments']);

        $organizations = array();

        foreach ($departments as $node) {
            if ($node->parent == 0) {
                $organizations[] = array('id' => $node->id, 'text' => $node->text);
            }
        }

        return $organizations;
    }

    /**
     * Дерево отделов
     * @param int $parent идентификатор родительского отдела
     * @return array дерево потомков
     */
    public function getDepartments($parent = 0)
    {
        $vauv_plugins = get_option('vauv_plugins');
        $departments = json_decode($vauv_plugins['phones']['departments']);

        return $this->fetch_department_tree($parent, $departments);
    }

    /**
     * Рекурсивно ищет потомков отдела в дереве отделов
     * @param $parent идентификатор родителя
     * @param $tree дерево отделов
     * @return array отделы-потомки
     */
    private function fetch_department_tree($parent, &$tree)
    {
        foreach ($tree as $node) {
            if ($node->id == $parent) {
                return $node;
            } elseif (count($node->nodes)) {
                $r = $this->fetch_department_tree($parent, $node->nodes);
                if ($r !== null) {
                    return $r;
                }
            }
        }

        return null;
    }

    /**
     * Список абонентов
     * @param $department идентификатор отдела
     * @return array абоненты
     */
    public function getSubscribers($department)
    {
        global $wpdb;

        $query = array(
            'where' => $wpdb->prepare(' AND department=%d', $department)
        );

        return $this->get($query, $this->subscribers['sql_query_defaults'], $this->subscribers['table_name'], $this->subscribers['column_formats']);
    }

    /**
     * Найти абонентов по имени или номеру телефона
     * @param $searchfor поисковый запрос
     * @return array найденные абоненты
     */
    public function findSubscribers($searchfor)
    {

        $query = array(
            'where' => ' AND phone LIKE "' . $searchfor . '%" OR name LIKE "' . $searchfor . '%" '
        );

        return $this->get($query, $this->subscribers['sql_query_defaults'], $this->subscribers['table_name'], $this->subscribers['column_formats']);
    }

    /**
     * Import phones from xml file
     * @param $file xml-data to import
     */
    public function import_phones($file)
    {
        global $wpdb;
        $xml = simplexml_load_file($file, null, LIBXML_NOCDATA);
        $records = $xml->records;

        $departments_list = array();
        foreach ($records->record as $record) {

            $organizaciya = trim($record->Організація);
            $pidrozdil = trim($record->Підрозділ);
            $viddil = trim($record->ВідділЦехДільниця);
            $gruppa = trim($record->ГрупаСлужба);

            $subscriber = new stdClass();
            $subscriber->abonent = trim($record->Абонент);
            $subscriber->posada = trim($record->Посада);
            $subscriber->telefon = trim($record->Телефон);

            if (isset($gruppa) && $gruppa != '') {
                $departments_list[$organizaciya][$pidrozdil][$viddil][$gruppa][] = $subscriber;
            } elseif (isset($viddil) && $viddil != '') {
                $departments_list[$organizaciya][$pidrozdil][$viddil][] = $subscriber;
            } elseif (isset($pidrozdil) && $pidrozdil != '') {
                $departments_list[$organizaciya][$pidrozdil][] = $subscriber;
            } elseif (isset($organizaciya) && $organizaciya != '') {
                $departments_list[$organizaciya][] = $subscriber;
            }
        }

        $wpdb->query('TRUNCATE ' . $this->subscribers['table_name'] . ';');
        unlink($file);

        $departments_tree = array();
        $this->save_phones($departments_list, $departments_tree, array());

        $vauv_plugins = get_option('vauv_plugins');
        $vauv_plugins['phones']['departments'] = json_encode($departments_tree);
        update_option('vauv_plugins', $vauv_plugins);
    }

    /**
     * Преобразовать упорядоченный массив с предприятиями в adjacency list,
     * а абонентов сохранить в таблицу
     * @param $node array массив с предприятиями и абонентами
     */
    private function save_phones($node, &$tree, $parents)
    {
        static $last_id = 0;
        $parent_id = $last_id;
        $keys = array_keys($node);

        foreach ($keys as $key) {
            if (is_array($node[$key])) {

                $last_id++;

                $tree_node = new stdClass();
                $tree_node->text = $key;
                $tree_node->id = $last_id;
                $tree_node->parent = $parent_id;
                $tree_node->nodes = array();

                $tree_node->parents = $parents;
                $tree_node->parents[] = array(
                    'id' => $last_id,
                    'parent' => $parent_id,
                    'text' => $key
                );

                array_push($tree, $tree_node);

                $this->save_phones($node[$key], $tree_node->nodes, $tree_node->parents);
            } else {
                $data = array(
                    'department' => $parent_id,
                    'name' => $node[$key]->abonent,
                    'position' => $node[$key]->posada,
                    'phone' => $node[$key]->telefon,
                    'parents' => json_encode($parents)
                );
                $this->insert($data, $this->subscribers['table_name'], $this->subscribers['column_formats']);
            }
        }
    }

    /**
     * Insert data into table
     * @param array $data data to insert
     * @return int insert_id
     */
    function insert($data = array(), $table_name, $column_formats)
    {

        global $wpdb;

        //Force fields to lower case
        $data = array_change_key_case($data);

        //Whitelist columns
        $data = array_intersect_key($data, $column_formats);

        //Reorder $columns_formats to match the order of columns given in $data
        $data_keys = array_keys($data);
        $column_formats = array_merge(array_flip($data_keys), $column_formats);

        // Inserting data
        $wpdb->insert($table_name, $data, $column_formats);

        return $wpdb->insert_id;
    }

    /**
     * Get data from departments or subscribers tables
     *
     * @param array $query can contains the following keys:
     * 'fields' - an array of columns to include in returned roles. Or 'count' to count rows. For example: empty (all fields)
     * 'where' - string. For example: 'WHERE id=5'
     * 'orderby' - string. For example: id
     * 'order' - asc or desc. For example: desc
     * 'number' - records to return. Or -1 to return all. For example: -1
     * 'offset' - offset. For example: 0
     * @param array $defaults same as $query param for defaults params
     * @param string $table_name
     * @param string $allowed_fields Whitelist of allowed fields
     * @return array
     */
    public function get($query, $defaults, $table_name, $allowed_fields)
    {

        global $wpdb;

        $query = wp_parse_args($query, $defaults);
        extract($query);

        /* SQL SELECT */

        if (is_array($fields)) {
            //Convert fields to lowercase
            $fields = array_map('strtolower', $fields);
            //Sanitize by white listing
            $fields = array_intersect($fields, $allowed_fields);
        } else {
            $fields = strtolower($fields);
        }

        //Return only selected fields. Empty is interpreted as all
        if (empty($fields)) {
            $select_sql = "SELECT* FROM {$table_name}";
        } elseif ('count' == $fields) {
            $select_sql = "SELECT COUNT(*) FROM {$table_name}";
        } else {
            $select_sql = "SELECT " . implode(',', $fields) . " FROM {$table_name}";
        }

        /* SQL Where */

        $where_sql = 'WHERE 1=1';
        if (!empty($where)) {
            $where_sql .= $where;
        }

        /* SQL Order */

        //Whitelist order
        $order = strtoupper($order);
        $order = ('ASC' == $order ? 'ASC' : 'DESC');

        switch ($orderby) {
            case 'id':
                $order_sql = "ORDER BY id $order";
                break;
            default:
                break;
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
        $sql = "$select_sql $where_sql $order_sql $limit_sql";

        if ('count' == $fields) {
            return $wpdb->get_var($sql);
        }

        /* Perform query */
        $result = $wpdb->get_results($sql, ARRAY_A);

        return $result;
    }

}