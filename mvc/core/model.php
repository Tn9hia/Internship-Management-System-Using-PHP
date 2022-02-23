<?php

/**
 * main model
 */
class Model extends Database
{
    public $errors = array();

    public function __construct()
    {
        // code...
        if(!property_exists($this, 'table'))
        {
            $this->table = strtolower(get_class($this)) . "s";
		}
    }


    protected function get_primary_key($table)
    {

        $query = "SHOW KEYS from $table WHERE Key_name = 'PRIMARY' ";
        $db = new Database();
        $data = $db->query($query);

        if(!empty($data[0]))
        {
            return $data[0]->Column_name;
        }
        return 'id';
    }

    public function where($column,$value,$orderby = 'desc',$limit = 10,$offset = 0)
    {

        $column = addslashes($column);
        $primary_key = $this->get_primary_key($this->table);

        $query = "select * from $this->table where $column = :value order by $primary_key $orderby limit $limit offset $offset";
        $data = $this->query($query,[
            'value'=>$value
        ]);

        //run functions after select
        if(is_array($data)){
            if(property_exists($this, 'afterSelect'))
            {
                foreach($this->afterSelect as $func)
                {
                    $data = $this->$func($data);
                }
            }
        }

        return $data;
    }

    public function first($column,$value,$orderby = 'desc')
    {

        $column = addslashes($column);
        $primary_key = $this->get_primary_key($this->table);

        $query = "select * from $this->table where $column = :value order by $primary_key $orderby";
        $data = $this->query($query,[
            'value'=>$value
        ]);

        //run functions after select
        if(is_array($data)){
            if(property_exists($this, 'afterSelect'))
            {
                foreach($this->afterSelect as $func)
                {
                    $data = $this->$func($data);
                }
            }
        }

        if(is_array($data)){
            $data = $data[0];
        }
        return $data;
    }

    public function findAll($orderby = 'desc',$limit = 100,$offset = 0)
    {

        $primary_key = $this->get_primary_key($this->table);

        $query = "select * from $this->table order by $primary_key $orderby limit $limit offset $offset";
        $data = $this->query($query);

        //run functions after select
        if(is_array($data)){
            if(property_exists($this, 'afterSelect'))
            {
                foreach($this->afterSelect as $func)
                {
                    $data = $this->$func($data);
                }
            }
        }

        return $data;

    }


    public function insert($data)
    {

        //remove unwanted columns
        if(property_exists($this, 'allowedColumns'))
        {
            foreach($data as $key => $column)
            {
                if(!in_array($key, $this->allowedColumns))
                {
                    unset($data[$key]);
                }
            }

        }

        //run functions before insert
        if(property_exists($this, 'beforeInsert'))
        {
            foreach($this->beforeInsert as $func)
            {
                $data = $this->$func($data);
            }
        }

        $keys = array_keys($data);
        $columns = implode(',', $keys);
        $values = implode(',:', $keys);

        $query = "insert into $this->table ($columns) values (:$values)";

        return $this->query($query,$data);
    }

    public function update($id,$data)
    {

        //remove unwanted columns
        if(property_exists($this, 'allowedColumns'))
        {
            foreach($data as $key => $column)
            {
                if(!in_array($key, $this->allowedColumns))
                {
                    unset($data[$key]);
                }
            }

        }

        //run functions before insert
        if(property_exists($this, 'beforeUpdate'))
        {
            foreach($this->beforeUpdate as $func)
            {
                $data = $this->$func($data);
            }
        }

        $str = "";
        foreach ($data as $key => $value) {
            // code...
            $str .= $key. "=:". $key.",";
        }

        $str = trim($str,",");

        $data['id'] = $id;
        $query = "update $this->table set $str where id = :id";

        return $this->query($query,$data);
    }

    public function delete($id)
    {

        $query = "delete from $this->table where user_id = :id";
        $data['id'] = $id;
        return $this->query($query,$data);
    }

    public function get_extra_data($id,$class) {
        $classes = $class.'s';
        $query = "select user_id, $class from $classes where user_id = '$id'";
        $data = $this->query($query);

        //run functions after select
        if(is_array($data)){
            if(property_exists($this, 'afterSelect'))
            {
                foreach($this->afterSelect as $func)
                {
                    $data = $this->$func($data);
                }
            }
        }

        if(is_array($data)){
            $data = $data[0];
        }
        return $data;
    }

    public function get_topic($id) {
        $query = "select user_id, topic_id from scores where user_id = '$id'";
        $data = $this->query($query);

        //run functions after select
        if(is_array($data)){
            if(property_exists($this, 'afterSelect'))
            {
                foreach($this->afterSelect as $func)
                {
                    $data = $this->$func($data);
                }
            }
        }

        if(is_array($data)){
            $data = $data[0];
        }
        return $data;
    }

    public function get_score($id) {
        $query = "select user_id, score from scores where user_id = '$id'";
        $data = $this->query($query);

        //run functions after select
        if(is_array($data)){
            if(property_exists($this, 'afterSelect'))
            {
                foreach($this->afterSelect as $func)
                {
                    $data = $this->$func($data);
                }
            }
        }

        if(is_array($data)){
            $data = $data[0];
        }
        return $data;
    }

    

    public function get_topic_name($id) {
        $query = "select topic_students.user_id, topics.topic from topics join topic_students on topics.topic_id = topic_students.topic_id where topic_students.user_id = '$id'";
        $data = $this->query($query);

        //run functions after select
        if(is_array($data)){
            if(property_exists($this, 'afterSelect'))
            {
                foreach($this->afterSelect as $func)
                {
                    $data = $this->$func($data);
                }
            }
        }

        if(is_array($data)){
            $data = $data[0];
        }
        return $data;
    }

    public function update_score($id,$score) {
        $query = "update scores set score = '$score' where user_id = '$id'";
        
        return (object) $this->query($query);
    }

    public function update_image($id,$image) {
        $query = "update users set image = '$image' where user_id = '$id'";
        return $this->query($query);
    }

}

