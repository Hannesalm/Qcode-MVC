<?php

namespace Anax\Users;

/**
 * Model for Users.
 *
 */
class CDatabaseModel implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    public function query($columns = '*')
    {
        $this->db->select($columns)
            ->from($this->getSource());

        return $this;
    }

    public function where($condition)
    {
        $this->db->where($condition);

        return $this;
    }

    public function andWhere($condition)
    {
        $this->db->andWhere($condition);

        return $this;
    }

    public function orderBy($condition)
    {
        $this->db->orderBy($condition);

        return $this;
    }

    public function limit($condition)
    {
        $this->db->limit($condition);

        return $this;
    }

    public function from($condition){
        $this->db->from($condition);

        return $this;
    }

    public function set($condition){
        $this->db->set($condition);

        return $this;
    }

    public function update($condition){
        $this->db->update($condition);

        return $this;
    }

    public function execute($params = [])
    {

        $this->db->execute($this->db->getSQL(), $params);
        $this->db->setFetchModeClass(__CLASS__);

        return $this->db->fetchAll();
    }
}