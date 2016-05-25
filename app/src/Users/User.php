<?php

namespace Anax\Users;

/**
* Model for Users.
*
*/
class User extends \Anax\Users\CDatabaseModel {

    /**
     * The login section
     *
     */

    public function setSessionVariablesAtLogin($res){

        $this->session->set('userName', $res[0]->name);
        $this->session->set('loggedIn', 1);
        $this->session->set('userID', $res[0]->id);
    }

    public function find($user){
        $this->db->select()
            ->from($this->getSource())
            ->where("id = ?");

        $this->db->execute([$user]);


        return $this->db->fetchInto($this);
    }

    public function findUser($user){
        $this->db->select()
            ->from($this->getSource())
            ->where("username = ?");

        $this->db->execute([$user]);


        return $this->db->fetchAll($this);
    }

    public function findMostActive(){
        $this->db->select()->from($this->getSource())->orderBy('score DESC');

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    public function logOut(){
        $this->session->destroy();
    }

    public function isAuthenticated(){
        if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == 1){
            return true;
        }
        return false;
    }

    /**
     * Get the table name.
     *
     * @return string with the table name.
     */
    public function getSource()
    {
        return strtolower(implode('', array_slice(explode('\\', get_class($this)), -1)));
    }

    /**
     * Get object properties.
     *
     * @return array with object properties.
     */
    public function getProperties()
    {
        $properties = get_object_vars($this);
        unset($properties['di']);
        unset($properties['db']);

        return $properties;
    }

    /**
     * Set object properties.
     *
     * @param array $properties with properties to set.
     *
     * @return void
     */
    public function setProperties($properties)
    {
        // Update object with incoming values, if any
        if (!empty($properties)) {
            foreach ($properties as $key => $val) {
                $this->$key = $val;
            }
        }
    }

    /**
     * Find and return all.
     *
     * @return array
     */
    public function findAll()
    {
        $this->db->select()->from($this->getSource());

        $this->db->execute();
        $this->db->setFetchModeClass(__CLASS__);
        return $this->db->fetchAll();
    }

    /**
     * Find and return specific.
     *
     * @return this
     */
    public function findQuestionsForUser($id)
    {
        $this->db->select()
            ->from($this->getSource())
            ->where("id = ?");

        $this->db->execute([$id]);
        return $this->db->fetchInto($this);
    }
    public function findAnswersForUser($id)
    {
        $this->db->select()
            ->from('comments')
            ->where("user_id = ?")
            ->andWhere('comment_parent_id IS NULL');

        $this->db->execute([$id]);
        return $this->db->fetchAll($this);
    }

    public function findCommentsForUser($id)
    {
        $this->db->select()
            ->from('comments')
            ->where("user_id = ?")
            ->andWhere('question_id IS NULL');

        $this->db->execute([$id]);
        return $this->db->fetchAll($this);
    }


    public function findActive()
    {
        $this->db->select()
            ->from($this->getSource())
            ->where('status = \'Active\'')
            ->execute();

        return $this->db->fetchAll($this);

    }
    public function findDeactive()
    {
        $this->db->select()
            ->from($this->getSource())
            ->where('status = \'Deactive\'')
            ->execute();

        return $this->db->fetchAll($this);

    }

    public function findDeleted()
    {
        $this->db->select()
            ->from($this->getSource())
            ->where('deleted IS NOT NULL')
            ->execute();

        return $this->db->fetchAll($this);

    }


    /**
     * Save current object/row.
     *
     * @param array $values key/values to save or empty to use object properties.
     *
     * @return boolean true or false if saving went okey.
     */
    public function save($values = [])
    {

        $this->setProperties($values);
        $values = $this->getProperties();

        if (isset($values['id'])) {
            return $this->updateUser($values);
        } else {
            return $this->create($values);
        }
    }

    /**
     * Create new row.
     *
     * @param array $values key/values to save.
     *
     * @return boolean true or false if saving went okey.
     */
    public function create($values)
    {
        $this->setProperties($values);
        $values = $this->getProperties();

        $keys   = array_keys($values);
        $values = array_values($values);

        $this->db->insert(
            $this->getSource(),
            $keys
        );

        $res = $this->db->execute($values);

        return $this->id = $this->db->lastInsertId();

    }

    /**
     * Update row.
     *
     * @param array $values key/values to save.
     *
     * @return boolean true or false if saving went okey.
     */
    public function updateUser($values)
    {
        $this->setProperties($values);
        $values = $this->getProperties();

        $keys   = array_keys($values);
        $values = array_values($values);


        // Its update, remove id and use as where-clause
        unset($keys['id']);
        $values[] = $this->id;


        $this->db->update(
            $this->getSource(),
            $keys,
            "id = ?"
        );

        return $this->db->execute($values);
    }

    public function delete($id)
    {
        $this->db->delete(
            $this->getSource(),
            'id = ?'
        );

        return $this->db->execute([$id]);
    }

    public function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }

        return $url;
    }

}